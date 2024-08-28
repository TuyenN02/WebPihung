<?php
// Lấy thông tin lỗi và dữ liệu từ session
$TenbaivietError = isset($_SESSION['errors']['Tenbaiviet']) ? $_SESSION['errors']['Tenbaiviet'] : '';
$NoidungError = isset($_SESSION['errors']['Noidung']) ? $_SESSION['errors']['Noidung'] : '';
$ImgError = isset($_SESSION['errors']['Img']) ? $_SESSION['errors']['Img'] : '';
$data = isset($_SESSION['data']) ? $_SESSION['data'] : [];
unset($_SESSION['errors']); // Xóa thông tin lỗi sau khi đã lấy
unset($_SESSION['data']); // Xóa dữ liệu sau khi đã lấy
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?posts=list-posts">Quay lại</a>
            </button>
            <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Thêm bài viết</h5>
        </div>
        <div class="card-body">
            <form id="postForm" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="Tenbaiviet">Tên bài viết:</label>
                    <input class="form-control" type="text" name="Tenbaiviet" id="Tenbaiviet" value="<?php echo htmlspecialchars($data['Tenbaiviet'] ?? ''); ?>">
                    <div id="TenbaivietError" class="text-danger"><?php echo $TenbaivietError; ?></div>
                </div>
                <div class="form-group">
                    <label>Hình ảnh:</label>
                    <input class="form-control" type="file" name="Img" id="Img" accept=".jpg,.png" onchange="validateImage()">
                    <div id="ImgError" class="text-danger"><?php echo $ImgError; ?></div>
                    <img id="preview" src="<?php echo isset($data['Img']) ? '../../../assets/image/supplier/' . htmlspecialchars($data['Img']) : ''; ?>" style="max-width: 200px; margin-top: 10px;">
                </div>
                <div class="form-group">
                    <label for="Noidung">Nội dung:</label>
                    <textarea class="form-control" name="Noidung" id="Noidung" rows="10" style="width: 100%; resize: both;"><?php echo htmlspecialchars($data['Noidung'] ?? ''); ?></textarea>
                    <div id="NoidungError" class="text-danger"><?php echo $NoidungError; ?></div>
                </div>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Thêm mới</button>
                <a href="index.php?posts=list-posts" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<script>
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

function validateForm() {
    let isValid = true;
    
    // Clear previous error messages
    document.getElementById('TenbaivietError').textContent = '';
    document.getElementById('ImgError').textContent = '';
    document.getElementById('NoidungError').textContent = '';

    // Check for empty fields and length constraints
    const tenbaiviet = document.getElementById('Tenbaiviet').value.trim();
    const noidung = document.getElementById('Noidung').value.trim();

    if (tenbaiviet === '') {
        document.getElementById('TenbaivietError').textContent = 'Tên bài viết không được để trống.';
        isValid = false;
    } else if (tenbaiviet.length < 3 || tenbaiviet.length > 255) {
        document.getElementById('TenbaivietError').textContent = 'Tên bài viết phải từ 3 đến 255 ký tự.';
        isValid = false;
    }

    if (noidung === '') {
        document.getElementById('NoidungError').textContent = 'Nội dung không được để trống.';
        isValid = false;
    }else if (noidung.length <= 10) {
        document.getElementById('NoidungError').textContent = 'Nội dung quá ngắn.';
        isValid = false;
    }


    // Check for image file validity
    const fileInput = document.getElementById('Img');
    const filePath = fileInput.value;
    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

    if (filePath && !allowedExtensions.exec(filePath)) {
        document.getElementById('ImgError').textContent = 'Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.';
        isValid = false;
    }

    return isValid;
}

function submitForm() {
    if (validateForm()) {
        const form = document.getElementById('postForm');
        const formData = new FormData(form);

        fetch('modules/manage_posts/add.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('success')) {
                // Thành công: Chuyển hướng về trang danh sách bài viết
                window.location.href = 'index.php?posts=list-posts';
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
</script>

<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 80px;
}
</style>