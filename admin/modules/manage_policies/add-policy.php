<?php


// Lấy thông báo lỗi và dữ liệu từ session
$TieuDeError = isset($_SESSION['errors']['TieuDe']) ? $_SESSION['errors']['TieuDe'] : '';
$NoiDungError = isset($_SESSION['errors']['NoiDung']) ? $_SESSION['errors']['NoiDung'] : '';
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$data = isset($_SESSION['data']) ? $_SESSION['data'] : [];

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
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="modules/manage_policies/add.php">
                <div class="form-group">
                    <label for="TieuDe">Tiêu đề chính sách:</label>
                    <input 
                        required 
                        class="form-control <?php echo $TieuDeError ? 'is-invalid' : ''; ?>" 
                        type="text" 
                        name="TieuDe" 
                        id="TieuDe"
                        value="<?php echo isset($data['TieuDe']) ? htmlspecialchars(trim($data['TieuDe'])) : ''; ?>"
                    >
                    <?php if ($TieuDeError): ?>
                        <div class="invalid-feedback"><?php echo $TieuDeError; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="NoiDung">Nội dung:</label>
                    <textarea 
                        required 
                        class="form-control <?php echo $NoiDungError ? 'is-invalid' : ''; ?>" 
                        name="NoiDung" 
                        id="NoiDung"
                    ><?php echo isset($data['NoiDung']) ? htmlspecialchars(trim($data['NoiDung'])) : ''; ?></textarea>
                    <?php if ($NoiDungError): ?>
                        <div class="invalid-feedback"><?php echo $NoiDungError; ?></div>
                    <?php endif; ?>
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
<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 100px;
}
</style>