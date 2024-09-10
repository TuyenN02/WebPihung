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

            <form id="editForm" action="pages/main/account/change.php?id=<?= $ID_ThanhVien ?>" method="POST">
                
                <!-- Họ và tên -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                        <input id="HoVaTen" name="HoVaTen" class="form-control fixed-height" value="<?php echo htmlspecialchars($HoVaTen); ?>">
                    </div>
                    <div class="text-danger" id="HoVaTenError"></div>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
                        <input id="Email" name="Email" class="form-control fixed-height" value="<?php echo htmlspecialchars($Email); ?>">
                    </div>
                    <div class="text-danger" id="EmailError"></div>
                </div>
                
                <!-- Số điện thoại -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"> <i class="fa fa-phone"></i> </span>
                        <input id="SoDienThoai" name="SoDienThoai" class="form-control fixed-height" value="<?php echo htmlspecialchars($SoDienThoai); ?>">
                    </div>
                    <div class="text-danger" id="SoDienThoaiError"></div>
                </div>
                
                <!-- Địa chỉ -->
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"> <i class="fa fa-building"></i> </span>
                        <input id="DiaChi" name="DiaChi" class="form-control fixed-height" type="text" value="<?php echo htmlspecialchars($DiaChi); ?>">
                    </div>
                    <div class="text-danger" id="DiaChiError"></div>
                </div>
                
                <div class="form-group">
                    <input type="submit" class="btn btn-primary btn-block" name="sua" value="Cập nhật">
                    <button type="button" class="btn btn-secondary btn-block" onclick="window.history.back();">Hủy</button>
                </div>
            </form>
        </article>
    </div>
</div>

<script>
document.getElementById('editForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn form gửi ngay lập tức

    // Xóa thông báo lỗi cũ
    document.getElementById('HoVaTenError').textContent = '';
    document.getElementById('EmailError').textContent = '';
    document.getElementById('SoDienThoaiError').textContent = '';
    document.getElementById('DiaChiError').textContent = '';

    // Lấy giá trị từ form
    var HoVaTen = document.getElementById('HoVaTen').value.trim();
    var Email = document.getElementById('Email').value.trim();
    var SoDienThoai = document.getElementById('SoDienThoai').value.trim();
    var DiaChi = document.getElementById('DiaChi').value.trim();
    var ID_ThanhVien = "<?php echo $ID_ThanhVien; ?>";  // Đảm bảo PHP có thể xuất biến đúng

    var hasError = false;

   // Kiểm tra Họ và Tên
   if (HoVaTen === '') {
        document.getElementById('HoVaTenError').textContent = 'Vui lòng nhập họ và tên!';
        hasError = true;
    } else if (HoVaTen.length < 2 || HoVaTen.length > 50) {
        document.getElementById('HoVaTenError').textContent = 'Họ và tên phải có từ 2 đến 50 ký tự!';
        hasError = true;
    }

    // Kiểm tra Email
    var emailPattern = /^[^\s@]{4,}@([^\s@]+\.)+[^\s@]+$/;
    if (Email === '') {
        document.getElementById('EmailError').textContent = 'Vui lòng nhập email!';
        hasError = true;
    } else if (!emailPattern.test(Email)) {
        document.getElementById('EmailError').textContent = 'Email không đúng định dạng!';
        hasError = true;
    } else if (Email.length > 255) {
        document.getElementById('EmailError').textContent = 'Email không được vượt quá 255 ký tự!';
        hasError = true;
    }

    // Kiểm tra Số điện thoại
    var phonePattern = /^0[0-9]{9}$/;
    if (SoDienThoai === '') {
        document.getElementById('SoDienThoaiError').textContent = 'Vui lòng nhập số điện thoại!';
        hasError = true;
    } else if (!phonePattern.test(SoDienThoai)) {
        document.getElementById('SoDienThoaiError').textContent = 'Số điện thoại phải có 10 số và bắt đầu bằng số 0!';
        hasError = true;
    }

    // Kiểm tra Địa chỉ
    if (DiaChi === '') {
        document.getElementById('DiaChiError').textContent = 'Vui lòng nhập địa chỉ!';
        hasError = true;
    } else if (DiaChi.length < 10 || DiaChi.length > 100) {
        document.getElementById('DiaChiError').textContent = 'Địa chỉ phải có từ 10 đến 100 ký tự!';
        hasError = true;
    }

    // Ngăn chặn việc gửi form nếu có lỗi
    if (hasError) {
        return;
    }

    // Kiểm tra trùng lặp Email và Số điện thoại qua AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'pages/main/account/change.php?id=' + ID_ThanhVien, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);

            if (response.status === 'error') {
                // Hiển thị lỗi từ phía server
                if (response.message.includes('Email')) {
                    document.getElementById('EmailError').textContent = 'Email đã tồn tại!';
                }
                if (response.message.includes('Số điện thoại')) {
                    document.getElementById('SoDienThoaiError').textContent = 'Số điện thoại đã tồn tại!';
                }
            } else if (response.status === 'success') {
                // Sử dụng sessionStorage để lưu thông báo thành công
                sessionStorage.setItem('message', response.message);
                sessionStorage.setItem('message_type', 'success');

                // Chuyển hướng đến trang profile.php
                window.location.href = 'index.php?navigate=profile';
            }
        }
    };

    xhr.send('HoVaTen=' + encodeURIComponent(HoVaTen) + 
             '&Email=' + encodeURIComponent(Email) + 
             '&SoDienThoai=' + encodeURIComponent(SoDienThoai) + 
             '&DiaChi=' + encodeURIComponent(DiaChi));
});

</script>