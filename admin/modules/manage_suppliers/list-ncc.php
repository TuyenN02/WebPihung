<?php 
include("config/connection.php"); // Kết nối đến cơ sở dữ liệu

// Kiểm tra thông báo thành công
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Xóa thông báo sau khi đã hiển thị
}

// Xử lý phân trang
if (isset($_GET['trang'])) {
    $page = $_GET['trang'];
} else {
    $page = 1;
}
$begin = ($page == '' || $page == 1) ? 0 : ($page * 8) - 8;

// Khởi tạo biến từ khóa tìm kiếm
$tukhoa = '';

// Truy vấn nhà cung cấp với collation để tìm kiếm bao gồm cả dấu
$sql_NCC = "SELECT * FROM nhacungcap ORDER BY ID_NCC DESC LIMIT $begin, 8";
if (isset($_POST['tukhoa'])) {
    $tukhoa = mysqli_real_escape_string($mysqli, trim($_POST['tukhoa']));
    // Sử dụng COLLATE để tìm kiếm bao gồm cả dấu
    $sql_NCC = "SELECT * FROM nhacungcap WHERE TenNCC COLLATE utf8mb4_bin LIKE '%$tukhoa%' ORDER BY ID_NCC DESC LIMIT $begin, 8";
}
$query_NCC = mysqli_query($mysqli, $sql_NCC);

// Kiểm tra số lượng bản ghi trả về
$total_records = mysqli_num_rows($query_NCC);
?>
<?php

if (isset($_SESSION['success_message'])): ?>
    <div id="success-message" class="alert alert-success" role="alert">
        <?php
        echo $_SESSION['success_message'];
        // Xóa thông báo khỏi session sau khi hiển thị
        unset($_SESSION['success_message']);
        ?>
    </div>
<?php endif; ?>

<script>
    // Tự động ẩn thông báo sau 5 giây
    setTimeout(function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 3000); // 5000 ms = 5 giây
</script>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <button class="btn btn-primary">
        <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?ncc=add-ncc">Thêm mới</a>
        </button>
        <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Danh sách nhà cung cấp</h5>
            <div class="form-search form-inline">
                <form action="" method="POST" class="d-flex">
                    <!-- Giữ lại từ khóa tìm kiếm đã nhập trong ô tìm kiếm -->
                    <input type="text" class="form-control form-search" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                    <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                </form>
            </div>
        </div>
        
        <div class="card-body">
            <?php if ($total_records > 0): ?>
                <table class="table table-striped table-checkall">
                    <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Tên nhà cung cấp</th>
                            <th scope="col">Email</th>
                            <th scope="col">SĐT</th>
                            <th scope="col">Địa chỉ</th>
                            <th scope="col">Sửa/Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = $begin;
                        while ($row = mysqli_fetch_array($query_NCC)) {
                            $i++;
                        ?>
                            <tr>
                                <td scope="row"><?php echo $i; ?></td>
                                <td><?php echo htmlspecialchars($row['TenNCC']); ?></td>
                                <td><?php echo htmlspecialchars($row['Email']); ?></td>
                                <td><?php echo htmlspecialchars($row['SoDienThoai']); ?></td>
                                <td><?php echo htmlspecialchars($row['DiaChi']); ?></td>
                                <td class="d-flex">
                                    <a href="?ncc=sua-ncc&id_NCC=<?php echo $row['ID_NCC']; ?>" class="btn btn-success btn-sm text-white mr-2" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete" onclick="confirmDelete(<?php echo $row['ID_NCC']; ?>)"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php else: ?>
                <!-- Vẫn hiển thị tiêu đề bảng khi không có kết quả -->
                <table class="table table-striped table-checkall">
                    <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Tên nhà cung cấp</th>
                            <th scope="col">Email</th>
                            <th scope="col">SĐT</th>
                            <th scope="col">Địa chỉ</th>
                            <th scope="col">Sửa/Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Các hàng sẽ không hiển thị, nhưng tiêu đề bảng vẫn được giữ lại -->
                    </tbody>
                </table>
                <p class="text-center">Không tìm thấy nhà cung cấp nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa nhà cung cấp này?")) {
            window.location.href = 'modules/manage_suppliers/delete-ncc.php?id_NCC=' + id;
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = 0;
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 3000);
        });
    });
</script>
<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 100px;
}
#success-message {
    position: fixed; /* Đặt thông báo ở vị trí cố định so với cửa sổ trình duyệt */
    bottom: 50px; /* Khoảng cách từ cạnh dưới của cửa sổ trình duyệt */
    right: 10px; /* Khoảng cách từ cạnh phải của cửa sổ trình duyệt */
    z-index: 9999; /* Đảm bảo thông báo nằm trên các phần tử khác */
    padding: 15px; /* Khoảng cách bên trong thông báo */
    background-color: #d4edda; /* Màu nền xanh nhạt */
    color: #bf0000; /* Màu chữ xanh đậm */
    border: 1px solid #ffaaaa; /* Đường viền xanh nhạt */
    border-radius: 4px; /* Bo tròn các góc */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Tạo bóng cho thông báo */
    font-size: 16px; /* Kích thước chữ */
    display: none; /* Ẩn thông báo theo mặc định */
}
/* Định dạng bảng */
.table {
    border-collapse: collapse; /* Đảm bảo các ô không bị gộp lại */
    width: 100%; /* Đặt chiều rộng bảng bằng 100% */
}

.table thead th {
    background-color: #f8f9fa; /* Màu nền cho tiêu đề bảng */
    border: 1px solid #dee2e6; /* Đường viền xung quanh tiêu đề */
    text-align: center; /* Căn giữa nội dung tiêu đề */
    padding: 8px; /* Khoảng cách bên trong các tiêu đề */
}

.table tbody td {
    border: 1px solid #dee2e6; /* Đường viền xung quanh các ô dữ liệu */
    padding: 8px; /* Khoảng cách bên trong các ô dữ liệu */
}

.table tbody tr:nth-child(even) {
    background-color: #ffff; /* Màu nền cho các hàng chẵn */
}

.table tbody tr:hover {
    background-color: #e9ecef; /* Màu nền khi hover qua hàng */
}

.table th, .table td {
    text-align: left; /* Căn trái nội dung của các ô dữ liệu */
}

.table .btn {
    padding: 5px 10px; /* Điều chỉnh kích thước nút */
    font-size: 12px; /* Kích thước chữ trong nút */
}
.alert {
    position: fixed;
    top: 50px;
    right: 940px;
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
    color: #269963;
    border: 1px solid #269963;
}
</style>
