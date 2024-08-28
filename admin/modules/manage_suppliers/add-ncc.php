<?php

$TenNCCError = isset($_SESSION['errors']['TenNCC']) ? $_SESSION['errors']['TenNCC'] : '';
$DiaChiError = isset($_SESSION['errors']['DiaChi']) ? $_SESSION['errors']['DiaChi'] : '';
$EmailError = isset($_SESSION['errors']['Email']) ? $_SESSION['errors']['Email'] : '';
$PhoneError = isset($_SESSION['errors']['SoDienThoai']) ? $_SESSION['errors']['SoDienThoai'] : '';
$MoTaError = isset($_SESSION['errors']['MoTa']) ? $_SESSION['errors']['MoTa'] : '';
$ImgError = isset($_SESSION['errors']['Img']) ? $_SESSION['errors']['Img'] : '';
$data = isset($_SESSION['data']) ? $_SESSION['data'] : [];
unset($_SESSION['errors']); // Xóa thông tin lỗi sau khi đã lấy
unset($_SESSION['data']); // Xóa dữ liệu sau khi đã lấy
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?ncc=list-ncc">Quay lại</a>
            </button>
            <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Thêm Nhà Cung Cấp</h5>
        </div>
        <div class="card-body">
            <form id="nccForm" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TenNCC">Tên Nhà Cung Cấp:</label>
                    <input class="form-control" type="text" name="TenNCC" id="TenNCC" value="<?php echo htmlspecialchars($data['TenNCC'] ?? ''); ?>">
                    <div id="TenNCCError" class="text-danger"><?php echo $errors['TenNCC'] ?? ''; ?></div>
                </div>
                <div class="form-group">
                    <label for="MoTa">Mô Tả:</label>
                    <textarea class="form-control" name="MoTa" id="MoTa" rows="4" style="width: 100%; resize: both;"><?php echo htmlspecialchars($data['MoTa'] ?? ''); ?></textarea>
                    <div id="MoTaError" class="text-danger"><?php echo $errors['MoTa'] ?? ''; ?></div>
                </div>
                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input class="form-control" type="email" name="Email" id="Email" value="<?php echo htmlspecialchars($data['Email'] ?? ''); ?>">
                    <div id="EmailError" class="text-danger"><?php echo $errors['Email'] ?? ''; ?></div>
                </div>
                <div class="form-group">
                    <label for="SoDienThoai">Số Điện Thoại:</label>
                    <input class="form-control" type="text" name="SoDienThoai" id="SoDienThoai" value="<?php echo htmlspecialchars($data['SoDienThoai'] ?? ''); ?>">
                    <div id="PhoneError" class="text-danger"><?php echo $errors['SoDienThoai'] ?? ''; ?></div>
                </div>
                <div class="form-group">
                    <label for="DiaChi">Địa Chỉ:</label>
                    <input class="form-control" type="text" name="DiaChi" id="DiaChi" value="<?php echo htmlspecialchars($data['DiaChi'] ?? ''); ?>">
                    <div id="DiaChiError" class="text-danger"><?php echo $errors['DiaChi'] ?? ''; ?></div>
                </div>
                
               
                <div class="form-group">
                    <label>Hình ảnh:</label>
                    <input class="form-control" type="file" name="Img" id="Img" accept=".jpg,.png" onchange="validateImage()">
                    <div id="ImgError" class="text-danger"><?php echo $errors['Img'] ?? ''; ?></div>
                    <img id="preview" src="<?php echo isset($data['Img']) ? '../../../assets/image/supplier/' . htmlspecialchars($data['Img']) : ''; ?>" style="max-width: 200px; margin-top: 10px;">
                </div>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Thêm mới</button>
                <a href="index.php?ncc=list-ncc" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
<script>
function validateForm() {
    let isValid = true;
    
 

    // Check for empty fields and length constraints
    const tenNCC = document.getElementById('TenNCC').value.trim();
    const diaChi = document.getElementById('DiaChi').value.trim();
    const email = document.getElementById('Email').value.trim();
    const soDienThoai = document.getElementById('SoDienThoai').value.trim();
    const moTa = document.getElementById('MoTa').value.trim();
    const img = document.getElementById('Img').files[0];

    if (tenNCC === '') {
        document.getElementById('TenNCCError').textContent = 'Tên nhà cung cấp không được để trống.';
        isValid = false;
    } else if (tenNCC.length < 3 || tenNCC.length > 255) {
        document.getElementById('TenNCCError').textContent = 'Tên nhà cung cấp phải từ 3 đến 255 ký tự.';
        isValid = false;
    }

    if (diaChi === '') {
        document.getElementById('DiaChiError').textContent = 'Địa chỉ không được để trống.';
        isValid = false;
    }

    if (email === '') {
        document.getElementById('EmailError').textContent = 'Email không được để trống.';
        isValid = false;
    } else if (!/\S+@\S+\.\S+/.test(email)) {
        document.getElementById('EmailError').textContent = 'Email không hợp lệ.';
        isValid = false;
    }

    if (soDienThoai === '') {
        document.getElementById('PhoneError').textContent = 'Số điện thoại không được để trống.';
        isValid = false;
    } else if (!/^\d{10,15}$/.test(soDienThoai)) {
        document.getElementById('PhoneError').textContent = 'Số điện thoại không hợp lệ.';
        isValid = false;
    }

 
    if (!img) {
        document.getElementById('ImgError').textContent = 'Hãy chọn hình ảnh để tải lên.';
        isValid = false;
    } else {
        const allowedExtensions = ['image/jpeg', 'image/png'];
        if (!allowedExtensions.includes(img.type)) {
            document.getElementById('ImgError').textContent = 'Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.';
            isValid = false;
        }
    }

    return isValid;
}
function validateImage() {
    const fileInput = document.getElementById('Img');
    const filePath = fileInput.value;
    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

    if (filePath && !allowedExtensions.exec(filePath)) {
        document.getElementById('ImgError').textContent = 'Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.';
        fileInput.value = ''; // Clear the input
        document.getElementById('preview').src = '';
        return false;
    } else {
        document.getElementById('ImgError').textContent = ''; // Clear error message
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
}
function submitForm() {
    if (validateForm()) {
        const form = document.getElementById('nccForm');
        const formData = new FormData(form);

        fetch('modules/manage_suppliers/add.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('success')) {
                // Thành công: Chuyển hướng về trang danh sách nhà cung cấp
                window.location.href = 'index.php?ncc=list-ncc';
            } else {
                // Hiển thị thông báo lỗi
                console.log(data);
                alert('Có lỗi xảy ra, vui lòng kiểm tra lại.');
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            alert('Đã xảy ra lỗi khi gửi dữ liệu.');
        });
    }
}

function clearErrors() {
    document.getElementById('TenNCCError').textContent = '';
    document.getElementById('DiaChiError').textContent = '';
    document.getElementById('EmailError').textContent = '';
    document.getElementById('PhoneError').textContent = '';
    document.getElementById('MoTaError').textContent = '';
    document.getElementById('ImgError').textContent = '';
}

function clearFormData() {
    document.getElementById('nccForm').reset();
    document.getElementById('preview').src = '';
}

</script>

<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 80px;
}
</style>
