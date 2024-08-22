<?php

if (isset($_GET['id_baiviet'])) {
    $ID_baiviet = intval($_GET['id_baiviet']); // Bảo mật: ép kiểu ID_baiviet thành số nguyên
    $sql_getNCC = "SELECT * FROM posts WHERE ID_baiviet=$ID_baiviet";
    $query_getNCC = mysqli_query($mysqli, $sql_getNCC);
    
    if ($query_getNCC) {
        $row = mysqli_fetch_array($query_getNCC);
        
        // Sử dụng dữ liệu đã lưu trong session nếu có
        $Tenbaiviet = isset($_SESSION['errors']['Tenbaiviet']) ? $_SESSION['errors']['Tenbaiviet'] : '';
        $NoidungError = isset($_SESSION['errors']['Noidung']) ? $_SESSION['errors']['Noidung'] : '';
        $data = isset($_SESSION['data']) ? $_SESSION['data'] : [
            'Tenbaiviet' => htmlspecialchars($row['Tenbaiviet']),
            'Noidung' => htmlspecialchars($row['Noidung']),
        ];
    } else {
        // Xử lý khi không tìm thấy nhà cung cấp
        echo "Bài viết không tồn tại.";
        exit();
    }
}
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold">
            Sửa thông tin bài viết
        </div>
        <div class="card-body">
            <form method="POST" action="modules/manage_posts/sua.php?id_baiviet=<?php echo $ID_baiviet; ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Tên bài viết:</label>
                    <input required class="form-control <?php echo isset($_SESSION['errors']['Tenbaiviet']) ? 'is-invalid' : ''; ?>" type="text" name="name" value="<?php echo isset($data['Tenbaiviet']) ? htmlspecialchars($data['Tenbaiviet']) : ''; ?>">
                    <?php if (isset($_SESSION['errors']['Tenbaiviet'])): ?>
                        <div class="invalid-feedback">
                            <?php echo $_SESSION['errors']['Tenbaiviet']; ?>
                        </div>
                    <?php endif; ?>
                </div>
               
                <div class="form-group">
                    <label for="formFile">Hình ảnh:</label>
                    <?php if (!empty($row['Img'])): ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center;" src="../assets/image/supplier/<?php echo htmlspecialchars($row['Img']); ?>" alt="Hình ảnh bài viết">
                    <?php else: ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center;" src="#" alt="Hình ảnh bài viết" style="display: none;">
                    <?php endif; ?>
                    <input class="form-control" type="file" name="image" accept="image/*" onchange="previewImage()">
                </div>
               
                <div class="form-group">
                    <label for="Noidung">Nội dung:</label>
                    <textarea required class="form-control <?php echo isset($_SESSION['errors']['Noidung']) ? 'is-invalid' : ''; ?>" name="Noidung" rows="10" style="width: 100%; resize: both;"><?php echo isset($data['Noidung']) ? htmlspecialchars(trim($data['Noidung'])) : ''; ?></textarea>
                    <?php if (isset($_SESSION['errors']['Noidung'])): ?>
                        <div class="invalid-feedback">
                            <?php echo $_SESSION['errors']['Noidung']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <input type="submit" class="btn btn-primary" name="submit" value="Lưu">
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
</script>
