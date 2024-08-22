<?php

if (isset($_GET['id'])) {
    $ID_DanhMuc = intval($_GET['id']); // Bảo mật: ép kiểu ID_DanhMuc thành số nguyên
    $sql_getDanhMuc = "SELECT * FROM danhmuc WHERE ID_DanhMuc = $ID_DanhMuc LIMIT 1";
    $query_getDanhMuc = mysqli_query($mysqli, $sql_getDanhMuc);

    if ($query_getDanhMuc) {
        $row = mysqli_fetch_array($query_getDanhMuc);
    } else {
        $_SESSION['errors']['database'] = "Lỗi cơ sở dữ liệu: " . mysqli_error($mysqli);
        header('Location: ../../index.php?cat=list-cat');
        exit();
    }
} else {
    $_SESSION['errors']['general'] = "ID danh mục không được xác định.";
    header('Location: ../../index.php?cat=list-cat');
    exit();
}
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold">
            Chỉnh sửa thông tin danh mục
        </div>
        <div class="card-body">
            <!-- Hiển thị thông báo lỗi hoặc thành công từ session -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" id="success-alert">
                    <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="modules/manage_categories/sua.php?id=<?php echo $ID_DanhMuc; ?>" method="POST">
                <div class="form-group">
                    <label for="name">Tên danh mục</label>
                    <input required class="form-control <?php echo isset($_SESSION['errors']['TenDanhMuc']) ? 'is-invalid' : ''; ?>" type="text" name="TenDanhMuc" id="name"
                           value="<?php echo isset($row['TenDanhMuc']) ? htmlspecialchars($row['TenDanhMuc']) : ''; ?>">
                    <?php if (isset($_SESSION['errors']['TenDanhMuc'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($_SESSION['errors']['TenDanhMuc']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="Mota">Mô tả:</label>
                    <textarea class="form-control <?php echo isset($_SESSION['errors']['Mota']) ? 'is-invalid' : ''; ?>" name="Mota" id="Mota" rows="4"><?php echo isset($row['Mota']) ? htmlspecialchars($row['Mota']) : ''; ?></textarea>
                    <small class="form-text text-muted">Không bắt buộc</small>
                    <?php if (isset($_SESSION['errors']['Mota'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($_SESSION['errors']['Mota']); ?></small>
                    <?php endif; ?>
                </div>
                <input type="submit" class="btn btn-primary" name="submit" value="Lưu">
                <a href="index.php?cat=list-cat" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<script>
    // Ẩn thông báo thành công sau 3 giây
    setTimeout(function() {
        var alert = document.getElementById('success-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 3000);
</script>
