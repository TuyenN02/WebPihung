<?php 


// Khởi tạo biến lỗi và dữ liệu form từ session
$errors = $_SESSION['errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['errors']);
unset($_SESSION['form_data']);

// Truy vấn danh mục và nhà cung cấp
$sql_DanhMuc = "SELECT * FROM danhmuc ORDER BY ID_DanhMuc DESC";
$query_DanhMuc = mysqli_query($mysqli, $sql_DanhMuc);

$sql_NCC = "SELECT * FROM nhacungcap ORDER BY ID_NCC DESC";
$query_NCC = mysqli_query($mysqli, $sql_NCC);

// Đường dẫn ảnh đã tải lên nếu có
$image_preview = isset($form_data['Img']) ? '../../../assets/image/product/' . htmlspecialchars($form_data['Img'], ENT_QUOTES) : '';
$image_display = !empty($image_preview) ? 'block' : 'none';

// Đường dẫn ảnh mô tả đã tải lên nếu có
$image_desc_previews = isset($form_data['ImgDesc']) ? $form_data['ImgDesc'] : [];
$image_desc_display = !empty($image_desc_previews) ? 'block' : 'none';
// Đóng kết nối

?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold">
            Thêm sản phẩm
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form action="modules/manage_products/add.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name">Tên sản phẩm</label>
                            <input required class="form-control <?php echo isset($errors['empty_name']) || isset($errors['name_length']) || isset($errors['product_name_length']) || isset($errors['duplicate_name']) ? 'is-invalid' : ''; ?>" 
                                   type="text" name="TenSanPham" id="name" value="<?php echo htmlspecialchars($form_data['TenSanPham'] ?? '', ENT_QUOTES); ?>">
                            <?php if (isset($errors['empty_name']) || isset($errors['name_length']) || isset($errors['product_name_length']) || isset($errors['duplicate_name'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['empty_name'] ?? $errors['name_length'] ?? $errors['product_name_length'] ?? $errors['duplicate_name']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="price">Giá</label>
                            <input required class="form-control <?php echo isset($errors['empty_price']) || isset($errors['invalid_price']) ? 'is-invalid' : ''; ?>" 
                                   type="text" name="GiaBan" id="price" value="<?php echo htmlspecialchars($form_data['GiaBan'] ?? '', ENT_QUOTES); ?>">
                            <?php if (isset($errors['empty_price']) || isset($errors['invalid_price'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['empty_price'] ?? $errors['invalid_price']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Số lượng</label>
                            <input required class="form-control <?php echo isset($errors['empty_quantity']) || isset($errors['invalid_quantity']) ? 'is-invalid' : ''; ?>" 
                                   type="text" name="SoLuong" id="quantity" value="<?php echo htmlspecialchars($form_data['SoLuong'] ?? '', ENT_QUOTES); ?>">
                            <?php if (isset($errors['empty_quantity']) || isset($errors['invalid_quantity'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['empty_quantity'] ?? $errors['invalid_quantity']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="intro">Mô tả sản phẩm</label>
                            <textarea required name="MoTa" class="form-control <?php echo isset($errors['empty_description']) ? 'is-invalid' : ''; ?>" 
                                      id="intro" cols="30" rows="5"><?php echo htmlspecialchars($form_data['MoTa'] ?? '', ENT_QUOTES); ?></textarea>
                            <?php if (isset($errors['empty_description'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['empty_description']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </div>
                <div class="form-group">
                    <label>Hình ảnh:</label>
                    <input required class="form-control" type="file" name="Img" accept=".jpg,.png" onchange="validateImage()">
                    <img id="preview" src="<?php echo $image_preview; ?>" style="max-width: 200px; margin-top: 10px; display: <?php echo $image_display; ?>;">
                </div>
                <div class="form-group">
                            <label>Ảnh khác:</label>
                            <input required class="form-control" type="file" name="ImgDesc[]" accept=".jpg,.png" multiple onchange="validateImagesDesc()">
                            <div id="preview-desc" style="margin-top: 10px;">
                                <?php foreach ($image_desc_previews as $image): ?>
                                    <img src="../../../assets/image/product/<?php echo htmlspecialchars($image, ENT_QUOTES); ?>" 
                                         style="max-width: 200px; margin-top: 10px; display: block;">
                                <?php endforeach; ?>
                            </div>
                        </div>
                <div class="form-group">
                    <label for="">Danh mục</label>
                    <select class="form-control <?php echo isset($errors['empty_category']) ? 'is-invalid' : ''; ?>" required name="danhmuc">
                        <option value="">Chọn danh mục</option>
                        <?php while ($row_DanhMuc = mysqli_fetch_array($query_DanhMuc)) { ?>
                        <option value="<?php echo $row_DanhMuc['ID_DanhMuc']?>" <?php echo (isset($form_data['danhmuc']) && $form_data['danhmuc'] == $row_DanhMuc['ID_DanhMuc']) ? 'selected' : ''; ?>>
                            <?php echo $row_DanhMuc['TenDanhMuc']?>
                        </option>
                        <?php } ?>
                    </select>
                    <?php if (isset($errors['empty_category'])): ?>
                        <div class="invalid-feedback">
                            <?php echo $errors['empty_category']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="">Nhà cung cấp</label>
                    <select class="form-control <?php echo isset($errors['empty_supplier']) ? 'is-invalid' : ''; ?>" required name="nhacungcap">
                        <option value="">Chọn nhà cung cấp</option>
                        <?php while ($row_NCC = mysqli_fetch_array($query_NCC)) { ?>
                        <option value="<?php echo $row_NCC['ID_NCC']?>" <?php echo (isset($form_data['nhacungcap']) && $form_data['nhacungcap'] == $row_NCC['ID_NCC']) ? 'selected' : ''; ?>>
                            <?php echo $row_NCC['TenNCC']?>
                        </option>
                        <?php } ?>
                    </select>
                    <?php if (isset($errors['empty_supplier'])): ?>
                        <div class="invalid-feedback">
                            <?php echo $errors['empty_supplier']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <input type="submit" class="btn btn-primary" value="Thêm mới" name="submit">
                <a href="index.php?product=list-product" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<script>
    function validateImage() {
        var fileInput = document.querySelector('input[name="Img"]');
        var filePath = fileInput.value;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        var preview = document.getElementById('preview');

        if (!allowedExtensions.exec(filePath)) {
            alert('Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.');
            fileInput.value = ''; // Clear the input
            preview.src = '';
            preview.style.display = 'none';
            return false;
        } else {
            // Image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }

    function validateImagesDesc() {
        var fileInput = document.querySelector('input[name="ImgDesc[]"]');
        var filePaths = fileInput.files;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        var previewDesc = document.getElementById('preview-desc');

        // Clear existing previews
        previewDesc.innerHTML = '';

        for (var i = 0; i < filePaths.length; i++) {
            var filePath = filePaths[i].name;
            
            if (!allowedExtensions.exec(filePath)) {
                alert('Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.');
                fileInput.value = ''; // Clear the input
                return false;
            } else {
                // Image preview
                var reader = new FileReader();
                reader.onload = (function(file) {
                    return function(e) {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '200px';
                        img.style.marginTop = '10px';
                        previewDesc.appendChild(img);
                    };
                })(filePaths[i]);
                reader.readAsDataURL(filePaths[i]);
            }
        }
    }
</script>
