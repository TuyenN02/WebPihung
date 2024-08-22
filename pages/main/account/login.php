<?php

$redirect = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Mã hóa mật khẩu
    $sql_login = "SELECT * FROM thanhvien WHERE TenDangNhap = '$username' AND MatKhau = '$password' LIMIT 1";
    $query_login = mysqli_query($mysqli, $sql_login);
    $count = mysqli_num_rows($query_login);

    if ($count > 0) {
        $row = mysqli_fetch_array($query_login);
        $id_cus = $row['ID_ThanhVien'];

        // Lấy thông tin giỏ hàng của người dùng
        $sql_cart = "SELECT * FROM giohang WHERE ID_ThanhVien = $id_cus";
        $query_cart = mysqli_query($mysqli, $sql_cart);
        $row_cart = mysqli_fetch_array($query_cart);

        // Lưu thông tin vào session
        $_SESSION['TenDangNhap'] = $username;
        $_SESSION['HoVaTen'] = $row['HoVaTen'];
        $_SESSION['ID_ThanhVien'] = $id_cus;
        $_SESSION['Email'] = $row['Email'];
        $_SESSION['ID_GioHang'] = $row_cart['ID_GioHang'];
        
        // Lưu thông báo thành công vào session
        $_SESSION['login_success'] = 'Đăng nhập thành công! Chào mừng bạn trở lại.';

        // Chuyển hướng đến trang chính
        $redirect = 'index.php';
    } else {
        // Nếu không có kết quả, thông báo lỗi
        $checkLogin = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .login-header {
            margin-bottom: 2rem;
        }
        .login-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            transition: color 0.3s ease;
        }
        .login-header h1:hover {
            color: #007bff;
        }
        .login-header p {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .alert-custom {
            border-radius: 0.5rem;
            padding: 1rem;
        }
    </style>
</head>
<body>
<section class="vh-100">
  <div class="container-fluid h-custom">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5">
        <img src="./assets/image/banner/login.png" class="img-fluid" alt="Sample image">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
        <div class="login-header text-center">
          <h1 class="display-4 font-weight-bold">Đăng Nhập</h1>
          <p class="lead text-muted">Vui lòng nhập tên đăng nhập và mật khẩu của bạn để tiếp tục.</p>
        </div>
        <form method="POST" action="">
          <div class="form-outline mb-4">
            <label for="username">Tên đăng nhập:</label>
            <input required type="text" id="username" class="form-control form-control-lg" name="username" placeholder="Enter username" />
          </div>
          <div class="form-outline mb-3">
            <label for="password">Mật khẩu:</label>
            <input required type="password" id="password" class="form-control form-control-lg" name="password" placeholder="Enter password" />
          </div>
          <?php if (isset($checkLogin)): ?>
            <div class="alert alert-danger alert-custom text-center" role="alert">
                <?php echo $checkLogin; ?>
            </div>
          <?php endif; ?>
          <div class="text-center text-lg-start mt-4 pt-2">
            <input type="submit" class="btn btn-primary btn-lg" name="login" style="padding-left: 2.5rem; padding-right: 2.5rem;" value="Đăng nhập">
            <p class="small font-weight-bold mt-2 pt-1 mb-0">Chưa có tài khoản? <a href="index.php?navigate=signup" class="text-danger">Đăng ký</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<?php if ($redirect): ?>
<script>
    window.location.href = '<?php echo $redirect; ?>';
</script>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
