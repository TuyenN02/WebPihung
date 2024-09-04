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
                </div>

                <input type="hidden" name="id" value="<?php echo $ID_DanhMuc; ?>">
                <button type="submit" class="btn btn-primary">Lưu</button>
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
document.getElementById('editCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Lấy giá trị từ form
    const tenDanhMuc = document.getElementById('TenDanhMuc').value.trim();
    const moTa = document.getElementById('MoTa').value.trim();
    
    let hasError = false;

    // Kiểm tra lỗi
    if (tenDanhMuc.length < 3 || tenDanhMuc.length > 30) {
        document.getElementById('TenDanhMucError').textContent = "Tên danh mục phải từ 3 đến 30 ký tự.";
        hasError = true;
    } else {
        document.getElementById('TenDanhMucError').textContent = "";
    }



    // Kiểm tra nếu có lỗi thì không gửi form
    if (hasError) {
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
