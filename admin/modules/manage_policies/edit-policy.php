<?php

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_policy = "SELECT * FROM chinhsach WHERE ID_ChinhSach='$id' LIMIT 1";
    $query_policy = mysqli_query($mysqli, $sql_policy);
    $row = mysqli_fetch_array($query_policy);
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h5>Chỉnh sửa chính sách</h5>
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
                <a href="index.php?policy=list-policy" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
