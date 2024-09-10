<?php


$ID_ThanhVien = $_SESSION['ID_ThanhVien'];
$sql_Cus = "SELECT * FROM thanhvien WHERE ID_ThanhVien = $ID_ThanhVien";
$query_Cus = mysqli_query($mysqli, $sql_Cus);
$row = mysqli_fetch_array($query_Cus);

// Lấy thông báo từ session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Xóa session sau khi hiển thị
}

// Xóa thông báo sau khi hiển thị
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>
<div class="container">
    <div class="card bg-light mt-5">
        <article class="card-body mx-auto" style="width: 400px;">
            <h4 class="card-title mt-3 text-center">Thông tin cá nhân</h4>
            <?php if (!empty($success_message)): ?>
        <div class="alert" id="successAlert"><?php echo $success_message; ?></div>
    <?php endif; ?>
            <!-- Hiển thị thông báo nếu có -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-user-circle"></i> </span>
                    </div>
                    <input readonly name="HoVaTen" class="form-control" value="<?php echo htmlspecialchars($row['HoVaTen']); ?>">
                </div>
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                    </div>
                    <input readonly name="Email" class="form-control" value="<?php echo htmlspecialchars($row['Email']); ?>">
                </div>
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
                    </div>
                    <input readonly name="" class="form-control" value="<?php echo htmlspecialchars($row['SoDienThoai']); ?>">
                </div>
                <div class="form-group input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"> <i class="fa fa fa-home"></i> </span>
                    </div>
                    <input readonly name="" class="form-control" type="text" value="<?php echo htmlspecialchars($row['DiaChi']); ?>">
                </div>
                <br>
                <p class="text-center">
                    <a class="btn btn-outline-primary" href="index.php?navigate=changePassword">Đổi mật khẩu</a>
                </p>
                <p class="text-center">
                    <a class="btn btn-outline-primary" href="index.php?navigate=changeProfile">Sửa thông tin</a>
                </p>
            </form>
        </article>
    </div>
</div>
<script>
        // Hiển thị và ẩn thông báo sau 3 giây
        window.onload = function() {
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                successAlert.style.display = 'block'; // Hiển thị thông báo
                setTimeout(function() {
                    successAlert.style.display = 'none'; // Ẩn sau 3 giây
                }, 2000);
            }
        };
 
    </script>

<style>

#successAlert {
    background-color: #d4edda; /* Nền màu xanh lá nhạt */
    color: #155724; /* Chữ màu xanh lá đậm */
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #c3e6cb; /* Đường viền màu xanh nhạt */
    margin-bottom: 15px;
    font-weight: bold;
}
/* CSS cho thông báo thành công */
.alert-success {
    background-color: #d4edda; /* Nền màu xanh lá nhạt */
    color: #155724; /* Chữ màu xanh lá đậm */
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #c3e6cb; /* Đường viền màu xanh lá nhạt */
    margin-bottom: 15px;
    font-weight: bold;
}

/* CSS cho thông báo lỗi (ví dụ) */
.alert-error {
    background-color: #f8d7da; /* Nền màu đỏ nhạt */
    color: #721c24; /* Chữ màu đỏ đậm */
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #f5c6cb; /* Đường viền màu đỏ nhạt */
    margin-bottom: 15px;
    font-weight: bold;
}

/* CSS cho các loại thông báo khác (ví dụ) */
.alert-warning {
    background-color: #fff3cd; /* Nền màu vàng nhạt */
    color: #856404; /* Chữ màu vàng đậm */
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ffeeba; /* Đường viền màu vàng nhạt */
    margin-bottom: 15px;
    font-weight: bold;
}
</style>