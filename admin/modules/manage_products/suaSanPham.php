<?php
if (isset($_GET['id'])) {
    $ID_SanPham = intval($_GET['id']); // Bảo mật: ép kiểu ID_SanPham thành số nguyên
    $sql_getSP = "SELECT * FROM sanpham WHERE ID_SanPham=$ID_SanPham";
    $query_getSP = mysqli_query($mysqli, $sql_getSP);

    if ($query_getSP) {
        $row = mysqli_fetch_array($query_getSP);

        // Sử dụng dữ liệu đã lưu trong session nếu có
        $TenSanPham = isset($_SESSION['data']['TenSanPham']) ? htmlspecialchars($_SESSION['data']['TenSanPham']) : htmlspecialchars($row['TenSanPham']);
        $MoTa = isset($_SESSION['data']['MoTa']) ? htmlspecialchars($_SESSION['data']['MoTa']) : htmlspecialchars($row['MoTa']);
        $GiaBan = isset($_SESSION['data']['GiaBan']) ? htmlspecialchars($_SESSION['data']['GiaBan']) : htmlspecialchars($row['GiaBan']);
        $SoLuong = isset($_SESSION['data']['SoLuong']) ? htmlspecialchars($_SESSION['data']['SoLuong']) : htmlspecialchars($row['SoLuong']);
        $ID_DanhMuc = isset($_SESSION['data']['ID_DanhMuc']) ? intval($_SESSION['data']['ID_DanhMuc']) : intval($row['ID_DanhMuc']);
        $ID_NhaCungCap = isset($_SESSION['data']['ID_NhaCungCap']) ? intval($_SESSION['data']['ID_NhaCungCap']) : intval($row['ID_NhaCungCap']);

        // Các lỗi
        $TenSanPhamError = isset($_SESSION['errors']['TenSanPham']) ? $_SESSION['errors']['TenSanPham'] : '';
        $GiaBanError = isset($_SESSION['errors']['GiaBan']) ? $_SESSION['errors']['GiaBan'] : '';
        $SoLuongError = isset($_SESSION['errors']['SoLuong']) ? $_SESSION['errors']['SoLuong'] : '';
           // Lấy danh sách ảnh mô tả
           $sql_getImages = "SELECT * FROM hinhanh_sanpham WHERE ID_SanPham=$ID_SanPham";
           $query_getImages = mysqli_query($mysqli, $sql_getImages);
    } else {
        echo "Sản phẩm không tồn tại.";
        exit();
    }
} else {
    echo "ID sản phẩm không hợp lệ.";
    exit();
}
// Lấy danh sách các nhà cung cấp
$sql_getNCC = "SELECT * FROM nhacungcap ORDER BY ID_NCC DESC";
$query_getNCC = mysqli_query($mysqli, $sql_getNCC);

// Lấy danh sách các danh mục
$sql_getDM = "SELECT * FROM danhmuc ORDER BY ID_DanhMuc DESC";
$query_getDM = mysqli_query($mysqli, $sql_getDM);

?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?product=list-product">Quay lại</a>
            </button>
            <h5 class="m-0" style="flex-grow: 1; text-align: center; font-size: 30px;">Sửa sản phẩm</h5>
        </div>
        <div class="card-body">
            <form id="editProductForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TenSanPham">Tên sản phẩm:</label>
                    <input class="form-control" type="text" name="TenSanPham" id="TenSanPham" value="<?php echo $TenSanPham; ?>" required>
                    <div id="TenSanPhamError" class="error-message"><?php echo $TenSanPhamError; ?></div>
                </div>

                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea class="form-control" name="MoTa" id="MoTa" rows="5"><?php echo $MoTa; ?></textarea>
                    <small class="form-text text-red">* Không bắt buộc</small> 
                </div>

                <div class="form-group">
            <label for="GiaBan">Giá tiền:</label>
            <input type="text" class="form-control" id="GiaBan" name="GiaBan" oninput="getFormattedGiaTien(this)" value="<?php echo isset($GiaBan) ? htmlspecialchars($GiaBan) : ''; ?>" required>
            <div id="GiaBanError" class="error-message"><?php echo isset($GiaBanError) ? $GiaBanError : ''; ?></div>
        </div>


                <div class="form-group">
                    <label for="SoLuong">Số lượng:</label>
                    <input class="form-control" type="number" name="SoLuong" id="SoLuong" value="<?php echo $SoLuong; ?>" required>
                    <div id="SoLuongError" class="error-message"><?php echo $SoLuongError; ?></div>
                </div>

                <div class="form-group">
                    <label for="ID_NhaCungCap">Nhà cung cấp:</label>
                    <select class="form-control" name="ID_NhaCungCap" id="ID_NhaCungCap" required>
                        <?php while ($ncc = mysqli_fetch_array($query_getNCC)) : ?>
                            <option value="<?php echo $ncc['ID_NCC']; ?>" <?php echo ($ncc['ID_NCC'] == $ID_NhaCungCap) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ncc['TenNCC']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ID_DanhMuc">Danh mục:</label>
                    <select class="form-control" name="ID_DanhMuc" id="ID_DanhMuc" required>
                        <?php while ($dm = mysqli_fetch_array($query_getDM)) : ?>
                            <option value="<?php echo $dm['ID_DanhMuc']; ?>" <?php echo ($dm['ID_DanhMuc'] == $ID_DanhMuc) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dm['TenDanhMuc']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Img" style="display: block;">Hình ảnh:</label>

                    <!-- Kiểm tra và hiển thị hình ảnh nếu có -->
                    <?php if (!empty($row['Img'])): ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center;" src="../assets/image/product/<?php echo htmlspecialchars($row['Img']); ?>" alt="Hình ảnh sản phẩm">
                    <?php else: ?>
                        <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center; display: none;" />
                    <?php endif; ?>

                    <!-- Nút chọn tệp tùy chỉnh -->
                    <button type="button" class="btn btn-custom" style="display: block; margin-top: 10px;" onclick="document.getElementById('Img').click();">Chọn hình ảnh</button>

                    <!-- Input file ẩn -->
                    <input class="form-control" type="file" name="Img" id="Img" accept="image/*" style="display: none;" onchange="previewImage()">

                    <!-- Hiển thị lỗi -->
                    <div id="ImgError" class="error-message"></div>
                </div>
                                <!-- Phần hình ảnh mô tả -->
                            <!-- Phần hình ảnh mô tả -->
                <div class="form-group">
                    <label>Hình ảnh mô tả:</label>

                    <!-- Hiển thị các hình ảnh mô tả hiện có -->
                    <div id="additionalImagesContainer" class="mb-3">
                        <?php while ($img = mysqli_fetch_array($query_getImages)) : ?>
                            <div class="image-item" style="display: inline-block; margin-right: 10px; text-align: center;">
                                <img style="width: 100px; height: 100px; object-fit: cover; display: block; margin-bottom: 5px;" src="../assets/image/product/<?php echo htmlspecialchars($img['Anh']); ?>" alt="Hình ảnh mô tả">
                                <button type="button" class="btn btn-danger remove-image" data-image="<?php echo htmlspecialchars($img['Anh']); ?>" >Xóa</button>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Nút chọn tệp tùy chỉnh -->
                    <button type="button" class="btn btn-custom" onclick="document.getElementById('ImgDescriptions').click();">Chọn hình ảnh</button>
                    
                    <!-- Input file ẩn -->
                    <input class="form-control" type="file" name="ImgDescriptions[]" id="ImgDescriptions" accept="image/*" multiple style="display: none;" onchange="previewAdditionalImages()">
                </div>
                <input type="hidden" name="id" value="<?php echo $ID_SanPham; ?>">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="index.php?product=list-product" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
<!-- Thêm vào phần <head> của trang HTML -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
// Xóa thông báo lỗi và dữ liệu sau khi hiển thị
unset($_SESSION['errors']);
unset($_SESSION['data']);
?>

<script>
 // Hàm để định dạng giá tiền
// Hàm kiểm tra lỗi giá tiền mà không tự động thêm dấu chấm
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
document.getElementById('TenSanPham').addEventListener('input', function() {
    const value = this.value.trim();
    if (value.length < 3 || value.length > 50) {
        document.getElementById('TenSanPhamError').textContent = "Tên sản phẩm phải từ 3 đến 50 ký tự!";
    } else {
        document.getElementById('TenSanPhamError').textContent = "";
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


document.getElementById('editProductForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Lấy giá trị từ form
    const tenSanPham = document.getElementById('TenSanPham').value.trim();
    const giaBan = getFormattedGiaTien(); // Sử dụng hàm để lấy giá tiền đã định dạng lại
    const soLuong = document.getElementById('SoLuong').value.trim();
    const moTa = document.getElementById('MoTa').value.trim();
    const image = document.getElementById('Img').files[0];

    let hasError = false;

    // Kiểm tra nếu có lỗi thì không gửi form
    if (hasError) {
        return;
    }

    // Nếu không có lỗi, tạo FormData và gửi yêu cầu
    const form = document.getElementById('editProductForm');
    const formData = new FormData(form);

    fetch('modules/manage_products/sua.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        console.log('Fetch Result:', result); // Kiểm tra phản hồi từ máy chủ

        if (result.status === 'success') {
            window.location.href = "index.php?product=list-product"; // Chuyển hướng ngay lập tức
        } else {
                alert(result.message); // Hiển thị thông báo lỗi khác nếu có
            }
        
    })
    .catch(error => {
        console.error('Có lỗi xảy ra:', error);
        alert('Đã xảy ra lỗi khi gửi dữ liệu.');
    });
});
var validFiles = []; // Mảng chứa các tệp hợp lệ đã thêm

function previewAdditionalImages() {
    const container = document.getElementById('additionalImagesContainer');
    container.innerHTML = ''; // Xóa các hình ảnh cũ
    const files = document.querySelector('input[name="ImgDescriptions[]"]').files;
    Array.from(files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement('img');
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.src = e.target.result;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-danger remove-image';
            btn.textContent = 'Xóa';
            btn.onclick = function () {
                container.removeChild(img.parentElement);
            };
            const div = document.createElement('div');
            div.className = 'image-item';
            div.appendChild(img);
            div.appendChild(btn);
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
// Hàm cập nhật fileInput sau khi xóa ảnh
function updateFileInput() {
    var dataTransfer = new DataTransfer(); // Sử dụng DataTransfer để cập nhật giá trị của file input
    validFiles.forEach(function (file) {
        dataTransfer.items.add(file);
    });
    document.querySelector('input[name="ImgDescriptions[]"]').files = dataTransfer.files;

    // Cập nhật lại dataset index cho các nút sau khi xóa ảnh
    var removeButtons = document.querySelectorAll('.remove-image');
    removeButtons.forEach((button, idx) => {
        button.dataset.index = idx; // Cập nhật lại chỉ số sau khi xóa
    });
}


function previewImage() {
    var fileInput = document.getElementById('Img');
    var file = fileInput.files[0];
    var preview = document.getElementById('imagePreview');
    var reader = new FileReader();

    // Các định dạng tệp hợp lệ
    var validExtensions = ['image/png', 'image/jpeg'];

    // Kiểm tra định dạng tệp
    if (file && validExtensions.includes(file.type)) {
        reader.onloadend = function () {
            preview.src = reader.result;
            preview.style.display = 'block'; // Hiển thị ảnh khi có ảnh mới
        };

        reader.readAsDataURL(file);
    } else {
        // Hiển thị thông báo lỗi nếu tệp không hợp lệ
        Swal.fire({
            title: 'Lỗi định dạng!',
            text: 'Vui lòng chọn tệp có định dạng PNG hoặc JPG.',
            icon: 'error',
            confirmButtonText: 'OK'
        });

        // Xóa giá trị của input file và ẩn ảnh preview
        fileInput.value = '';
        preview.src = "#";
        preview.style.display = 'none';
    }
}

// Hiển thị nhiều ảnh trước khi lưu

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
    margin-top: 70px;
}
.error-message {
    color: red;
    font-size: 0.875em;
}
.text-red {
    color: #c67777;
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
