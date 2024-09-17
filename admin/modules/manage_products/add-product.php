<?php 
// Truy vấn danh mục và nhà cung cấp
$sql_DanhMuc = "SELECT * FROM danhmuc ORDER BY ID_DanhMuc DESC";
$query_DanhMuc = mysqli_query($mysqli, $sql_DanhMuc);

$sql_NCC = "SELECT * FROM nhacungcap ORDER BY ID_NCC DESC";
$query_NCC = mysqli_query($mysqli, $sql_NCC);

// Ví dụ khởi tạo biến $data với giá trị mặc định
$data = [
    'TenSanPham' => '',
 
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
                <label for="GiaBan">Giá tiền:</label>
                <input type="text" class="form-control" id="GiaBan" name="GiaBan" oninput="getFormattedGiaTien(this)">
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
                    <small class="form-text" style="color: #c67777;">* Không bắt buộc</small> <!-- Dòng chữ nhỏ với màu đỏ -->
                </div>

                <div class="form-group">
                        <label for="Img" style="display: block;">Hình ảnh chính:</label>

                        <!-- Hiển thị hình ảnh xem trước -->
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center; display: none;" />

                        <!-- Nút chọn tệp tùy chỉnh, căn dưới label -->
                        <button type="button" class="btn btn-custom" style="margin-top: 10px;" onclick="document.getElementById('Img').click();">Chọn hình ảnh</button>

                        <!-- Input file ẩn -->
                        <input required class="form-control" type="file" name="Img" id="Img" accept="image/*" style="display: none;" onchange="previewImage()">

                        <!-- Hiển thị lỗi -->
                        <div id="ImgError" class="error-message"></div>
                    </div>


                <div class="form-group">
                    <label for="ImgDescriptions">Hình ảnh mô tả:</label>
                    
                    <!-- Hiển thị hình ảnh xem trước -->
                    <div id="descriptionImagesPreview" style="display: flex; gap: 10px;"></div>
                    
                    <!-- Nút chọn tệp tùy chỉnh -->
                    <button type="button" class="btn btn-custom" onclick="document.getElementById('ImgDescriptions').click();">Chọn hình ảnh</button>
                    
                    <!-- Input file ẩn -->
                    <input  class="form-control" type="file" name="ImgDescriptions[]" id="ImgDescriptions" accept="image/*" multiple style="display: none;" onchange="previewDescriptionImages()">
                    
                    <!-- Hiển thị lỗi -->
                    <div id="ImgDescriptionsError" class="error-message"></div>
                </div>
                <div class="form-group">
                    <label for="danhmuc">Danh mục:</label>
                    <select required class="form-control" name="danhmuc" id="danhmuc">
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
<!-- Thêm vào phần <head> của trang HTML -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('TenSanPham').addEventListener('input', function() {
    const value = this.value.trim();
    if (value.length < 3 || value.length > 50) {
        document.getElementById('TenSanPhamError').textContent = "Tên sản phẩm phải từ 3 đến 50 ký tự!";
    } else {
        document.getElementById('TenSanPhamError').textContent = "";
    }
});

document.getElementById('GiaBan').addEventListener('input', function() {
    const value = this.value;
    
    // Biểu thức chính quy kiểm tra giá trị chỉ chứa số và dấu chấm ngăn cách
    const pattern = /^[0-9]+(\.[0-9]{3})*$/;
    
    // Kiểm tra giá trị nhập vào
    if (!pattern.test(value)) {
        document.getElementById('GiaBanError').textContent = "Giá tiền không hợp lệ!";
    } else {
        document.getElementById('GiaBanError').textContent = "";
    }
});
document.getElementById('SoLuong').addEventListener('input', function() {
    const value = this.value;
    if (isNaN(value) || value <= 0) {
        document.getElementById('SoLuongError').textContent = "Số lượng không hợp lệ!";
    } else {
        document.getElementById('SoLuongError').textContent = "";
    }
});

document.getElementById('danhmuc').addEventListener('change', function() {
    const value = this.value;
    if (!value) {
        document.getElementById('danhmucError').textContent = "Vui lòng chọn danh mục!";
    } else {
        document.getElementById('danhmucError').textContent = "";
    }
});

document.getElementById('nhacungcap').addEventListener('change', function() {
    const value = this.value;
    if (!value) {
        document.getElementById('nhacungcapError').textContent = "Vui lòng chọn nhà cung cấp!";
    } else {
        document.getElementById('nhacungcapError').textContent = "";
    }
});
function getFormattedGiaTien() {
    // Lấy giá trị từ input
    let value = document.getElementById('GiaBan').value;

    // Kiểm tra nếu giá trị chứa ký tự không hợp lệ
    if (/[^0-9.,]/.test(value)) {
        document.getElementById('GiaBanError').textContent = "Giá tiền không hợp lệ!";
        return null;
    }

    // Chuyển thành dạng số
    value = Number(value);
    
    // Nếu giá trị không hợp lệ, trả về null
    if (isNaN(value) || value <= 0) {
        document.getElementById('GiaBanError').textContent = "Giá tiền không hợp lệ!";
        return null;
    }

    // Nếu giá trị hợp lệ, xóa thông báo lỗi và trả về giá trị
    document.getElementById('GiaBanError').textContent = "";
    return value;
}

document.getElementById('productForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Lấy giá trị từ form
    const tenSanPham = document.getElementById('TenSanPham').value.trim();
    const giaBan = getFormattedGiaTien(); // Sử dụng hàm để lấy giá tiền đã định dạng lại
    const soLuong = document.getElementById('SoLuong').value.trim();
    const moTa = document.getElementById('MoTa').value.trim();
    const danhMuc = document.getElementById('danhmuc').value;
    const nhaCungCap = document.getElementById('nhacungcap').value;
    const image = document.getElementById('Img').files[0];
    const descriptionImages = document.getElementById('ImgDescriptions').files;

    let hasError = false;

    // Kiểm tra lỗi
    if (tenSanPham.length < 3 || tenSanPham.length > 50) {
        document.getElementById('TenSanPhamError').textContent = "Tên sản phẩm phải từ 3 đến 50 ký tự!";
        hasError = true;
    } else {
        document.getElementById('TenSanPhamError').textContent = "";
    }

    if (giaBan === null) {
        hasError = true;
    }

    if (!soLuong || isNaN(soLuong) || soLuong <= 0) {
        document.getElementById('SoLuongError').textContent = "Số lượng phải là một số dương!";
        hasError = true;
    } else {
        document.getElementById('SoLuongError').textContent = "";
    }

    if (danhMuc === '') {
        document.getElementById('danhmucError').textContent = "Vui lòng chọn danh mục!";
        hasError = true;
    } else {
        document.getElementById('danhmucError').textContent = "";
    }

    if (nhaCungCap === '') {
        document.getElementById('nhacungcapError').textContent = "Vui lòng chọn nhà cung cấp!";
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
                alert(result.message); // Hiển thị thông báo lỗi chung
            }
        
    })
    .catch(error => {
        console.error('Có lỗi xảy ra:', error);
        alert('Đã xảy ra lỗi khi gửi dữ liệu.');
    });
});



function previewImage() {
    var fileInput = document.getElementById('Img');
    var file = fileInput.files[0];
    var preview = document.getElementById('imagePreview');
    
    // Các định dạng tệp hợp lệ
    var validExtensions = ['image/png', 'image/jpeg'];
    
    if (file) {
        // Kiểm tra định dạng tệp
        if (validExtensions.includes(file.type)) {
            var reader = new FileReader();
            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block'; // Hiển thị ảnh sau khi chọn
            };
            reader.readAsDataURL(file);
        } else {
            // Hiển thị thông báo lỗi nếu định dạng không hợp lệ
            Swal.fire({
                title: 'Lỗi định dạng!',
                text: 'Vui lòng chọn tệp có định dạng PNG, JPEG hoặc JPG!',
                icon: 'error',
                confirmButtonText: 'OK'
            });

            // Xóa giá trị của input file và ẩn ảnh preview
            fileInput.value = '';
            preview.src = "";
            preview.style.display = 'none';
        }
    } else {
        preview.src = "";
        preview.style.display = 'none'; // Ẩn ảnh nếu không có file
    }
}

// Mảng chứa các ảnh đã thêm trước đó (dữ liệu từ backend)
var validFiles = []; // Mảng chứa các tệp hợp lệ đã thêm
var existingImages = []; // Mảng chứa các ảnh đã có từ trước

// Hiển thị các ảnh đã thêm trước đó
function displayExistingImages() {
    var previewContainer = document.getElementById('descriptionImagesPreview');
    previewContainer.innerHTML = ''; // Xóa nội dung cũ

    existingImages.forEach(function(imageSrc, index) {
        var img = document.createElement('img');
        img.src = imageSrc;
        img.style.width = '150px';
        img.style.height = '150px';
        img.style.objectFit = 'cover';
        img.style.marginRight = '10px';

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-danger btn-sm remove-image';
        button.textContent = 'Xóa';
        button.dataset.index = index; // Gán index cho ảnh đã có

        // Sự kiện xóa ảnh
        button.addEventListener('click', function() {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa ảnh này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    var index = parseInt(button.dataset.index, 10);
                    existingImages.splice(index, 1); // Xóa ảnh khỏi mảng existingImages
                    img.parentElement.remove(); // Xóa ảnh khỏi giao diện

                    // Thông báo xóa thành công
                    Swal.fire({
                        title: 'Xóa thành công!',
                        text: 'Ảnh đã được xóa khỏi danh sách.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        var div = document.createElement('div');
        div.style.display = 'inline-block';
        div.style.textAlign = 'center';
        div.style.marginBottom = '10px';
        div.appendChild(img);
        div.appendChild(button);
        previewContainer.appendChild(div);
    });
}

// Hiển thị các ảnh mới thêm vào
function previewDescriptionImages() {
    var previewContainer = document.getElementById('descriptionImagesPreview');
    var fileInput = document.getElementById('ImgDescriptions');
    var newFiles = fileInput.files;
    var validExtensions = ['image/png', 'image/jpeg','image/jpg']; // Định dạng hợp lệ
    if (newFiles.length > 0) {
        for (var i = 0; i < newFiles.length; i++) {
            var file = newFiles[i];
            var reader = new FileReader();
        if (!validExtensions.includes(file.type)) {
            Swal.fire({
                title: 'Lỗi định dạng!',
                text: 'Vui lòng chọn tệp có định dạng PNG hoặc JPG.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            fileInput.value = ''; // Xóa tệp không hợp lệ
            return;
        }

            // Thêm tệp mới vào mảng validFiles mà không xóa các tệp đã có
            validFiles.push(file);

            reader.onload = (function(fileIndex) {
                return function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;

                    // Đặt kích thước ảnh cố định
                    img.style.width = '150px';
                    img.style.height = '150px';
                    img.style.objectFit = 'cover';
                    img.style.marginRight = '10px';

                    var button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-danger btn-sm remove-image';
                    button.textContent = 'Xóa';
                    button.dataset.index = validFiles.length - 1; // Gán index cho ảnh mới

                    // Sự kiện xóa ảnh mới
                    button.addEventListener('click', function() {
                        Swal.fire({
                            title: 'Xác nhận xóa?',
                            text: "Bạn có chắc chắn muốn xóa ảnh này?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Xác nhận',
                            cancelButtonText: 'Hủy'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var index = parseInt(button.dataset.index, 10);
                                validFiles.splice(index, 1); // Xóa ảnh khỏi mảng validFiles
                                img.parentElement.remove(); // Xóa ảnh khỏi giao diện
                                updateFileInput(); // Cập nhật lại file input

                                // Thông báo xóa thành công
                                Swal.fire({
                                    title: 'Xóa thành công!',
                                    text: 'Ảnh đã được xóa khỏi danh sách.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    });

                    var div = document.createElement('div');
                    div.style.display = 'inline-block';
                    div.style.textAlign = 'center';
                    div.style.marginBottom = '10px';
                    div.appendChild(img);
                    div.appendChild(button);
                    previewContainer.appendChild(div);
                };
            })(validFiles.length - 1); // Đảm bảo mỗi FileReader gắn liền với một file khác nhau

            reader.readAsDataURL(file);
        }
    }

    updateFileInput(); // Cập nhật lại file input
}

// Hàm cập nhật lại fileInput sau khi xóa ảnh
function updateFileInput() {
    var dataTransfer = new DataTransfer();
    validFiles.forEach(function(file) {
        dataTransfer.items.add(file);
    });
    document.getElementById('ImgDescriptions').files = dataTransfer.files;

    // Cập nhật lại dataset index cho các nút sau khi xóa ảnh
    var removeButtons = document.querySelectorAll('.remove-image');
    removeButtons.forEach((button, idx) => {
        button.dataset.index = idx; // Cập nhật lại chỉ số sau khi xóa
    });
}

// Gọi hàm để hiển thị ảnh đã có khi trang tải
displayExistingImages();

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
    /* Nút chọn file tùy chỉnh */
    .btn-custom {
        padding: 3px 6px;
        background-color: #8dddb4; /* Màu xanh lá cây nhạt */
        color: #666666;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 15px;
        text-align: center;
    }

    .btn-custom:hover {
        background-color: #76C776; /* Màu khi hover */
    }
</style>