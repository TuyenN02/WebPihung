<?php

// Lấy thông báo lỗi và dữ liệu từ session
$TieuDeError = isset($_SESSION['errors']['TieuDe']) ? $_SESSION['errors']['TieuDe'] : '';
$NoiDungError = isset($_SESSION['errors']['NoiDung']) ? $_SESSION['errors']['NoiDung'] : '';
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$data = isset($_SESSION['data']) ? $_SESSION['data'] : ['TieuDe' => '', 'NoiDung' => '']; // Mặc định là rỗng

// Xóa thông báo lỗi, thông báo thành công và dữ liệu sau khi hiển thị
unset($_SESSION['errors']);
unset($_SESSION['data']);
unset($_SESSION['success']);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex align-items-center justify-content-between">
            <button class="btn btn-primary" style="margin: 0;">
                <a style="color: white; text-decoration: none; border-radius: 5px;" href="?policy=list-policy">Quay lại</a>
            </button>
            <h5 class="m-0" style="text-align: center; font-size: 28px; flex-grow: 1;">Thêm chính sách</h5>
        </div>
        <div class="card-body">
            <?php if ($successMessage): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            
            <form id="policyForm" method="POST" action="modules/manage_policies/add.php">
                <div class="form-group">
                    <label for="TieuDe">Tiêu đề chính sách:</label>
                    <input 
                        class="form-control" 
                        type="text" 
                        name="TieuDe" 
                        id="TieuDe"
                        value="<?php echo htmlspecialchars(trim($data['TieuDe'])); ?>"
                    >
                    <div id="TieuDeError" class="error-message"><?php echo htmlspecialchars($TieuDeError); ?></div>
                </div>
                <div class="form-group">
                    <label for="NoiDung">Nội dung:</label>
                    <textarea 
                        class="form-control" 
                        name="NoiDung" 
                        id="NoiDung"
                    ><?php echo htmlspecialchars(trim($data['NoiDung'])); ?></textarea>
                    <div id="NoiDungError" class="error-message"><?php echo htmlspecialchars($NoiDungError); ?></div>
                </div>
                <input 
                    type="submit" 
                    class="btn btn-primary" 
                    value="Thêm mới" 
                    name="submit"
                >
                <a 
                    href="index.php?policy=list-policy" 
                    class="btn btn-secondary"
                >Hủy</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('TieuDe').addEventListener('input', function() {
    const tieuDe = this.value.trim();
    const errorElement = document.getElementById('TieuDeError');

    if (tieuDe.length < 3 || tieuDe.length > 50) {
        errorElement.textContent = "Tiêu đề phải từ 3 đến 50 ký tự!";
    } else {
        errorElement.textContent = "";
    }
});

document.getElementById('NoiDung').addEventListener('input', function() {
    const noiDung = this.value.trim();
    const errorElement = document.getElementById('NoiDungError');

    if (noiDung.length < 10) {
        errorElement.textContent = "Nội dung phải từ 10 ký tự!";
    } else {
        errorElement.textContent = "";
    }
});

document.getElementById('policyForm').addEventListener('submit', function(event) {
    const tieuDeError = document.getElementById('TieuDeError').textContent;
    const noiDungError = document.getElementById('NoiDungError').textContent;

    if (tieuDeError || noiDungError) {
        event.preventDefault(); // Ngăn gửi form nếu có lỗi
    } else {
        // Nếu không có lỗi, tạo FormData và gửi yêu cầu
        event.preventDefault(); // Ngăn gửi form mặc định

        const form = document.getElementById('policyForm');
        const formData = new FormData(form);

        fetch('modules/manage_policies/add.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log('Fetch Result:', result); // Kiểm tra phản hồi từ máy chủ

            if (result.status === 'success') {
                window.location.href = "index.php?policy=list-policy"; // Chuyển hướng ngay lập tức
            } else {
                alert(result.message); // Hiển thị thông báo lỗi khác nếu có
            }
        })
        .catch(error => {
            console.error('Có lỗi xảy ra:', error);
            alert('Đã xảy ra lỗi khi gửi dữ liệu.');
        });
    }
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
