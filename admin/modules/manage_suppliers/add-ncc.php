<?php


// Xóa dữ liệu cũ từ session khi người dùng truy cập trang này
unset($_SESSION['data']);
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Lấy dữ liệu từ session nếu có
$data = isset($_SESSION['data']) ? $_SESSION['data'] : [
    'TenNCC' => '',
    'MoTa' => '',
    'Email' => '',
    'SoDienThoai' => '',
    'DiaChi' => '',
];

// Hiển thị thông báo nếu có
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Xóa thông báo sau khi hiển thị
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?ncc=list-ncc">Quay lại</a>
            </button>
            <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Thêm Nhà Cung Cấp</h5>
        </div>
        <div class="card-body">
        <form id="addSupplierForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="TenNCC">Tên nhà cung cấp:</label>
                    <input required class="form-control" type="text" name="TenNCC" id="TenNCC" value="<?php echo htmlspecialchars($data['TenNCC']); ?>" >
                    <div id="TenNCCError" class="error-message"><?php echo isset($error_message['TenNCC']) ? $error_message['TenNCC'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea class="form-control" name="MoTa" id="MoTa" rows="5"><?php echo htmlspecialchars($data['MoTa']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input required class="form-control" type="email" name="Email" id="Email" value="<?php echo htmlspecialchars($data['Email']); ?>">
                    <div id="EmailError" class="error-message"><?php echo isset($error_message['Email']) ? $error_message['Email'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="SoDienThoai">Số điện thoại:</label>
                    <input required class="form-control" type="text" name="SoDienThoai" id="SoDienThoai" value="<?php echo htmlspecialchars($data['SoDienThoai']); ?>">
                    <div id="SoDienThoaiError" class="error-message"><?php echo isset($error_message['SoDienThoai']) ? $error_message['SoDienThoai'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="DiaChi">Địa chỉ:</label>
                    <input required class="form-control" type="text" name="DiaChi" id="DiaChi" value="<?php echo htmlspecialchars($data['DiaChi']); ?>">
                    <div id="DiaChiError" class="error-message"><?php echo isset($error_message['DiaChi']) ? $error_message['DiaChi'] : ''; ?></div>
                </div>

                <div class="form-group">
                    <label for="Img">Hình ảnh:</label>
                    <img id="imagePreview" style="width: 240px; height: 240px; object-fit: cover; object-position: center center; display: none;" />
                    <input required class="form-control" type="file" name="Img" id="Img" accept="image/*" onchange="previewImage()">
                    <div id="ImgError" class="error-message"></div>
                </div>

                <button type="submit" class="btn btn-primary">Thêm</button>
                <button type="button" id="cancelButton" class="btn btn-secondary">Hủy</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('addSupplierForm').addEventListener('submit', function(event) {
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
        document.getElementById('TenNCCError').textContent = "Tên nhà cung cấp phải từ 3 đến 50 ký tự!";
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
    const form = document.getElementById('addSupplierForm');
    const formData = new FormData(form);

    fetch('modules/manage_suppliers/add.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(result => {
        console.log('Fetch Result:', result); // Kiểm tra phản hồi từ máy chủ

        if (result.status === 'success') {
            // Reset form sau khi thêm thành công
            form.reset();
            document.getElementById('imagePreview').style.display = 'none'; // Ẩn hình ảnh xem trước

            // Xóa tất cả các thông báo lỗi
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

            // Chuyển hướng nếu cần
            window.location.href = "index.php?ncc=list-ncc";
        } else {
            // Hiển thị thông báo lỗi cho từng trường cụ thể
            if (result.message.includes('Email')) {
                document.getElementById('EmailError').textContent = result.message;
            } else if (result.message.includes('Số điện thoại')) {
                document.getElementById('SoDienThoaiError').textContent = result.message;
            } else {
                alert(result.message); // Hiển thị thông báo lỗi khác nếu có
            }
        }
    })
    .catch(error => {
        console.error('Có lỗi xảy ra:', error);
        alert('Đã xảy ra lỗi khi gửi dữ liệu.');
    });
});

document.getElementById('cancelButton').addEventListener('click', function() {
    // Reset form
    const form = document.getElementById('addSupplierForm');
    form.reset(); 

    // Ẩn hình ảnh xem trước
    document.getElementById('imagePreview').style.display = 'none';

    // Xóa tất cả các thông báo lỗi
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

    // Chuyển hướng về trang danh sách
    window.location.href = "index.php?ncc=list-ncc";
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
</style>
</body>
</html>