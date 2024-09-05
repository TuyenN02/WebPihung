<?php

// Xóa thông báo lỗi và dữ liệu nếu có yêu cầu hủy bỏ
if (isset($_GET['action']) && $_GET['action'] === 'cancel') {
    unset($_SESSION['errors']);
    unset($_SESSION['data']);
    header("Location: index.php?policy=list-policy");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_policy = "SELECT * FROM chinhsach WHERE ID_ChinhSach='$id' LIMIT 1";
    $query_policy = mysqli_query($mysqli, $sql_policy);
    $row = mysqli_fetch_array($query_policy);
}
?>
<div class="container">
    <div class="card">
    <div class="card-header font-weight-bold d-flex align-items-center justify-content-between">
    <button class="btn btn-primary" style="margin: 0;">
        <a style="color: white; text-decoration: none; border-radius: 5px;" href="?policy=list-policy">Quay lại</a>
    </button>
    <h5 class="m-0" style="text-align: center; font-size: 28px; flex-grow: 1;">Sửa chính sách</h5>
        </div>
        <div class="card-body">
            <form action="modules/manage_policies/sua.php" method="POST">
                <div class="form-group">
                    <label for="TieuDe">Tiêu đề</label>
                    <input type="text" class="form-control <?php echo isset($_SESSION['errors']['TieuDe']) ? 'is-invalid' : ''; ?>" id="TieuDe" name="TieuDe" value="<?php echo htmlspecialchars(trim($row['TieuDe'])); ?>" required>
                    <?php
                    if (isset($_SESSION['errors']['TieuDe'])) {
                        echo "<div class='invalid-feedback'>" . $_SESSION['errors']['TieuDe'] . "</div>";
                    }
                    ?>
                </div>
                <div class="form-group">
                    <label for="NoiDung">Nội dung</label>
                    <textarea class="form-control <?php echo isset($_SESSION['errors']['NoiDung']) ? 'is-invalid' : ''; ?>" id="NoiDung" name="NoiDung" rows="5" required><?php echo htmlspecialchars(trim($row['NoiDung'])); ?></textarea>
                    <?php
                    if (isset($_SESSION['errors']['NoiDung'])) {
                        echo "<div class='invalid-feedback'>" . $_SESSION['errors']['NoiDung'] . "</div>";
                    }
                    ?>
                </div>
                <input type="hidden" name="ID_ChinhSach" value="<?php echo $row['ID_ChinhSach']; ?>">
                <button type="submit" name="updatePolicy" class="btn btn-primary">Cập nhật</button>
                <a href="index.php?policy=list-policy&action=cancel" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 100px;
}
</style>