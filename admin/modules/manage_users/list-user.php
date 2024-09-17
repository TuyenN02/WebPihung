<?php 
include("config/connection.php");

// Xử lý phân trang
if (isset($_GET['trang'])) {
    $page = $_GET['trang'];
} else {
    $page = 1;
}
$begin = ($page == '' || $page == 1) ? 0 : ($page * 8) - 8;

// Xử lý tìm kiếm
$tukhoa = '';
if (isset($_POST['tukhoa'])) {
    $tukhoa = mysqli_real_escape_string($mysqli, trim($_POST['tukhoa']));
    $tukhoa = str_replace(' ', '%', $tukhoa); // Xóa dấu cách và thay thế bằng dấu phần trăm
}

// Truy vấn tài khoản
$sql_Customer = "SELECT * FROM thanhvien";
if ($tukhoa != '') {
    $sql_Customer .= " WHERE HoVaTen LIKE '%$tukhoa%'";
}
$sql_Customer .= " ORDER BY ID_ThanhVien DESC LIMIT $begin, 8";
$query_Customer = mysqli_query($mysqli, $sql_Customer);
?>

<?php
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Danh sách tài khoản</h5>
            <div class="form-search form-inline">
                <form action="" method="POST" class="d-flex">
                    <input type="text" class="form-control form-search" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                    <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                </form>
            </div>
        </div>
        <div class="card-body">
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <table class="table table-striped table-checkall">
                <thead>
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">Họ tên</th>
                        <th scope="col">Địa chỉ</th>
                        <th scope="col">SĐT</th>
                        <th scope="col">Ngày đăng ký</th>
                     
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($query_Customer) > 0): 
                        $i = $begin;
                        while ($row_Customer = mysqli_fetch_array($query_Customer)) {
                            $i++;
                    ?>
                        <tr>
                            <th scope="row"><?php echo $i; ?></th>
                            <td><?php echo htmlspecialchars($row_Customer['HoVaTen']); ?></td>
                            <td><?php echo htmlspecialchars($row_Customer['DiaChi']); ?></td>
                            <td><?php echo htmlspecialchars($row_Customer['SoDienThoai']); ?></td>
                            <td><?php echo htmlspecialchars($row_Customer['NgayDangKi']); ?></td>
                           
                        </tr>
                    <?php } 
                    else: ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                Danh sách tài khoản trống!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa tài khoản này?")) {
            window.location.href = 'modules/manage_users/delete-user.php?id=' + id;
        }
    }
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
    border-collapse: collapse; /* Đảm bảo các ô không có khoảng cách giữa các viền */
}

.table th, .table td {
    border: 1px solid rgba(0, 0, 0, 0.2); /* Viền mờ cho các ô */
    padding: 8px; /* Thêm khoảng đệm cho nội dung bên trong các ô */
    text-align: center; /* Căn giữa nội dung theo chiều ngang */
    vertical-align: middle; /* Căn giữa nội dung theo chiều dọc */
}

.table thead {
    background-color: #f2f2f2; /* Màu nền cho hàng tiêu đề */
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
