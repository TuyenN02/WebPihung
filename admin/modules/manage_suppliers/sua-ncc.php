<?php
if (isset($_GET['id_NCC'])) {
    $ID_NCC = intval($_GET['id_NCC']); // Bảo mật: ép kiểu ID_NCC thành số nguyên
    $sql_getNCC = "SELECT * FROM nhacungcap WHERE ID_NCC=$ID_NCC";
    $query_getNCC = mysqli_query($mysqli, $sql_getNCC);

    if ($query_getNCC) {
        $row = mysqli_fetch_array($query_getNCC);

        // Sử dụng dữ liệu đã lưu trong session nếu có
        $TenNCC = isset($_SESSION['data']['TenNCC']) ? htmlspecialchars($_SESSION['data']['TenNCC']) : htmlspecialchars($row['TenNCC']);
        $SoDienThoai = isset($_SESSION['data']['SoDienThoai']) ? htmlspecialchars($_SESSION['data']['SoDienThoai']) : htmlspecialchars($row['SoDienThoai']);
        $Email = isset($_SESSION['data']['Email']) ? htmlspecialchars($_SESSION['data']['Email']) : htmlspecialchars($row['Email']);
        $DiaChi = isset($_SESSION['data']['DiaChi']) ? htmlspecialchars($_SESSION['data']['DiaChi']) : htmlspecialchars($row['DiaChi']);
        $MoTa = isset($_SESSION['data']['MoTa']) ? htmlspecialchars($_SESSION['data']['MoTa']) : htmlspecialchars($row['MoTa']);

        // Các lỗi
        $TenNCCError = isset($_SESSION['errors']['TenNCC']) ? $_SESSION['errors']['TenNCC'] : '';
        $phoneError = isset($_SESSION['errors']['SoDienThoai']) ? $_SESSION['errors']['SoDienThoai'] : '';
        $emailError = isset($_SESSION['errors']['Email']) ? $_SESSION['errors']['Email'] : '';
        $diaChiError = isset($_SESSION['errors']['DiaChi']) ? $_SESSION['errors']['DiaChi'] : '';
    } else {
        echo "Nhà cung cấp không tồn tại.";
        exit();
    }
} else {
    echo "ID nhà cung cấp không hợp lệ.";
    exit();
}
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?ncc=list-ncc">Quay lại</a>
            </button>
            <h5 class="m-0" style="flex-grow: 1; text-align: center; font-size: 30px;">Sửa nhà cung cấp</h5>
        </div>
        <div class="card-body">
            <form id="editSupplierForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TenNCC">Tên nhà cung cấp:</label>
                    <input class="form-control" type="text" name="TenNCC" id="TenNCC" value="<?php echo $TenNCC; ?>" required>
                    <div id="TenNCCError" class="error-message"><?php echo $TenNCCError; ?></div>
                </div>

                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea class="form-control" name="MoTa" id="MoTa" rows="5"><?php echo $MoTa; ?></textarea>
                    <small class="form-text" style="color: #c67777;">* Không bắt buộc</small> <!-- Dòng chữ nhỏ màu đỏ -->
                </div>

                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input class="form-control" type="email" name="Email" id="Email" value="<?php echo $Email; ?>">
                    <div id="EmailError" class="error-message"><?php echo $emailError; ?></div>
                </div>

                <div class="form-group">
                    <label for="SoDienThoai">Số điện thoại:</label>
                    <input class="form-control" type="text" name="SoDienThoai" id="SoDienThoai" value="<?php echo $SoDienThoai; ?>">
                    <div id="SoDienThoaiError" class="error-message"><?php echo $phoneError; ?></div>
                </div>

                <div class="form-group">
                    <label for="DiaChi">Địa chỉ:</label>
                    <input class="form-control" type="text" name="DiaChi" id="DiaChi" value="<?php echo $DiaChi; ?>">
                    <div id="DiaChiError" class="error-message"><?php echo $diaChiError; ?></div>
                </div>

                <div class="form-group">
    <label>Hình ảnh:</label>
    <div class="image-container">
        <?php if (!empty($row['Img'])): ?>
            <img id="imagePreview" src="../assets/image/supplier/<?php echo htmlspecialchars($row['Img']); ?>" style="width: 240px; height: 240px; object-fit: cover; object-position: center center;">
        <?php else: ?>
            <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center; display: none;" />
        <?php endif; ?>
        <input class="form-control" type="file" name="Img" id="Img" accept="image/*" onchange="previewImage()" style="display: none;">
        <label for="Img" class="btn btn-custom">Chọn hình ảnh</label> <!-- Nút chọn file tùy chỉnh -->
    </div>
    <div id="ImgError" class="error-message"><?php echo isset($ImgError) ? $ImgError : ''; ?></div>
</div>

                <input type="hidden" name="id_NCC" value="<?php echo $ID_NCC; ?>">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="index.php?ncc=list-ncc" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>

<?php
// Xóa thông báo lỗi và dữ liệu sau khi hiển thị
unset($_SESSION['errors']);
unset($_SESSION['data']);
?>

<script>
    // Bắt lỗi tên nhà cung cấp
document.getElementById('TenNCC').addEventListener('input', function () {
    const tenNCC = this.value.trim();
    if (tenNCC.length < 3 || tenNCC.length > 50) {
        document.getElementById('TenNCCError').textContent = "Tên nhà cung cấp phải từ 3 đến 50 ký tự.";
    } else {
        document.getElementById('TenNCCError').textContent = "";
    }
});

// Bắt lỗi email
document.getElementById('Email').addEventListener('input', function () {
    const email = this.value.trim();
    if (email.length < 4 || email.length > 255) {
        document.getElementById('EmailError').textContent = "Email không đúng định dạng!";
    } else {
        const [localPart, domain] = email.split('@');
        if (!localPart || localPart.length < 4 || domain.length < 3 || !/^[a-zA-Z0-9.-]+$/.test(domain) || domain.split('.').length < 2) {
            document.getElementById('EmailError').textContent = "Email không đúng định dạng!";
        } else {
            document.getElementById('EmailError').textContent = "";
        }
    }
});

// Bắt lỗi số điện thoại
document.getElementById('SoDienThoai').addEventListener('input', function () {
    const soDienThoai = this.value.trim();
    if (soDienThoai.length !== 10 || !/^\d{10}$/.test(soDienThoai) || !soDienThoai.startsWith('0')) {
        document.getElementById('SoDienThoaiError').textContent = "Số điện thoại phải có 10 chữ số và bắt đầu bằng số 0!";
    } else {
        document.getElementById('SoDienThoaiError').textContent = "";
    }
});

// Bắt lỗi địa chỉ
document.getElementById('DiaChi').addEventListener('input', function () {
    const diaChi = this.value.trim();
    if (diaChi === '') {
        document.getElementById('DiaChiError').textContent = "Địa chỉ không được để trống!";
    } else {
        document.getElementById('DiaChiError').textContent = "";
    }
});
document.getElementById('editSupplierForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Ngăn gửi form mặc định

    // Lấy giá trị từ form
    const tenNCC = document.getElementById('TenNCC').value.trim();
    const email = document.getElementById('Email').value.trim();
    const soDienThoai = document.getElementById('SoDienThoai').value.trim();
    const diaChi = document.getElementById('DiaChi').value.trim();
    const image = document.getElementById('Img').files[0];
    const moTa = document.getElementById('MoTa').value.trim();
    
    let hasError = false;

    // Kiểm tra lỗi
    if (tenNCC.length < 3 || tenNCC.length > 50) {
        document.getElementById('TenNCCError').textContent = "Tên nhà cung cấp phải từ 3 đến 50 ký tự.";
        hasError = true;
    } else {
        document.getElementById('TenNCCError').textContent = "";
    }

   // Kiểm tra email
   if (email.length < 4 || email.length > 255) {
    document.getElementById('EmailError').textContent = "Email không đúng định dạng!";
    hasError = true;
    } else {
        const [localPart, domain] = email.split('@');
        if (!localPart || localPart.length < 4 || domain.length < 3 || !/^[a-zA-Z0-9.-]+$/.test(domain) || domain.split('.').length < 2) {
            document.getElementById('EmailError').textContent = "Email không đúng định dạng!";
            hasError = true;
        } else {
            document.getElementById('EmailError').textContent = "";
        }
    }
    // Kiểm tra số điện thoại
    if (soDienThoai.length !== 10 || !/^\d{10}$/.test(soDienThoai) || !soDienThoai.startsWith('0')) {
        document.getElementById('SoDienThoaiError').textContent = "Số điện thoại phải có 10 chữ số và bắt đầu bằng số 0!";
        hasError = true;
    } else {
        document.getElementById('SoDienThoaiError').textContent = "";
    }

    if (diaChi === '') {
        document.getElementById('DiaChiError').textContent = "Địa chỉ không được để trống!";
        hasError = true;
    } else {
        document.getElementById('DiaChiError').textContent = "";
    }


    // Kiểm tra nếu có lỗi thì không gửi form
    if (hasError) {
        return;
    }

    // Nếu không có lỗi, tạo FormData và gửi yêu cầu
    const form = document.getElementById('editSupplierForm');
    const formData = new FormData(form);

    fetch('modules/manage_suppliers/sua.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        console.log('Fetch Result:', result); // Kiểm tra phản hồi từ máy chủ

        if (result.status === 'success') {
            window.location.href = "index.php?ncc=list-ncc"; // Chuyển hướng ngay lập tức
        }  else {
                alert(result.message); // Hiển thị thông báo lỗi khác nếu có
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
    var reader = new FileReader();

    reader.onloadend = function () {
        preview.src = reader.result;
        preview.style.display = 'block'; // Hiển thị ảnh khi có ảnh mới
    };

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "#";
        preview.style.display = 'none';
    }
}
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