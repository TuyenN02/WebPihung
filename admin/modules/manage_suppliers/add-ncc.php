<?php

// Lấy thông tin lỗi và dữ liệu từ session
$TenNCCError = isset($_SESSION['errors']['TenNCC']) ? $_SESSION['errors']['TenNCC'] : '';
$phoneError = isset($_SESSION['errors']['SoDienThoai']) ? $_SESSION['errors']['SoDienThoai'] : '';
$emailError = isset($_SESSION['errors']['Email']) ? $_SESSION['errors']['Email'] : '';
$DiaChiError = isset($_SESSION['errors']['DiaChi']) ? $_SESSION['errors']['DiaChi'] : '';
$ImgError = isset($_SESSION['errors']['Img']) ? $_SESSION['errors']['Img'] : '';
$data = isset($_SESSION['data']) ? $_SESSION['data'] : [];
$uploadedImage = isset($_SESSION['uploaded_image']) ? $_SESSION['uploaded_image'] : '';

?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold">
            Thêm nhà cung cấp
        </div>
        <div class="card-body">
            <form method="POST" action="modules/manage_suppliers/add.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TenNCC">Tên nhà cung cấp:</label>
                    <input required class="form-control <?php echo $TenNCCError ? 'is-invalid' : ''; ?>" type="text" name="TenNCC" id="TenNCC" value="<?php echo isset($data['TenNCC']) ? htmlspecialchars(trim($data['TenNCC'])) : ''; ?>">
                    <?php if ($TenNCCError): ?>
                        <div class="invalid-feedback">
                            <?php echo $TenNCCError; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea class="form-control" name="MoTa" id="MoTa"><?php echo isset($data['MoTa']) ? htmlspecialchars(trim($data['MoTa'])) : ''; ?></textarea>
                    <small class="form-text text-muted">Không bắt buộc</small> <!-- Chú thích không bắt buộc -->
                </div>
                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input required class="form-control <?php echo $emailError ? 'is-invalid' : ''; ?>" type="email" name="Email" id="Email" value="<?php echo isset($data['Email']) ? htmlspecialchars(trim($data['Email'])) : ''; ?>">
                    <?php if ($emailError): ?>
                        <div class="invalid-feedback">
                            <?php echo $emailError; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="SoDienThoai">Số điện thoại:</label>
                    <input required class="form-control <?php echo $phoneError ? 'is-invalid' : ''; ?>" type="text" name="SoDienThoai" id="SoDienThoai" value="<?php echo isset($data['SoDienThoai']) ? htmlspecialchars(trim($data['SoDienThoai'])) : ''; ?>">
                    <?php if ($phoneError): ?>
                        <div class="invalid-feedback">
                            <?php echo $phoneError; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="DiaChi">Địa chỉ:</label>
                    <input required class="form-control <?php echo $DiaChiError ? 'is-invalid' : ''; ?>" type="text" name="DiaChi" id="DiaChi" value="<?php echo isset($data['DiaChi']) ? htmlspecialchars(trim($data['DiaChi'])) : ''; ?>">
                    <?php if ($DiaChiError): ?>
                        <div class="invalid-feedback">
                            <?php echo $DiaChiError; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="Img">Hình ảnh:</label>
                    <input required  class="form-control <?php echo $ImgError ? 'is-invalid' : ''; ?>" type="file" name="Img" id="Img" accept=".jpg,.png" onchange="validateImage()">
                    <?php if ($ImgError): ?>
                        <div class="invalid-feedback">
                            <?php echo $ImgError; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($uploadedImage): ?>
                        <img id="preview" src="<?php echo htmlspecialchars($uploadedImage); ?>" style="max-width: 200px; margin-top: 10px;">
                    <?php else: ?>
                        <img id="preview" src="" style="max-width: 200px; margin-top: 10px;">
                    <?php endif; ?>
                </div>
                <input type="submit" class="btn btn-primary" value="Thêm mới" name="submit">
                <a href="index.php?ncc=list-ncc" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php
// Xóa thông báo lỗi và dữ liệu sau khi hiển thị nếu không có lỗi
if (empty($TenNCCError) && empty($phoneError) && empty($emailError) && empty($DiaChiError) && empty($ImgError)) {
    unset($_SESSION['errors']);
    unset($_SESSION['data']);
    unset($_SESSION['uploaded_image']);
}
?>

<script>
    function validateImage() {
        var fileInput = document.getElementById('Img');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.');
            fileInput.value = ''; // Xóa dữ liệu đầu vào
            document.getElementById('preview').src = '';
            return false;
        } else {
            // Xem trước hình ảnh
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
