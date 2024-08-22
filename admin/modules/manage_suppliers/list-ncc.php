<?php 
include("config/connection.php"); // Kết nối đến cơ sở dữ liệu

// Kiểm tra thông báo thành công
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']); // Xóa thông báo sau khi đã hiển thị
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

// Truy vấn nhà cung cấp
$sql_NCC = "SELECT * FROM nhacungcap ORDER BY ID_NCC DESC LIMIT $begin, 8";
if (isset($_POST['tukhoa'])) {
    $tukhoa = mysqli_real_escape_string($mysqli, trim($_POST['tukhoa']));
    $sql_NCC = "SELECT * FROM nhacungcap WHERE TenNCC LIKE '%$tukhoa%' ORDER BY ID_NCC DESC LIMIT $begin, 8";
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
            <h5 class="m-0">Danh sách nhà cung cấp</h5>
            <div class="form-search form-inline">
                <form action="" method="POST" class="d-flex">
                    <!-- Giữ lại từ khóa tìm kiếm đã nhập trong ô tìm kiếm -->
                    <input type="text" class="form-control form-search" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                    <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                </form>
            </div>
        </div>
        
        <div class="card-body">
            <?php if ($total_records == 0): ?>
                <p class="text-center">Không tìm thấy nhà cung cấp nào.</p>
            <?php else: ?>
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
