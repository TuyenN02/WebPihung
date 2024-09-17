<?php
if (isset($_GET['id'])) {
    $ID_DanhMuc = intval($_GET['id']); // Bảo mật: ép kiểu ID_DanhMuc thành số nguyên
    $sql_getCat = "SELECT * FROM danhmuc WHERE ID_DanhMuc=$ID_DanhMuc";
    $query_getCat = mysqli_query($mysqli, $sql_getCat);

    if ($query_getCat) {
        $row = mysqli_fetch_array($query_getCat);

        // Sử dụng dữ liệu đã lưu trong session nếu có
        $TenDanhMuc = isset($_SESSION['data']['TenDanhMuc']) ? htmlspecialchars($_SESSION['data']['TenDanhMuc']) : htmlspecialchars($row['TenDanhMuc']);
        $MoTa = isset($_SESSION['data']['MoTa']) ? htmlspecialchars($_SESSION['data']['MoTa']) : htmlspecialchars($row['Mota']);

        // Các lỗi
        $TenDanhMucError = isset($_SESSION['errors']['TenDanhMuc']) ? $_SESSION['errors']['TenDanhMuc'] : '';
        $MoTaError = isset($_SESSION['errors']['MoTa']) ? $_SESSION['errors']['MoTa'] : '';
    } else {
        echo "Danh mục không tồn tại.";
        exit();
    }
} else {
    echo "ID danh mục không hợp lệ.";
    exit();
}
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?cat=list-cat">Quay lại</a>
            </button>
            <h5 class="m-0" style="flex-grow: 1; text-align: center; font-size: 30px;">Sửa danh mục</h5>
        </div>
        <div class="card-body">
            <form id="editCategoryForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TenDanhMuc">Tên danh mục:</label>
                    <input class="form-control" type="text" name="TenDanhMuc" id="TenDanhMuc" value="<?php echo $TenDanhMuc; ?>" required>
                    <div id="TenDanhMucError" class="error-message"><?php echo $TenDanhMucError; ?></div>
                </div>

                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea class="form-control" name="MoTa" id="MoTa" rows="5"><?php echo $MoTa; ?></textarea>
                    <div id="MoTaError" class="error-message"><?php echo $MoTaError; ?></div>
                    <small class="form-text" style="color: #c67777;">* Không bắt buộc</small> <!-- Dòng chữ nhỏ màu đỏ -->
                </div>

                <input type="hidden" name="id" value="<?php echo $ID_DanhMuc; ?>">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="index.php?cat=list-cat" class="btn btn-secondary">Hủy</a>
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
// Hàm kiểm tra giá trị nhập vào trường Tên danh mục
function validateTenDanhMuc() {
    const tenDanhMuc = document.getElementById('TenDanhMuc').value.trim();
    if (tenDanhMuc.length < 3 || tenDanhMuc.length > 30) {
        document.getElementById('TenDanhMucError').textContent = "Tên danh mục phải từ 3 đến 30 ký tự.";
        return false;
    } else {
        document.getElementById('TenDanhMucError').textContent = "";
        return true;
    }
}

// Hàm kiểm tra giá trị nhập vào trường Mô tả
function validateMoTa() {
    const moTa = document.getElementById('MoTa').value.trim();
    if (moTa.length > 255) {
        document.getElementById('MoTaError').textContent = "Mô tả không được vượt quá 255 ký tự.";
        return false;
    } else {
        document.getElementById('MoTaError').textContent = "";
        return true;
    }
}

// Gắn sự kiện 'input' để bắt lỗi ngay khi người dùng nhập
document.getElementById('TenDanhMuc').addEventListener('input', validateTenDanhMuc);
document.getElementById('MoTa').addEventListener('input', validateMoTa);

document.getElementById('editCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Kiểm tra lỗi từng trường trước khi gửi form
    const isTenDanhMucValid = validateTenDanhMuc();
    const isMoTaValid = validateMoTa();

    // Nếu có lỗi thì không gửi form
    if (!isTenDanhMucValid || !isMoTaValid) {
        return;
    }

    // Nếu không có lỗi, tạo FormData và gửi yêu cầu
    const form = document.getElementById('editCategoryForm');
    const formData = new FormData(form);

    fetch('modules/manage_categories/sua.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        console.log('Fetch Result:', result); // Kiểm tra phản hồi từ máy chủ

        if (result.status === 'success') {
            window.location.href = "index.php?cat=list-cat"; // Chuyển hướng ngay lập tức
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
