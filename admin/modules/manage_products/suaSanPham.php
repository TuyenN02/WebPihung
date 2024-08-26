<?php
// Lấy ID sản phẩm từ query string và chuẩn bị câu lệnh SQL
$ID_SanPham = (int)$_GET['id'];
$sql = $mysqli->prepare("SELECT * FROM sanpham WHERE ID_SanPham = ?");
$sql->bind_param("i", $ID_SanPham);
$sql->execute();
$result = $sql->get_result();
$product = $result->fetch_assoc();

// Truy vấn để lấy danh sách danh mục và nhà cung cấp
$sql_DanhMuc = "SELECT * FROM danhmuc ORDER BY ID_DanhMuc DESC";
$query_DanhMuc = $mysqli->query($sql_DanhMuc);
$sql_NCC = "SELECT * FROM nhacungcap ORDER BY ID_NCC DESC";
$query_NCC = $mysqli->query($sql_NCC);

// Truy vấn để lấy ảnh phụ
$sql_Images = $mysqli->prepare("SELECT * FROM hinhanh_sanpham WHERE ID_SanPham = ?");
$sql_Images->bind_param("i", $ID_SanPham);
$sql_Images->execute();
$query_Images = $sql_Images->get_result();

// Hiển thị thông báo lỗi và thành công từ session
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
?>

<div id="content" class="container-fluid">
    <div class="card">
    <div class="card-header font-weight-bold d-flex align-items-center">
    <button class="btn btn-primary" style="margin-right: 10px;">
        <a style="color: white; text-decoration: none; border-radius: 5px;" href="?product=list-product">Quay lại</a>
    </button>
    <h5 class="m-0" style="text-align:center; font-size: 28px; flex-grow: 1;">Sửa sản phẩm</h5>
</div>
        <div class="card-body">
            <form action="modules/manage_products/sua.php?id=<?= $ID_SanPham ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Tên sản phẩm</label>
                    <input class="form-control <?= isset($errors['TenSanPham']) ? 'is-invalid' : ''; ?>" type="text" name="TenSanPham" value="<?= htmlspecialchars($form_data['TenSanPham'] ?? $product['TenSanPham'], ENT_QUOTES); ?>" />
                    <?php if (isset($errors['TenSanPham'])): ?>
                        <div class="invalid-feedback"><?= $errors['TenSanPham']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea class="form-control <?= isset($errors['MoTa']) ? 'is-invalid' : ''; ?>" name="MoTa"><?= htmlspecialchars($form_data['MoTa'] ?? $product['MoTa'], ENT_QUOTES); ?></textarea>
                    <?php if (isset($errors['MoTa'])): ?>
                        <div class="invalid-feedback"><?= $errors['MoTa']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="quantity">Số lượng</label>
                    <input class="form-control <?= isset($errors['SoLuong']) ? 'is-invalid' : ''; ?>" type="number" name="SoLuong" value="<?= htmlspecialchars($form_data['SoLuong'] ?? $product['SoLuong'], ENT_QUOTES); ?>" />
                    <?php if (isset($errors['SoLuong'])): ?>
                        <div class="invalid-feedback"><?= $errors['SoLuong']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="price">Giá bán</label>
                    <input class="form-control <?= isset($errors['GiaBan']) ? 'is-invalid' : ''; ?>" type="number" step="0.01" name="GiaBan" value="<?= htmlspecialchars($form_data['GiaBan'] ?? $product['GiaBan'], ENT_QUOTES); ?>" />
                    <?php if (isset($errors['GiaBan'])): ?>
                        <div class="invalid-feedback"><?= $errors['GiaBan']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="category">Danh mục</label>
                    <select class="form-control" name="danhmuc">
                        <?php while ($row_DanhMuc = $query_DanhMuc->fetch_assoc()) { ?>
                        <option value="<?= $row_DanhMuc['ID_DanhMuc']; ?>" <?= ($form_data['danhmuc'] ?? $product['ID_DanhMuc']) == $row_DanhMuc['ID_DanhMuc'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($row_DanhMuc['TenDanhMuc'], ENT_QUOTES); ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="supplier">Nhà cung cấp</label>
                    <select class="form-control" name="nhacungcap">
                        <?php while ($row_NCC = $query_NCC->fetch_assoc()) { ?>
                        <option value="<?= $row_NCC['ID_NCC']; ?>" <?= ($form_data['nhacungcap'] ?? $product['ID_NhaCungCap']) == $row_NCC['ID_NCC'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($row_NCC['TenNCC'], ENT_QUOTES); ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="formFile">Hình ảnh chính:</label>
                    <?php if (!empty($product['Img'])): ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center;" src="../assets/image/product/<?= htmlspecialchars($product['Img']); ?>" alt="Hình ảnh sản phẩm">
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($product['Img']); ?>">
                    <?php else: ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center; display: none;" src="#" alt="Hình ảnh sản phẩm">
                    <?php endif; ?>
                    <input class="form-control" type="file" name="image" accept="image/*" onchange="previewImage()">
                </div>
                <div class="form-group">
                    <label for="additionalImages">Hình ảnh mô tả:</label>
                    <div id="additionalImagesContainer" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php while ($imgRow = $query_Images->fetch_assoc()) { ?>
                            <div class="image-item" style="display: flex; flex-direction: column; align-items: center;">
                                <img style="width: 100px; height: 100px; object-fit: cover; object-position: center center;" src="../assets/image/product/<?= htmlspecialchars($imgRow['Anh']); ?>" alt="Ảnh phụ">
                                <input type="hidden" name="existing_images[]" value="<?= htmlspecialchars($imgRow['Anh']); ?>">
                                <button type="button" class="btn btn-danger btn-sm remove-image" data-image="<?= htmlspecialchars($imgRow['Anh']); ?>">Xóa</button>
                            </div>
                        <?php } ?>
                    </div>
                    <input class="form-control" type="file" name="additional_images[]" accept="image/*" multiple onchange="previewAdditionalImages()">
                    <small class="form-text text-muted">Chọn nhiều ảnh để tải lên thêm ảnh phụ.</small>
                </div>

                <div class="form-group">
                    <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
                    <a href="index.php?product=list-product" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Xóa thông báo lỗi và dữ liệu sau khi hiển thị -->
<?php
unset($_SESSION['errors']);
unset($_SESSION['data']);
?>

<!-- Các đoạn script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewImage() {
        var fileInput = document.querySelector('input[name="image"]');
        var file = fileInput.files[0];
        var preview = document.getElementById('imagePreview');
        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
            preview.style.display = 'block'; // Hiển thị ảnh khi có ảnh mới
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "<?php echo (!empty($product['Img'])) ? '../assets/image/product/' . htmlspecialchars($product['Img']) : '#'; ?>";
            preview.style.display = '<?php echo (!empty($product['Img'])) ? 'block' : 'none'; ?>';
        }
    }

    function previewAdditionalImages() {
        var fileInput = document.querySelector('input[name="additional_images[]"]');
        var files = fileInput.files;
        var container = document.getElementById('additionalImagesContainer');
        container.innerHTML = ''; // Xóa các hình ảnh hiện có

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();

            reader.onloadend = function (e) {
                var img = document.createElement('img');
                img.style.width = '100px';
                  img.style.height = '100px';
                img.style.objectFit = 'cover';
                 img.style.objectPosition = 'center center';
                img.src = e.target.result;

                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn btn-danger btn-sm remove-image';
                button.textContent = 'Xóa';
                button.addEventListener('click', function () {
                    img.parentElement.remove();
                });

                        var div = document.createElement('div');
                        div.className = 'image-item';
                        div.style.display = 'flex';
                          div.style.flexDirection = 'column';
                        div.style.alignItems = 'center';
                         div.appendChild(img);
                        div.appendChild(button);
                        container.appendChild(div);
                    };

            reader.readAsDataURL(file);
        }
    }

    
</script>
<script>
    $(document).ready(function () {
        $('.remove-image').on('click', function () {
            var imageName = $(this).data('image');
            var imageItem = $(this).closest('.image-item');

            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa ảnh này?',
                text: "Bạn sẽ không thể khôi phục ảnh này!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Có',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'modules/manage_products/delete_image.php',
                        type: 'POST',
                        data: { image: imageName },
                        success: function (response) {
                            var data = JSON.parse(response);
                            if (data.success) {
                                imageItem.remove(); // Xóa ảnh khỏi DOM
                                Swal.fire(
                                    'Xóa thành công!',
                                    data.message,
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Lỗi!',
                                    data.message,
                                    'error'
                                );
                            }
                        },
                        error: function () {
                            Swal.fire(
                                'Lỗi!',
                                'Không thể thực hiện yêu cầu xóa.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
<style>
 #wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 60px;
 }</style>