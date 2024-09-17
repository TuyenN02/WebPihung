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
                    <div class="image-container">
                        <?php if (!empty($row['Img'])): ?>
                            <img id="imagePreview" src="../assets/image/supplier/<?php echo htmlspecialchars($row['Img']); ?>" alt="Hình ảnh bài viết">
                        <?php else: ?>
                            <img id="imagePreview" src="#" alt="Hình ảnh bài viết" style="display: none;">
                        <?php endif; ?>
                        <input class="form-control" type="file" name="image" id="image" accept="image/*" onchange="previewImage()" style="display: none;">
                        <label for="image" class="btn btn-custom">Chọn hình ảnh</label> <!-- Nút tùy chỉnh -->
                    </div>
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
// Real-time validation for name input
document.getElementById('name').addEventListener('input', function() {
    const name = this.value.trim();
    if (name.length < 3 || name.length > 255) {
        document.getElementById('nameError').textContent = "Tên bài viết phải từ 3 đến 255 ký tự!";
    } else {
        document.getElementById('nameError').textContent = "";
    }
});

// Real-time validation for content input (Noidung)
document.getElementById('Noidung').addEventListener('input', function() {
    const noidung = this.value.trim();
    if (noidung.length <= 10) {
        document.getElementById('noidungError').textContent = "Nội dung phải từ 10 ký tự!";
    } else {
        document.getElementById('noidungError').textContent = "";
    }
});

// Image preview
document.getElementById('image').addEventListener('change', function() {
    previewImage();
});

// Form submit validation
document.getElementById('editPostForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission
    
    const name = document.getElementById('name').value.trim();
    const noidung = document.getElementById('Noidung').value.trim();
    
    let hasError = false;

    // Validate name
    if (name.length < 3 || name.length > 255) {
        document.getElementById('nameError').textContent = "Tên bài viết phải từ 3 đến 255 ký tự!";
        hasError = true;
    } else {
        document.getElementById('nameError').textContent = "";
    }

    // Validate content
    if (noidung.length <= 10) {
        document.getElementById('noidungError').textContent = "Nội dung phải từ 10 ký tự!";
        hasError = true;
    } else {
        document.getElementById('noidungError').textContent = "";
    }

    // If there are no errors, proceed with form submission
    if (!hasError) {
        const form = document.getElementById('editPostForm');
        const formData = new FormData(form);

        fetch('modules/manage_posts/sua.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                window.location.href = "index.php?posts=list-posts";
            } else {
                alert(result.message);
            }
        })
        .catch(error => {
            console.error('Có lỗi xảy ra:', error);
            alert('Đã xảy ra lỗi khi gửi dữ liệu.');
        });
    }
});


    function previewImage() {
        const input = document.getElementById('image');
        const preview = document.getElementById('imagePreview');
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Hiện ảnh khi đã chọn file
            }
            reader.readAsDataURL(file);
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
.btn-custom {
        display: inline-block;
        margin-top: 10px; /* Để nút xuất hiện ngay dưới ảnh */
        padding: 10px 20px;
        background-color: #5aa880; /* Màu xanh lá cây nhạt */
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
        text-align: center;
    }

    .btn-custom:hover {
        background-color: #76C776; /* Màu khi hover */
    }
 
    /* Container chứa ảnh và nút */
    .image-container {
        display: flex;
        flex-direction: column;
        align-items: center; /* Căn giữa theo chiều ngang */
    }

    /* Ảnh hiển thị */
    #imagePreview {
        width: 240px;
        height: 240px;
        object-fit: cover;
        object-position: center center;
        margin-bottom: 10px; /* Để nút cách ảnh một khoảng */
    }

    /* Nút chọn file tùy chỉnh */
    .btn-custom {
        padding: 3px 6px;
        background-color: #8dddb4; /* Màu xanh lá cây nhạt */
        color: #666666;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 15px;
        text-align: center;
    }

    .btn-custom:hover {
        background-color: #76C776; /* Màu khi hover */
    }

</style>
