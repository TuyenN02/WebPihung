<?php

// Xóa dữ liệu cũ từ session khi người dùng truy cập trang này
unset($_SESSION['data']);
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Lấy dữ liệu từ session nếu có
$data = isset($_SESSION['data']) ? $_SESSION['data'] : [
    'TenDanhMuc' => '',
    'MoTa' => '',
];

// Hiển thị thông báo nếu có
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Xóa thông báo sau khi hiển thị
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?cat=list-cat">Quay lại</a>
            </button>
            <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Thêm Danh Mục</h5>
        </div>
        <div class="card-body">
            <form id="addCategoryForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TenDanhMuc">Tên danh mục:</label>
                    <input required class="form-control" type="text" name="TenDanhMuc" id="TenDanhMuc" value="<?php echo htmlspecialchars($data['TenDanhMuc']); ?>" >
                    <div id="TenDanhMucError" class="error-message"><?php echo isset($error_message['TenDanhMuc']) ? $error_message['TenDanhMuc'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea class="form-control" name="MoTa" id="MoTa" rows="5"><?php echo htmlspecialchars($data['MoTa']); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Thêm</button>
                <button type="button" id="cancelButton" class="btn btn-secondary">Hủy</button>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('addCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Lấy giá trị từ form
    const tenDanhMuc = document.getElementById('TenDanhMuc').value.trim();
    
    let hasError = false;

    // Kiểm tra lỗi
    if (tenDanhMuc.length < 3 || tenDanhMuc.length > 50) {
        document.getElementById('TenDanhMucError').textContent = "Tên danh mục phải từ 3 đến 50 ký tự.";
        hasError = true;
    } else {
        document.getElementById('TenDanhMucError').textContent = "";
    }

    // Kiểm tra nếu có lỗi thì không gửi form
    if (hasError) {
        return;
    }

    // Nếu không có lỗi, tạo FormData và gửi yêu cầu
    const form = document.getElementById('addCategoryForm');
    const formData = new FormData(form);

    fetch('modules/manage_categories/add.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        console.log('Fetch Result:', result); // Kiểm tra phản hồi từ máy chủ

        if (result.status === 'success') {
            // Reset form sau khi thêm thành công
            form.reset();

            // Xóa tất cả các thông báo lỗi
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

            // Chuyển hướng nếu cần
            window.location.href = "index.php?cat=list-cat";
        } else {
            // Hiển thị thông báo lỗi cho từng trường cụ thể
            if (result.message.includes('Tên danh mục đã tồn tại!')) {
                document.getElementById('TenDanhMucError').textContent = result.message;
            } else {
                alert(result.message); // Hiển thị thông báo lỗi khác nếu có
            }
        }
    })
    .catch(error => {
        console.error('Có lỗi xảy ra:', error);
        alert('Đã xảy ra lỗi khi gửi dữ liệu.');
    });
});

document.getElementById('cancelButton').addEventListener('click', function() {
    // Reset form
    const form = document.getElementById('addCategoryForm');
    form.reset(); 

    // Xóa tất cả các thông báo lỗi
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // Chuyển hướng về trang danh sách
    window.location.href = "index.php?cat=list-cat";
});
</script>
<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 80px;
}

    .error-message {
        color: red;
        font-size: 0.875em;
    }
</style>
