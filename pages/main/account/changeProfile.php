<?php


$ID_ThanhVien = $_SESSION['ID_ThanhVien'];
$sql_Cus = "SELECT * FROM thanhvien WHERE ID_ThanhVien = $ID_ThanhVien";
$query_Cus = mysqli_query($mysqli, $sql_Cus);
$row = mysqli_fetch_array($query_Cus);
$HoVaTen = $row['HoVaTen'];
$Email = $row['Email'];
$SoDienThoai = $row['SoDienThoai'];
$DiaChi = $row['DiaChi'];

// Lấy lỗi và thông báo từ session
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : array();
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';

// Xóa lỗi và thông báo sau khi hiển thị
unset($_SESSION['errors']);
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<div class="container mt-60">
    <div class="card bg-light">
        <article class="card-body mx-auto">
            <h4 class="card-title mt-3 text-center">Sửa thông tin cá nhân</h4>

            <form action="pages/main/account/change.php?id=<?= $ID_ThanhVien ?>" method="POST">
                
                <!-- Họ và tên -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                        <input name="HoVaTen" class="form-control fixed-height" value="<?php echo htmlspecialchars($HoVaTen); ?>">
                    </div>
                    <?php if (isset($errors['HoVaTen'])): ?>
                        <div class="text-danger"><?php echo htmlspecialchars($errors['HoVaTen']); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                        <input name="Email" class="form-control fixed-height" value="<?php echo htmlspecialchars($Email); ?>">
                    </div>
                    <?php if (isset($errors['Email'])): ?>
                        <div class="text-danger"><?php echo htmlspecialchars($errors['Email']); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Số điện thoại -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
                        <input name="SoDienThoai" class="form-control fixed-height" value="<?php echo htmlspecialchars($SoDienThoai); ?>">
                    </div>
                    <?php if (isset($errors['SoDienThoai'])): ?>
                        <div class="text-danger"><?php echo htmlspecialchars($errors['SoDienThoai']); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Địa chỉ -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"> <i class="fa fa-building"></i> </span>
                        <input name="DiaChi" class="form-control fixed-height" type="text" value="<?php echo htmlspecialchars($DiaChi); ?>">
                    </div>
                    <?php if (isset($errors['DiaChi'])): ?>
                        <div class="text-danger"><?php echo htmlspecialchars($errors['DiaChi']); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Thông báo chung -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($message_type); ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary btn-block" name="sua" value="Sửa">
                </div>
            </form>
        </article>
    </div>
</div>
