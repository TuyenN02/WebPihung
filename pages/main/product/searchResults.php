<?php

// Thiết lập trang mặc định là trang 1 nếu không có trang nào được chọn
$page = isset($_GET['trang']) ? (int)$_GET['trang'] : 1;
$records_per_page = 8;
$begin = ($page > 1) ? ($page * $records_per_page) - $records_per_page : 0;

// Mặc định sắp xếp theo ID_SanPham giảm dần
$order_by = 'ID_SanPham DESC';
$keyword = '';

// Xử lý tìm kiếm
if (isset($_POST['search'])) {
    $keyword = trim($_POST['keyword']);
    $keyword = preg_replace('/\s+/', ' ', $keyword);

    $search_sql = " AND sanpham.TenSanPham LIKE '%$keyword%'";
    $order_by = isset($_POST['sortOrder']) ? $_POST['sortOrder'] : 'ID_SanPham DESC';
} else {
    $search_sql = isset($_GET['keyword']) ? " AND sanpham.TenSanPham LIKE '%" . mysqli_real_escape_string($mysqli, $_GET['keyword']) . "%'" : '';
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
    $order_by = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ID_SanPham DESC';
}

// Xử lý sắp xếp
switch ($order_by) {
    case 'desc':
        $order_by = 'GiaBan DESC';
        break;
    case 'asc':
        $order_by = 'GiaBan ASC';
        break;
    case 'hot':
        $order_by = '(SELECT SUM(SoLuong) FROM chitietdonhang WHERE chitietdonhang.ID_SanPham = sanpham.ID_SanPham) DESC';
        break;
    default:
        $order_by = 'ID_SanPham DESC';
        break;
}

// Đếm tổng số sản phẩm khớp với tìm kiếm
$total_products_query = mysqli_query($mysqli, "SELECT COUNT(*) AS total FROM sanpham WHERE 1 $search_sql");
$total_products = mysqli_fetch_assoc($total_products_query)['total'];
$total_pages = ceil($total_products / $records_per_page);

// Kiểm tra nếu trang hiện tại vượt quá số trang hợp lệ
if ($page > $total_pages && $total_pages > 0) {
    header("Location: ".$_SERVER['PHP_SELF']."?trang=".$total_pages."&keyword=".urlencode($keyword)."&sortOrder=".urlencode($order_by));
    exit;
}

// Câu truy vấn lấy sản phẩm dựa trên sắp xếp và phân trang
$sql_product = "SELECT * FROM sanpham WHERE 1 $search_sql ORDER BY $order_by LIMIT $begin, $records_per_page";
$query_product = mysqli_query($mysqli, $sql_product);

$num = ($page - 1) * $records_per_page; // Thiết lập số thứ tự bắt đầu

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Kết quả tìm kiếm sản phẩm</title>
    <style>
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card-title a, .card-text a {
            color: inherit;
            text-decoration: none;
        }
        .card-title a:hover, .card-text a:hover {
            text-decoration: underline;
        }
        .search-form {
            max-width: 800px;
            margin: 0 auto;
        }
        .search-form .form-control {
            border-radius: 20px;
            margin-bottom: 20px;
        }
        .search-form .btn {
            border-radius: 20px;
            margin-top: -3px;
            padding: 8px;
            background-color: #007bff;
            border-color: #007bff;
        }
        .search-form .btn:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .pagination {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
<!-- Form tìm kiếm và sắp xếp -->
<form method="POST" action="" class="search-form">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="keyword">Từ khóa tìm kiếm:</label>
            <input type="text" class="form-control" id="keyword" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
        </div>
       
        <div class="form-group col-md-3">
            <label for="sortOrder">Sắp xếp theo:</label>
            <select id="sortOrder" name="sortOrder" class="form-control">
                <option value="desc" <?php echo ($order_by == 'GiaBan DESC') ? 'selected' : ''; ?>>Giá giảm dần</option>
                <option value="asc" <?php echo ($order_by == 'GiaBan ASC') ? 'selected' : ''; ?>>Giá tăng dần</option>
                <option value="hot" <?php echo ($order_by == '(SELECT SUM(SoLuong) FROM chitietdonhang WHERE chitietdonhang.ID_SanPham = sanpham.ID_SanPham) DESC') ? 'selected' : ''; ?>>Bán chạy nhất</option>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary btn-block" name="search">Tìm kiếm</button>
        </div>
    </div>
</form>

    <!-- Hiển thị sản phẩm -->
<div class="row">
    <?php if (mysqli_num_rows($query_product) > 0) { ?>
        <?php while ($row = mysqli_fetch_assoc($query_product)) { ?>
            <?php $num++; // Tăng số thứ tự cho mỗi sản phẩm ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100">
                    <!-- Liên kết đến chi tiết sản phẩm khi nhấn vào ảnh -->
                    <a href="./index.php?navigate=productInfo&id_product=<?php echo $row['ID_SanPham']; ?>">
                        <img class="card-img-top" src="<?php echo './assets/image/product/' . $row['Img']; ?>" alt="Product Image">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">
                            <!-- Hiển thị số thứ tự sản phẩm -->
                            <strong>Số thứ tự: <?php echo $num; ?></strong><br>
                            <a href="./index.php?navigate=productInfo&id_product=<?php echo $row['ID_SanPham']; ?>">
                                <?php echo htmlspecialchars($row['TenSanPham']); ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <a href="./index.php?navigate=productInfo&id_product=<?php echo $row['ID_SanPham']; ?>">
                                <strong>Giá:</strong> <?php echo number_format($row['GiaBan']) . ' VND'; ?>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-md-12">
            <p class="text-center">Không tìm thấy kết quả phù hợp.</p>
        </div>
    <?php } ?>
</div>
    <!-- Phân trang -->
<!-- Phân trang -->
<nav>
    <ul class="pagination justify-content-center">
        <?php
        // Lấy URL gốc không bao gồm query string
        $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
        $queryParameters = $_GET;
        unset($queryParameters['trang']); // Loại bỏ tham số 'trang' để thêm lại sau

        // Giữ lại các giá trị từ khóa và trạng thái trong URL (nếu có)
        if (!empty($keyword)) {
            $queryParameters['keyword'] = urlencode($keyword);
        }
        if (!empty($sortOrder)) {
            $queryParameters['sortOrder'] = urlencode($sortOrder);
        }

        // Link đến trang trước
        if ($page > 1):
            $queryParameters['trang'] = $page - 1; // Chuyển sang trang trước
            $prevPageUrl = $baseUrl . '?' . http_build_query($queryParameters); ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $prevPageUrl; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Link đến từng số trang -->
        <?php for ($i = 1; $i <= $total_pages; $i++):
            $queryParameters['trang'] = $i; // Thêm số trang vào query string
            $pageUrl = $baseUrl . '?' . http_build_query($queryParameters); ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="<?php echo $pageUrl; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Link đến trang tiếp theo -->
        <?php if ($page < $total_pages):
            $queryParameters['trang'] = $page + 1; // Chuyển sang trang tiếp theo
            $nextPageUrl = $baseUrl . '?' . http_build_query($queryParameters); ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $nextPageUrl; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

</div>
</body>
</html>
