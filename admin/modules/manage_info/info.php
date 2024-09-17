<?php
// Kết nối đến cơ sở dữ liệu

$errors = []; // Mảng lưu thông báo lỗi

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = trim(mysqli_real_escape_string($mysqli, $_POST['address']));
    $work_hours = trim(mysqli_real_escape_string($mysqli, $_POST['work_hours']));
    $break_hours = trim(mysqli_real_escape_string($mysqli, $_POST['break_hours']));
    $phone = trim(mysqli_real_escape_string($mysqli, $_POST['phone']));
    $email = trim(mysqli_real_escape_string($mysqli, $_POST['email']));


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

    <div class="bg-white p-4 rounded shadow-sm"> 
        <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 35px;">Quản lý thông tin</h5>
        <form id="infoForm" action="" method="POST">
            <div class="form-group mb-3">
                <label for="phone">Số điện thoại:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($current_info['SDT']); ?>" required>
                <div id="phoneError" class="text-danger"></div>
            </div>
            <div class="form-group mb-3">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($current_info['Email']); ?>" required>
                <div id="emailError" class="text-danger"></div>
            </div>
            <div class="form-group mb-3">
                <label for="address">Địa chỉ:</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($current_info['DiaChi']); ?>" required>
                <div id="addressError" class="text-danger"></div>
            </div>
            <div class="form-group mb-3">
                <label for="work_hours">Giờ làm việc:</label>
                <input type="time" class="form-control" id="work_hours" name="work_hours" value="<?php echo htmlspecialchars($current_info['gio_lam_viec']); ?>" required>
                <div id="workHoursError" class="text-danger"></div>
            </div>
            <div class="form-group mb-3">
                <label for="break_hours">Giờ nghỉ:</label>
                <input type="time" class="form-control" id="break_hours" name="break_hours" value="<?php echo htmlspecialchars($current_info['gio_nghi']); ?>" required>
                <div id="breakHoursError" class="text-danger"></div>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="index.php?info=info" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bắt lỗi cho từng trường ngay khi nhập liệu
    document.getElementById('phone').addEventListener('input', function() {
        const phone = this.value.trim();
        const phoneRegex = /^0\d{9}$/;
        if (!phoneRegex.test(phone)) {
            document.getElementById('phoneError').textContent = 'Số điện thoại phải có 10 chữ số, bắt đầu bằng 0!';
        } else {
            document.getElementById('phoneError').textContent = '';
        }
    });

    document.getElementById('email').addEventListener('input', function() {
        const email = this.value.trim();
        const emailParts = email.split('@');
        if (emailParts.length !== 2 || emailParts[0].length < 4 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            document.getElementById('emailError').textContent = 'Email chưa đúng định dạng!';
        } else {
            document.getElementById('emailError').textContent = '';
        }
    });

    document.getElementById('address').addEventListener('input', function() {
        const address = this.value.trim();
        if (address.length < 10) {
            document.getElementById('addressError').textContent = 'Địa chỉ phải có ít nhất 10 ký tự!';
        } else {
            document.getElementById('addressError').textContent = '';
        }
    });

    document.getElementById('work_hours').addEventListener('input', function() {
        const workHours = this.value.trim();
        if (workHours === '') {
            document.getElementById('workHoursError').textContent = 'Giờ làm việc không được để trống!';
        } else {
            document.getElementById('workHoursError').textContent = '';
        }
    });

    document.getElementById('break_hours').addEventListener('input', function() {
        const breakHours = this.value.trim();
        if (breakHours === '') {
            document.getElementById('breakHoursError').textContent = 'Giờ nghỉ không được để trống!';
        } else {
            document.getElementById('breakHoursError').textContent = '';
        }
    });
    
    // Kiểm tra khi submit form
    document.getElementById('infoForm').addEventListener('submit', function(event) {
        // Kiểm tra nếu có lỗi
        const errors = [
            document.getElementById('phoneError').textContent,
            document.getElementById('emailError').textContent,
            document.getElementById('addressError').textContent,
            document.getElementById('workHoursError').textContent,
            document.getElementById('breakHoursError').textContent
        ];

        if (errors.some(error => error !== '')) {
            event.preventDefault(); // Ngăn form gửi nếu có lỗi
        }
    });
});

</script>
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