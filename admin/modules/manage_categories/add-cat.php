<div id="content" class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
            <div class="card-header font-weight-bold d-flex align-items-center justify-content-between">
    <button class="btn btn-primary" style="margin: 0;">
        <a style="color: white; text-decoration: none; border-radius: 5px;" href="?cat=list-cat">Quay lại</a>
    </button>
    <h5 class="m-0" style="text-align: center; font-size: 28px; flex-grow: 1;">Thêm danh mục</h5>
</div>
                <div class="card-body">
                    <!-- Hiển thị thông báo lỗi hoặc thành công từ session -->
                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                            <?php unset($_SESSION['errors']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success" role="alert">
                            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="modules/manage_categories/add.php" method="POST">
                        <div class="form-group">
                            <label for="name">Tên danh mục</label>
                            <input  class="form-control <?php echo isset($_SESSION['errors']['name']) ? 'is-invalid' : ''; ?>" type="text" name="name" id="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            <?php if (isset($_SESSION['errors']['name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo htmlspecialchars($_SESSION['errors']['name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="Mota">Mô Tả</label>
                            <textarea class="form-control" name="Mota" id="Mota"><?php echo isset($_POST['Mota']) ? htmlspecialchars($_POST['Mota']) : ''; ?></textarea>
                            <small class="form-text text-muted">Không bắt buộc</small>
                        </div>
                        <button type="submit" class="btn btn-success" name="add">Thêm mới</button>
                        <a href="index.php?cat=list-cat" class="btn btn-secondary">Hủy</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 80px;
}
</style>