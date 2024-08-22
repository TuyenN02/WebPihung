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
    // Lấy từ khóa và xóa dấu cách thừa
    $keyword = trim($_POST['keyword']);
    $keyword = preg_replace('/\s+/', ' ', $keyword); // Thay thế nhiều dấu cách liên tiếp bằng một dấu cách

    // Thực hiện truy vấn tìm kiếm
    $search_sql = " AND sanpham.TenSanPham LIKE '%$keyword%'";
} else {
    $search_sql = '';
}

// Xử lý sắp xếp
if (isset($_POST['sortOrder'])) {
    switch ($_POST['sortOrder']) {
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
}

// Câu truy vấn lấy sản phẩm dựa trên sắp xếp và phân trang
$sql_product = "SELECT * FROM sanpham WHERE 1 $search_sql ORDER BY $order_by LIMIT $begin, $records_per_page";
$query_product = mysqli_query($mysqli, $sql_product);

// Số lượng sản phẩm
$total_products_query = mysqli_query($mysqli, "SELECT COUNT(*) AS total FROM sanpham WHERE 1 $search_sql");
$total_products = mysqli_fetch_assoc($total_products_query)['total'];
$total_pages = ceil($total_products / $records_per_page);
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
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block" name="search">Tìm kiếm</button>
            </div>
            <div class="form-group col-md-3">
                <label for="sortOrder">Sắp xếp theo:</label>
                <select id="sortOrder" name="sortOrder" class="form-control" onchange="this.form.submit()">
                    <option value="desc" <?php echo ($_POST['sortOrder'] ?? '') == 'desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                    <option value="asc" <?php echo ($_POST['sortOrder'] ?? '') == 'asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                    <option value="hot" <?php echo ($_POST['sortOrder'] ?? '') == 'hot' ? 'selected' : ''; ?>>Bán chạy nhất</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Hiển thị sản phẩm -->
    <div class="row">
        <?php if (mysqli_num_rows($query_product) > 0) { ?>
            <?php while ($row = mysqli_fetch_assoc($query_product)) { ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <!-- Liên kết đến chi tiết sản phẩm khi nhấn vào ảnh -->
                        <a href="./index.php?navigate=productInfo&id_product=<?php echo $row['ID_SanPham']; ?>">
                            <img class="card-img-top" src="<?php echo './assets/image/product/' . $row['Img']; ?>" alt="Product Image">
                        </a>
                        <div class="card-body">
                            <!-- Liên kết đến chi tiết sản phẩm khi nhấn vào tên -->
                            <h5 class="card-title">
                                <a href="./index.php?navigate=productInfo&id_product=<?php echo $row['ID_SanPham']; ?>">
                                    <?php echo htmlspecialchars($row['TenSanPham']); ?>
                                </a>
                            </h5>
                            <!-- Liên kết đến chi tiết sản phẩm khi nhấn vào giá -->
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
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1) { ?>
                <li class="page-item"><a class="page-link" href="?trang=<?php echo $page - 1; ?>">Trang trước</a></li>
            <?php } ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?trang=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
            <?php if ($page < $total_pages) { ?>
                <li class="page-item"><a class="page-link" href="?trang=<?php echo $page + 1; ?>">Trang kế tiếp</a></li>
            <?php } ?>
        </ul>
    </nav>
</div>
</body>
</html>
