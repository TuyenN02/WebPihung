<?php

// Xử lý tìm kiếm
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $search = mysqli_real_escape_string($mysqli, $search);
    $search = preg_replace('/\s+/', ' ', $search); // Xóa dấu cách thừa
}

// Số lượng bản ghi trên mỗi trang
$records_per_page = 8;

// Xác định trang hiện tại
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính toán bắt đầu từ đâu
$offset = ($page - 1) * $records_per_page;

// Truy vấn để đếm tổng số bình luận (dựa trên tìm kiếm nếu có)
$sql_count = "
    SELECT COUNT(*) as total 
    FROM binhluan 
    JOIN sanpham ON binhluan.ID_SanPham = sanpham.ID_SanPham 
    JOIN thanhvien ON binhluan.ID_ThanhVien = thanhvien.ID_ThanhVien 
    WHERE thanhvien.HoVaTen LIKE '%$search%' 
    OR sanpham.TenSanPham LIKE '%$search%'
";
$result_count = mysqli_query($mysqli, $sql_count);
$total_rows = mysqli_fetch_assoc($result_count)['total'];

// Tính tổng số trang
$total_pages = ceil($total_rows / $records_per_page);

// Truy vấn dữ liệu với giới hạn phân trang
$sql_comment = "
    SELECT binhluan.*, sanpham.TenSanPham, thanhvien.HoVaTen 
    FROM binhluan 
    JOIN sanpham ON binhluan.ID_SanPham = sanpham.ID_SanPham 
    JOIN thanhvien ON binhluan.ID_ThanhVien = thanhvien.ID_ThanhVien 
    WHERE thanhvien.HoVaTen LIKE '%$search%' 
    OR sanpham.TenSanPham LIKE '%$search%'
    ORDER BY binhluan.ThoiGianBinhLuan DESC
    LIMIT $offset, $records_per_page
";
$query_comment = mysqli_query($mysqli, $sql_comment);

// Kiểm tra số lượng bản ghi
$num_rows = mysqli_num_rows($query_comment);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Danh sách bình luận</h5>
            <form action="" method="GET" class="form-inline">
                <input type="hidden" name="comment" value="comments">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Nhập từ khóa" 
                    value="<?= htmlspecialchars($search); ?>"
                >
                <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
            </form>
        </div>
        <div class="card-body">
            <?php
            // Hiển thị thông báo thành công
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success" role="alert">';
                echo htmlspecialchars($_SESSION['success_message']);
                echo '</div>';
                unset($_SESSION['success_message']);
            }

            // Hiển thị thông báo lỗi
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger" role="alert">';
                echo htmlspecialchars($_SESSION['error_message']);
                echo '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <table class="table table-striped table-checkall">
                <thead>
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">Tên khách hàng</th>
                        <th scope="col">Tên sản phẩm</th>
                        <th scope="col">Nội dung</th>
                        <th scope="col">Thời gian</th>
                        <th scope="col">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num_rows > 0) { 
                        $i = $offset + 1; // Để hiển thị số thứ tự đúng
                        while ($row_comment = mysqli_fetch_array($query_comment)) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $i++; ?></th>
                        <td><?php echo htmlspecialchars($row_comment['HoVaTen']); ?></td>
                        <td><?php echo htmlspecialchars($row_comment['TenSanPham']); ?></td>
                        <td><?php echo htmlspecialchars($row_comment['NoiDung']); ?></td>
                        <td><?php echo htmlspecialchars($row_comment['ThoiGianBinhLuan']); ?></td>
                        <td>
                            <a href="modules/manage_comment/delete-comment.php?id=<?php echo $row_comment['ID_BinhLuan']; ?>" 
                               class="btn btn-danger btn-sm rounded text-white" 
                               type="button" 
                               data-toggle="tooltip" 
                               data-placement="top" 
                               title="Delete"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } 
                    } else { ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            Danh sách bình luận trống!
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Phân trang -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php
            // Tạo lại URL gốc không bao gồm query string
            $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
            $queryParameters = $_GET;
            unset($queryParameters['page']); // Loại bỏ 'page' để thêm lại sau

            // Giữ lại từ khóa tìm kiếm (nếu có)
            if (!empty($search)) {
                $queryParameters['search'] = urlencode($search);
            }

            // Nút Previous
            if ($page > 1): 
                $queryParameters['page'] = $page - 1;
                $prevPageUrl = $baseUrl . '?' . http_build_query($queryParameters); ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $prevPageUrl; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Hiển thị các nút trang -->
            <?php for ($i = 1; $i <= $total_pages; $i++):
                $queryParameters['page'] = $i;
                $pageUrl = $baseUrl . '?' . http_build_query($queryParameters); ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="<?php echo $pageUrl; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <!-- Nút Next -->
            <?php if ($page < $total_pages):
                $queryParameters['page'] = $page + 1;
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
<script>
// Tự động ẩn thông báo sau vài giây
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = 0;
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 2000);
    });

</script>
<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 60px;
}
/* CSS cho bảng */
.table {
    font-size: 16px;
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    border: 1px solid rgba(0, 0, 0, 0.2);
    padding: 8px;
    text-align: center;
    vertical-align: middle;
}

.table thead {
    background-color: #f2f2f2;
}

.table-checkall {
    width: 100%;
}

.alert {
    position: fixed;
    top: 50px;
    right: 130px;
    padding: 15px;
    border-radius: 5px;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.alert-success {
    background-color: #d4edda;
    color: #269963;
    border: 3px solid #269963;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
