<?php 
// Truy vấn danh mục và nhà cung cấp
$sql_DanhMuc = "SELECT * FROM danhmuc ORDER BY ID_DanhMuc DESC";
$query_DanhMuc = mysqli_query($mysqli, $sql_DanhMuc);

$sql_NCC = "SELECT * FROM nhacungcap ORDER BY ID_NCC DESC";
$query_NCC = mysqli_query($mysqli, $sql_NCC);

// Ví dụ khởi tạo biến $data với giá trị mặc định
$data = [
    'TenSanPham' => '',
    'GiaBan' => '',
    'SoLuong' => '',
    'MoTa' => '',
    'danhmuc' => '',
    'nhacungcap' => '',
];
// Hiển thị thông báo nếu có
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Xóa thông báo sau khi hiển thị
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
unset($_SESSION['data']);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?product=list-product">Quay lại</a>
            </button>
            <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Thêm Sản Phẩm</h5>
        </div>
        <div class="card-body">
            <form id="productForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TenSanPham">Tên sản phẩm:</label>
                    <input class="form-control" type="text" name="TenSanPham" id="TenSanPham" value="<?php echo htmlspecialchars($data['TenSanPham']); ?>" required>
                    <div id="TenSanPhamError" class="error-message"><?php echo isset($error_message['TenSanPham']) ? $error_message['TenSanPham'] : ''; ?></div>
                </div>
             
                <div class="form-group">
                    <label for="GiaBan">Giá:</label>
                    <input class="form-control" type="text" name="GiaBan" id="GiaBan" value="<?php echo htmlspecialchars($data['GiaBan']); ?>" required>
                    <div id="GiaBanError" class="error-message"><?php echo isset($error_message['GiaBan']) ? $error_message['GiaBan'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="SoLuong">Số lượng:</label>
                    <input class="form-control" type="text" name="SoLuong" id="SoLuong" value="<?php echo htmlspecialchars($data['SoLuong']); ?>" required>
                    <div id="SoLuongError" class="error-message"><?php echo isset($error_message['SoLuong']) ? $error_message['SoLuong'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea class="form-control" name="MoTa" id="MoTa" rows="5"><?php echo htmlspecialchars($data['MoTa']); ?></textarea>
                    <div id="MoTaError" class="error-message"><?php echo isset($error_message['MoTa']) ? $error_message['MoTa'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="Img">Hình ảnh chính:</label>
                    <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center; display: none;" />
                    <input class="form-control" type="file" name="Img" id="Img" accept="image/*" onchange="previewImage()">
                    <div id="ImgError" class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="ImgDescriptions">Hình ảnh mô tả:</label>
                    <div id="descriptionImagesPreview"></div>
                    <input class="form-control" type="file" name="ImgDescriptions[]" id="ImgDescriptions" accept="image/*" multiple onchange="previewDescriptionImages()">
                    <div id="ImgDescriptionsError" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label for="danhmuc">Danh mục:</label>
                    <select class="form-control" name="danhmuc" id="danhmuc">
                        <option value="">Chọn danh mục</option>
                        <?php while ($row_DanhMuc = mysqli_fetch_array($query_DanhMuc)) { ?>
                            <option value="<?php echo $row_DanhMuc['ID_DanhMuc']?>" <?php echo (isset($data['danhmuc']) && $data['danhmuc'] == $row_DanhMuc['ID_DanhMuc']) ? 'selected' : ''; ?>>
                                <?php echo $row_DanhMuc['TenDanhMuc']?>
                            </option>
                        <?php } ?>
                    </select>
                    <div id="danhmucError" class="error-message"><?php echo isset($error_message['danhmuc']) ? $error_message['danhmuc'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="nhacungcap">Nhà cung cấp:</label>
                    <select class="form-control" name="nhacungcap" id="nhacungcap">
                        <option value="">Chọn nhà cung cấp</option>
                        <?php while ($row_NCC = mysqli_fetch_array($query_NCC)) { ?>
                            <option value="<?php echo $row_NCC['ID_NCC']?>" <?php echo (isset($data['nhacungcap']) && $data['nhacungcap'] == $row_NCC['ID_NCC']) ? 'selected' : ''; ?>>
                                <?php echo $row_NCC['TenNCC']?>
                            </option>
                        <?php } ?>
                    </select>
                    <div id="nhacungcapError" class="error-message"><?php echo isset($error_message['nhacungcap']) ? $error_message['nhacungcap'] : ''; ?></div>
                </div>

                <button type="submit" class="btn btn-primary">Thêm mới</button>
                <a href="index.php?product=list-product" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('productForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Lấy giá trị từ form
    const tenSanPham = document.getElementById('TenSanPham').value.trim();
    const giaBan = document.getElementById('GiaBan').value.trim();
    const soLuong = document.getElementById('SoLuong').value.trim();
    const moTa = document.getElementById('MoTa').value.trim();
    const danhMuc = document.getElementById('danhmuc').value;
    const nhaCungCap = document.getElementById('nhacungcap').value;
    const image = document.getElementById('Img').files[0];
    const descriptionImages = document.getElementById('ImgDescriptions').files;

    let hasError = false;

    // Kiểm tra lỗi
    if (tenSanPham.length < 3 || tenSanPham.length > 50) {
        document.getElementById('TenSanPhamError').textContent = "Tên sản phẩm phải từ 3 đến 50 ký tự.";
        hasError = true;
    } else {
        document.getElementById('TenSanPhamError').textContent = "";
    }

    if (!giaBan || isNaN(giaBan) || giaBan <= 0) {
        document.getElementById('GiaBanError').textContent = "Giá phải là một số dương.";
        hasError = true;
    } else {
        document.getElementById('GiaBanError').textContent = "";
    }

    if (!soLuong || isNaN(soLuong) || soLuong <= 0) {
        document.getElementById('SoLuongError').textContent = "Số lượng phải là một số dương.";
        hasError = true;
    } else {
        document.getElementById('SoLuongError').textContent = "";
    }

    if (danhMuc === '') {
        document.getElementById('danhmucError').textContent = "Vui lòng chọn danh mục.";
        hasError = true;
    } else {
        document.getElementById('danhmucError').textContent = "";
    }

    if (nhaCungCap === '') {
        document.getElementById('nhacungcapError').textContent = "Vui lòng chọn nhà cung cấp.";
        hasError = true;
    } else {
        document.getElementById('nhacungcapError').textContent = "";
    }

    // Kiểm tra nếu có lỗi thì không gửi form
    if (hasError) {
        return;
    }

    // Nếu không có lỗi, tạo FormData và gửi yêu cầu
    const form = document.getElementById('productForm');
    const formData = new FormData(form);

    fetch('modules/manage_products/add.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        console.log('Fetch Result:', result); // Kiểm tra phản hồi từ máy chủ

        if (result.status === 'success') {
            window.location.href = result.redirect; // Chuyển hướng nếu thành công
        } else {
            // Hiển thị thông báo lỗi cho từng trường cụ thể
            if (result.message.includes('Tên sản phẩm đã tồn tại')) {
                document.getElementById('TenSanPhamError').textContent = result.message;
            } else {
                alert(result.message); // Hiển thị thông báo lỗi chung
            }
        }
    })
    .catch(error => {
        console.error('Có lỗi xảy ra:', error);
        alert('Đã xảy ra lỗi khi gửi dữ liệu.');
    });
});
function previewImage() {
    var file = document.getElementById('Img').files[0];
    var preview = document.getElementById('imagePreview');
    
    if (file) {
        var reader = new FileReader();
        reader.onloadend = function() {
            preview.src = reader.result;
            preview.style.display = 'block'; // Hiển thị ảnh sau khi chọn
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
        preview.style.display = 'none'; // Ẩn ảnh nếu không có file
    }
}
function previewDescriptionImages() {
    var previewContainer = document.getElementById('descriptionImagesPreview');
    var files = document.getElementById('ImgDescriptions').files;
    previewContainer.innerHTML = '';

    if (files.length > 0) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();

            reader.onload = (function(file) {
                return function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '150px'; // Kích thước tối đa của ảnh
                    img.style.marginRight = '10px'; // Khoảng cách giữa các ảnh
                    previewContainer.appendChild(img);
                };
            })(file); // Đảm bảo rằng mỗi FileReader gắn liền với một file khác nhau

            reader.readAsDataURL(file);
        }
    }
}

document.getElementById('Img').addEventListener('change', previewImage);

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