<?php
if (isset($_GET['id'])) {
    $ID_ChinhSach = intval($_GET['id']); // Bảo mật: ép kiểu ID_ChinhSach thành số nguyên
    $sql_getPolicy = "SELECT * FROM chinhsach WHERE ID_ChinhSach=$ID_ChinhSach";
    $query_getPolicy = mysqli_query($mysqli, $sql_getPolicy);

    if ($query_getPolicy) {
        $row = mysqli_fetch_array($query_getPolicy);

        // Sử dụng dữ liệu đã lưu trong session nếu có
        $TieuDe = isset($_SESSION['data']['TieuDe']) ? htmlspecialchars($_SESSION['data']['TieuDe']) : htmlspecialchars($row['TieuDe']);
        $NoiDung = isset($_SESSION['data']['NoiDung']) ? htmlspecialchars($_SESSION['data']['NoiDung']) : htmlspecialchars($row['NoiDung']);

        // Các lỗi
        $TieuDeError = isset($_SESSION['errors']['TieuDe']) ? $_SESSION['errors']['TieuDe'] : '';
        $NoiDungError = isset($_SESSION['errors']['NoiDung']) ? $_SESSION['errors']['NoiDung'] : '';
    } else {
        echo "Chính sách không tồn tại.";
        exit();
    }
} else {
    echo "ID chính sách không hợp lệ.";
    exit();
}
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?cat=list-policy">Quay lại</a>
            </button>
            <h5 class="m-0" style="flex-grow: 1; text-align: center; font-size: 30px;">Sửa chính sách</h5>
        </div>
        <div class="card-body">
            <form id="editPolicyForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TieuDe">Tiêu đề chính sách:</label>
                    <input class="form-control" type="text" name="TieuDe" id="TieuDe" value="<?php echo $TieuDe; ?>" required>
                    <div id="TieuDeError" class="error-message"><?php echo $TieuDeError; ?></div>
                </div>

                <div class="form-group">
                    <label for="NoiDung">Nội dung:</label>
                    <textarea required class="form-control" name="NoiDung" id="NoiDung" rows="5"><?php echo $NoiDung; ?></textarea>
                    <div id="NoiDungError" class="error-message"><?php echo $NoiDungError; ?></div>
                </div>

                <input type="hidden" name="id" value="<?php echo $ID_ChinhSach; ?>">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="index.php?policy=list-policy" class="btn btn-secondary">Hủy</a>
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
document.getElementById('editPolicyForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Lấy giá trị từ form
    const tieuDe = document.getElementById('TieuDe').value.trim();
    const noiDung = document.getElementById('NoiDung').value.trim();
    
    let hasError = false;

    // Kiểm tra lỗi
    if (tieuDe.length < 3 || tieuDe.length > 50) {
        document.getElementById('TieuDeError').textContent = "Tiêu đề phải từ 3 đến 50 ký tự!";
        hasError = true;
    } else {
        document.getElementById('TieuDeError').textContent = "";
    }
    if (noiDung.length < 10) {
        document.getElementById('NoiDungError').textContent = "Nội dung phải từ 10 ký tự!";
        hasError = true;
    } else {
        document.getElementById('NoiDungError').textContent = "";
    }

    // Kiểm tra nếu có lỗi thì không gửi form
    if (hasError) {
        return;
    }

    // Nếu không có lỗi, tạo FormData và gửi yêu cầu
    const form = document.getElementById('editPolicyForm');
    const formData = new FormData(form);

    fetch('modules/manage_policies/sua.php', {
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
});
</script>

<style>
    #wp-content {
        margin-left: 250px;
        flex: 1;
        padding: 10px;
        margin-top: 70px;
    }
    .error-message {
        color: red;
        font-size: 0.875em;
    }
</style>
