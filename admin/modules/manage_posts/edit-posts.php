<?php


if (isset($_GET['id_baiviet'])) {
    $ID_baiviet = intval($_GET['id_baiviet']); // Bảo mật: ép kiểu ID_baiviet thành số nguyên
    $sql_getNCC = "SELECT * FROM posts WHERE ID_baiviet=$ID_baiviet";
    $query_getNCC = mysqli_query($mysqli, $sql_getNCC);

    if ($query_getNCC) {
        $row = mysqli_fetch_array($query_getNCC);

        // Sử dụng dữ liệu đã lưu trong session nếu có
        $Tenbaiviet = isset($_SESSION['data']['Tenbaiviet']) ? htmlspecialchars($_SESSION['data']['Tenbaiviet']) : htmlspecialchars($row['Tenbaiviet']);
        $Noidung = isset($_SESSION['data']['Noidung']) ? htmlspecialchars($_SESSION['data']['Noidung']) : htmlspecialchars($row['Noidung']);
        $nameError = isset($_SESSION['errors']['Tenbaiviet']) ? $_SESSION['errors']['Tenbaiviet'] : '';
        $noidungError = isset($_SESSION['errors']['Noidung']) ? $_SESSION['errors']['Noidung'] : '';
    } else {
        echo "Bài viết không tồn tại.";
        exit();
    }
} else {
    echo "ID bài viết không hợp lệ.";
    exit();
}
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?posts=list-posts">Quay lại</a>
            </button>
            <h5 class="m-0" style="flex-grow: 1; text-align: center; font-size: 30px; margin-left: -20px;">Sửa bài viết</h5>
        </div>
        <div class="card-body">
            <form id="editPostForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Tên bài viết:</label>
                    <input class="form-control" type="text" name="name" id="name" value="<?php echo $Tenbaiviet; ?>" required>
                    <div id="nameError" class="error-message"><?php echo $nameError; ?></div>
                </div>
                
                <div class="form-group">
                    <label for="formFile">Hình ảnh:</label>
                    <?php if (!empty($row['Img'])): ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center;" src="../assets/image/supplier/<?php echo htmlspecialchars($row['Img']); ?>" alt="Hình ảnh bài viết">
                    <?php else: ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center;" src="#" alt="Hình ảnh bài viết" style="display: none;">
                    <?php endif; ?>
                    <input class="form-control" type="file" name="image" id="image" accept="image/*" onchange="previewImage()">
                </div>
                
                <div class="form-group">
                    <label for="Noidung">Nội dung:</label>
                    <textarea class="form-control" name="Noidung" id="Noidung" rows="10" style="width: 100%; resize: both;" required><?php echo $Noidung; ?></textarea>
                    <div id="noidungError" class="error-message"><?php echo $noidungError; ?></div>
                </div>
                <input type="hidden" name="id_baiviet" value="<?php echo $ID_baiviet; ?>">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
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
document.getElementById('editPostForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Lấy giá trị từ form
    const name = document.getElementById('name').value.trim();
    const noidung = document.getElementById('Noidung').value.trim();
    const image = document.getElementById('image').files[0];
    
    let hasError = false;

    // Kiểm tra lỗi
    // Kiểm tra tên bài viết
    if (name.length < 3 || name.length > 255) {
        document.getElementById('nameError').textContent = "Tên bài viết phải từ 3 đến 255 ký tự.";
        hasError = true;
    } else {
        document.getElementById('nameError').textContent = "";
    }

    // Kiểm tra nội dung
    if (noidung.length <= 10) {
        document.getElementById('noidungError').textContent = "Nội dung quá ngắn.";
        hasError = true;
    } else {
        document.getElementById('noidungError').textContent = "";
    }

    // Kiểm tra nếu có lỗi thì không gửi form
    if (hasError) {
        return;
    }

    // Nếu không có lỗi, tạo FormData và gửi yêu cầu
    const form = document.getElementById('editPostForm');
    const formData = new FormData(form);

    fetch('modules/manage_posts/sua.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            window.location.href = "index.php?posts=list-posts"; // Chuyển hướng ngay lập tức
        } else {
            alert(result.message); // Hiển thị thông báo lỗi nếu có
        }
    })
    .catch(error => {
        console.error('Có lỗi xảy ra:', error);
        alert('Đã xảy ra lỗi khi gửi dữ liệu.');
    });
});

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

<style>
.error-message {
    color: red;
    font-size: 0.875em;
}

#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 60px;
}

</style>
