<?php
// Kết nối đến cơ sở dữ liệu

$errors = []; // Mảng lưu thông báo lỗi

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = trim(mysqli_real_escape_string($mysqli, $_POST['address']));
    $work_hours = trim(mysqli_real_escape_string($mysqli, $_POST['work_hours']));
    $break_hours = trim(mysqli_real_escape_string($mysqli, $_POST['break_hours']));
    $phone = trim(mysqli_real_escape_string($mysqli, $_POST['phone']));
    $email = trim(mysqli_real_escape_string($mysqli, $_POST['email']));

    // Kiểm tra số điện thoại không để trống
    if (empty($phone)) {
        $errors['phone'] = 'Số điện thoại không được để trống.';
    } elseif (!preg_match('/^0\d{9}$/', $phone)) {
        $errors['phone'] = 'Vui lòng nhập đúng định dạng.';
    }

    // Kiểm tra email
    if (empty($email)) {
        $errors['email'] = 'Email chưa đúng định dạng!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email chưa đúng định dạng!';
    } else {
        // Phân tách email thành phần trước và sau dấu @
        list($local_part, $domain_part) = explode('@', $email);

        // Kiểm tra phần miền domain không có ký tự đặc biệt
        if (!preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $domain_part)) {
            $errors['email'] = 'Miền email không hợp lệ.';
        } elseif (strlen($local_part) < 4) {
            $errors['email'] = 'Email chưa đúng định dạng!';
        }
    }

    // Kiểm tra các trường không để trống
    if (empty($address)) {
        $errors['address'] = 'Địa chỉ không được để trống.';
    }
    if (empty($work_hours)) {
        $errors['work_hours'] = 'Giờ làm việc không được để trống.';
    }
    if (empty($break_hours)) {
        $errors['break_hours'] = 'Giờ nghỉ không được để trống.';
    }

    // Nếu không có lỗi, thực hiện cập nhật
    if (empty($errors)) {
        $update_query = "UPDATE thongtin SET DiaChi='$address', gio_lam_viec='$work_hours', gio_nghi='$break_hours', SDT='$phone', Email='$email' WHERE ID=1";
        if (mysqli_query($mysqli, $update_query)) {
            $success_message = 'Cập nhật thông tin thành công!';
        } else {
            $errors['update'] = 'Lỗi cập nhật thông tin: ' . mysqli_error($mysqli);
        }
    }
}

// Lấy thông tin hiện tại
$result = mysqli_query($mysqli, "SELECT DiaChi, gio_lam_viec, gio_nghi, SDT, Email FROM thongtin WHERE ID=1");
$current_info = mysqli_fetch_assoc($result);
?>

<div class="container mt-5">
<?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($errors['update'])): ?>
                <div class="alert alert-danger"><?php echo $errors['update']; ?></div>
            <?php endif; ?>

    <div class="bg-white p-4 rounded shadow-sm"> <!-- Thêm lớp để nền trắng và có khoảng cách nội dung -->
    <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 35px;">Quản lý thông tin</h5>
        <form action="" method="POST">
          
            <div class="form-group mb-3">
                <label for="phone">Số điện thoại:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($current_info['SDT']); ?>" required>
                <?php if (isset($errors['phone'])): ?>
                    <div class="text-danger"><?php echo $errors['phone']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group mb-3">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($current_info['Email']); ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="text-danger"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group mb-3">
                <label for="address">Địa chỉ:</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($current_info['DiaChi']); ?>" required>
                <?php if (isset($errors['address'])): ?>
                    <div class="text-danger"><?php echo $errors['address']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group mb-3">
                <label for="work_hours">Giờ làm việc:</label>
                <input type="time" class="form-control" id="work_hours" name="work_hours" value="<?php echo htmlspecialchars($current_info['gio_lam_viec']); ?>" required>
                <?php if (isset($errors['work_hours'])): ?>
                    <div class="text-danger"><?php echo $errors['work_hours']; ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group mb-3">
                <label for="break_hours">Giờ nghỉ:</label>
                <input type="time" class="form-control" id="break_hours" name="break_hours" value="<?php echo htmlspecialchars($current_info['gio_nghi']); ?>" required>
                <?php if (isset($errors['break_hours'])): ?>
                    <div class="text-danger"><?php echo $errors['break_hours']; ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = 0;
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 2000);
        });
    });
</script>
<style>
.alert {
    position: fixed;
    top: 50px;
    right: 970px;
    padding: 15px;
    border-radius: 5px;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.alert-success {
    background-color: #d4edda;
    color: #ff0000;
    border: 3px solid #ff0000;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>