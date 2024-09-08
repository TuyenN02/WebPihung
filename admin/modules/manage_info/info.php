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
    const form = document.getElementById('infoForm');

    form.addEventListener('submit', function(event) {
        // Xóa thông báo lỗi trước đó
        document.getElementById('phoneError').innerHTML = '';
        document.getElementById('emailError').innerHTML = '';
        document.getElementById('addressError').innerHTML = '';
        document.getElementById('workHoursError').innerHTML = '';
        document.getElementById('breakHoursError').innerHTML = '';

        let isValid = true;

        // Kiểm tra số điện thoại
        const phone = document.getElementById('phone').value.trim();
        const phoneRegex = /^0\d{9}$/;
        if (!phoneRegex.test(phone)) {
            document.getElementById('phoneError').innerHTML = 'Số điện thoại phải có 10 chữ số, bắt đầu bằng 0!';
            isValid = false;
        }

        // Kiểm tra email
        const email = document.getElementById('email').value.trim();
        const emailParts = email.split('@');
        if (emailParts.length !== 2) {
            document.getElementById('emailError').innerHTML = 'Email chưa đúng định dạng!';
            isValid = false;
        } else {
            const localPart = emailParts[0];
            const domainPart = emailParts[1];
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const domainRegex = /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            const localPartRegex = /^[a-zA-Z0-9]+$/; // Chỉ cho phép ký tự chữ và số

            // Kiểm tra local part
            if (localPart.length < 4) {
                document.getElementById('emailError').innerHTML = 'Email chưa đúng định dạng!';
                isValid = false;
            } else if (!localPartRegex.test(localPart)) {
                document.getElementById('emailError').innerHTML = 'Email chưa đúng định dạng!';
                isValid = false;
            } else if (!emailRegex.test(email)) {
                document.getElementById('emailError').innerHTML = 'Email chưa đúng định dạng!';
                isValid = false;
            } else if (!domainRegex.test(domainPart)) {
                document.getElementById('emailError').innerHTML = 'Miền email chưa đúng định dạng!';
                isValid = false;
            }
        }

        // Kiểm tra địa chỉ
        const address = document.getElementById('address').value.trim();
        if (address.length < 10) {
            document.getElementById('addressError').innerHTML = 'Địa chỉ phải có ít nhất 10 ký tự!';
            isValid = false;
        }

        // Kiểm tra giờ làm việc và giờ nghỉ
        const workHours = document.getElementById('work_hours').value.trim();
        const breakHours = document.getElementById('break_hours').value.trim();

        if (workHours === '') {
            document.getElementById('workHoursError').innerHTML = 'Giờ làm việc không được để trống!';
            isValid = false;
        }

        if (breakHours === '') {
            document.getElementById('breakHoursError').innerHTML = 'Giờ nghỉ không được để trống!';
            isValid = false;
        }

        // Kiểm tra giờ làm việc phải sớm hơn giờ nghỉ theo định dạng 24 giờ
        if (workHours !== '' && breakHours !== '') {
            const workTime = workHours.split(':');
            const breakTime = breakHours.split(':');

            // Chuyển đổi sang số phút kể từ 00:00
            const workMinutes = parseInt(workTime[0]) * 60 + parseInt(workTime[1]);
            const breakMinutes = parseInt(breakTime[0]) * 60 + parseInt(breakTime[1]);

            if (workMinutes >= breakMinutes) {
                document.getElementById('workHoursError').innerHTML = 'Giờ làm phải sớm hơn giờ nghỉ!';
                isValid = false;
            }
        }

        // Nếu có lỗi, ngăn không cho form submit
        if (!isValid) {
            event.preventDefault();
        }
    });

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
