<?php


if (isset($_GET['id_NCC'])) {
    $ID_NCC = intval($_GET['id_NCC']); // Bảo mật: ép kiểu ID_NCC thành số nguyên
    $sql_getNCC = "SELECT * FROM nhacungcap WHERE ID_NCC=$ID_NCC";
    $query_getNCC = mysqli_query($mysqli, $sql_getNCC);
    
    if ($query_getNCC) {
        $row = mysqli_fetch_array($query_getNCC);
        
        // Sử dụng dữ liệu đã lưu trong session nếu có
        $TenNCCError = isset($_SESSION['errors']['TenNCC']) ? $_SESSION['errors']['TenNCC'] : '';
        $phoneError = isset($_SESSION['errors']['phone']) ? $_SESSION['errors']['phone'] : '';
        $emailError = isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : '';
        $data = isset($_SESSION['data']) ? $_SESSION['data'] : [
            'TenNCC' => htmlspecialchars($row['TenNCC']),
            'MoTa' => htmlspecialchars($row['MoTa']),
            'Email' => htmlspecialchars($row['Email']),
            'SoDienThoai' => htmlspecialchars($row['SoDienThoai']),
            'DiaChi' => htmlspecialchars($row['DiaChi']),
        ];
    } else {
        // Xử lý khi không tìm thấy nhà cung cấp
        echo "Nhà cung cấp không tồn tại.";
        exit();
    }
}
?>
<?php
// Xóa thông báo lỗi và dữ liệu sau khi hiển thị
unset($_SESSION['errors']);
unset($_SESSION['data']);

?>

<div id="content" class="container-fluid">
    <div class="card">
    <div class="card-header font-weight-bold">
        <button class="btn btn-primary">
        <a style="color: white; text-decoration: none; padding: 10px 5px; border-radius: 5px;" href="?ncc=list-ncc">Quay lại</a>
        </button>
        <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Sửa nhà cung cấp</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="modules/manage_suppliers/sua.php?id_NCC=<?php echo $ID_NCC; ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Tên nhà cung cấp:</label>
                    <input required class="form-control <?php echo $TenNCCError ? 'is-invalid' : ''; ?>" type="text" name="name" value="<?php echo $data['TenNCC']; ?>">
                    <?php if ($TenNCCError): ?>
                        <div class="invalid-feedback">
                            <?php echo $TenNCCError; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea name="MoTa" class="form-control"><?php echo $data['MoTa']; ?></textarea>
                    <small class="form-text text-muted">Không bắt buộc</small> <!-- Chú thích không bắt buộc -->
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input required class="form-control <?php echo $emailError ? 'is-invalid' : ''; ?>" type="text" name="email" value="<?php echo $data['Email']; ?>">
                    <?php if ($emailError): ?>
                        <div class="invalid-feedback">
                            <?php echo $emailError; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="SoDienThoai">Số điện thoại:</label>
                    <input required class="form-control <?php echo $phoneError ? 'is-invalid' : ''; ?>" type="text" name="SoDienThoai" value="<?php echo $data['SoDienThoai']; ?>">
                    <?php if ($phoneError): ?>
                        <div class="invalid-feedback">
                            <?php echo $phoneError; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
    <label for="DiaChi">Địa chỉ:</label>
    <input required class="form-control <?php echo isset($_SESSION['errors']['DiaChi']) ? 'is-invalid' : ''; ?>" type="text" name="DiaChi" value="<?php echo $data['DiaChi']; ?>">
    <?php if (isset($_SESSION['errors']['DiaChi'])): ?>
        <div class="invalid-feedback">
            <?php echo $_SESSION['errors']['DiaChi']; ?>
        </div>
    <?php endif; ?>
</div>
                <div class="form-group">
                    <label for="formFile">Hình ảnh:</label>
                    <?php if (!empty($row['Img'])): ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center;" src="../assets/image/supplier/<?php echo htmlspecialchars($row['Img']); ?>" alt="Hình ảnh nhà cung cấp">
                    <?php else: ?>
                        
                    <?php endif; ?>
                    <input class="form-control" type="file" name="image" accept="image/*" onchange="previewImage()">
                </div>
                <input type="submit" class="btn btn-primary" name="submit" value="Lưu">
                <a href="index.php?ncc=list-ncc" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>



<script>
    function previewImage() {
        var fileInput = document.querySelector('input[name="image"]');
        var file = fileInput.files[0];
        var preview = document.getElementById('imagePreview');
        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
            preview.style.display = 'block'; // Hiển thị ảnh khi có ảnh mới
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "#";
            preview.style.display = 'none';
        }
    }
    function clearTemporaryData() {
    // Gửi yêu cầu xóa dữ liệu tạm thời trước khi chuyển hướng
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "clear_temp_data.php", true);
    xhr.send();
}
</script>
<style>
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 60px;
}
.card-header .btn {
    margin-right: auto; /* Đẩy nút sang bên trái */
}

.card-header h5 {
    margin: 0;
    flex-grow: 1; /* Giúp tiêu đề tự động chiếm không gian còn lại */
    text-align: center; /* Căn giữa tiêu đề */
}
</style>