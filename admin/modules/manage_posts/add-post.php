<?php


// Lấy thông tin lỗi và dữ liệu từ session
$TenbaivietError = isset($_SESSION['errors']['Tenbaiviet']) ? $_SESSION['errors']['Tenbaiviet'] : '';
$NoidungError = isset($_SESSION['errors']['Noidung']) ? $_SESSION['errors']['Noidung'] : '';
$ImgError = isset($_SESSION['errors']['Img']) ? $_SESSION['errors']['Img'] : '';
$data = isset($_SESSION['data']) ? $_SESSION['data'] : [];
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold">
            Thêm bài viết
        </div>
        <div class="card-body">
            <form method="POST" action="modules/manage_posts/add.php" enctype="multipart/form-data">
            <div class="form-group">
                    <label for="TenNCC">Tên nhà cung cấp:</label>
                    <input required class="form-control <?php echo $TenbaivietError ? 'is-invalid' : ''; ?>" type="text" name="Tenbaiviet" id="Tenbaiviet" value="<?php echo isset($data['Tenbaiviet']) ? htmlspecialchars(trim($data['Tenbaiviet'])) : ''; ?>">
                    <?php if ($TenbaivietError): ?>
                        <div class="invalid-feedback">
                            <?php echo $TenbaivietError; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Hình ảnh:</label>
                    <input class="form-control <?php echo $ImgError ? 'is-invalid' : ''; ?>" type="file" name="Img" accept=".jpg,.png" onchange="validateImage()">
                    <?php if ($ImgError): ?>
                        <div class="invalid-feedback">
                            <?php echo $ImgError; ?>
                        </div>
                    <?php endif; ?>
                    <img id="preview" src="<?php echo isset($data['Img']) && !empty($data['Img']) ? '../../../assets/image/supplier/' . htmlspecialchars($data['Img']) : ''; ?>" style="max-width: 200px; margin-top: 10px;">
                </div>
                <div class="form-group">
                    <label for="Noidung">Nội dung:</label>
                    <textarea required class="form-control <?php echo $NoidungError ? 'is-invalid' : ''; ?>" name="Noidung" rows="10" style="width: 100%; resize: both;"><?php echo isset($data['Noidung']) ? htmlspecialchars(trim($data['Noidung'])) : ''; ?></textarea>
                    <?php if ($NoidungError): ?>
                        <div class="invalid-feedback">
                            <?php echo $NoidungError; ?>
                        </div>
                    <?php endif; ?>
                </div>
            
                <input type="submit" class="btn btn-primary" value="Thêm mới" name="submit">
                <a href="index.php?posts=list-posts" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php
// Xóa thông báo lỗi và dữ liệu sau khi hiển thị
unset($_SESSION['errors']);
unset($_SESSION['data']);
?>

<script>
    function validateImage() {
        var fileInput = document.querySelector('input[name="Img"]');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.');
            fileInput.value = ''; // Clear the input
            document.getElementById('preview').src = '';
            return false;
        } else {
            // Image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
</script>
