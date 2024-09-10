<?php
// Khởi tạo các biến để tránh lỗi "Undefined variable"
$username = $password = $password_repeat = $email = $fullname = $address = $phonenumber = "";
$username_err = $password_err = $password_repeat_err = $email_err = $fullname_err = $address_err = $phonenumber_err = "";
$checkRegister = "";

// Nhận dữ liệu từ người dùng
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_repeat = $_POST['password-repeat'];
    $email = trim($_POST['email']);
    $fullname = trim($_POST['fullname']);
    $address = trim($_POST['address']);
    $phonenumber = trim($_POST['phonenumber']);
    $NgayDangKi = date("Y-m-d H:i:s");

    // Mẫu regex để kiểm tra định dạng email hợp lệ
    $email_pattern = "/^[a-zA-Z0-9]{4,}[a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
   // Mẫu regex để kiểm tra số điện thoại hợp lệ
$phonenumber_pattern = "/^0[0-9]{9}$/";

    // Kiểm tra các trường có được điền đầy đủ không
    if (!empty($username) && !empty($password) && !empty($password_repeat) && !empty($email) && !empty($fullname) && !empty($address) && !empty($phonenumber)) {

        // Kiểm tra độ dài của tên và địa chỉ
        if (strlen($fullname) < 2 || strlen($fullname) > 50) {
            $fullname_err = "Tên phải từ 2 đến 50 ký tự!";
        } elseif (strlen($address) < 10 || strlen($address) > 100) {
            $address_err = "Địa chỉ phải từ 10 đến 100 ký tự!";
        
        // Kiểm tra mật khẩu nhập lại có trùng khớp không
        } elseif ($password != $password_repeat) {
            $password_repeat_err = "Mật khẩu không trùng khớp!";
        
        // Kiểm tra định dạng email
        } elseif (!preg_match($email_pattern, $email)) {
            $email_err = "Email không đúng định dạng!";
        
        // Kiểm tra số điện thoại có hợp lệ không
        } elseif (!preg_match("/^[0-9]{10,12}$/", $phonenumber)) {
            $phonenumber_err = "Số điện thoại không đúng định dạng!";
        
        // Kiểm tra trùng lặp tên đăng nhập
        } else {
            $sql_check_username = "SELECT * FROM thanhvien WHERE TenDangNhap = '$username'";
            $result_username = mysqli_query($mysqli, $sql_check_username);
            if (mysqli_num_rows($result_username) > 0) {
                $username_err = "Tên đăng nhập đã tồn tại!";
            }

            // Kiểm tra trùng lặp email
            $sql_check_email = "SELECT * FROM thanhvien WHERE Email = '$email'";
            $result_email = mysqli_query($mysqli, $sql_check_email);
            if (mysqli_num_rows($result_email) > 0) {
                $email_err = "Email đã tồn tại!";
            }

            // Kiểm tra trùng lặp số điện thoại
            $sql_check_phonenumber = "SELECT * FROM thanhvien WHERE SoDienThoai = '$phonenumber'";
            $result_phonenumber = mysqli_query($mysqli, $sql_check_phonenumber);
            if (mysqli_num_rows($result_phonenumber) > 0) {
                $phonenumber_err = "Số điện thoại đã tồn tại!";
            }
            // Kiểm tra số điện thoại có hợp lệ không
            if (!preg_match($phonenumber_pattern, $phonenumber)) {
              $phonenumber_err = "Số điện thoại không đúng định dạng!";
            }
            // Nếu không có lỗi, tiến hành lưu vào cơ sở dữ liệu
            if (empty($username_err) && empty($password_err) && empty($password_repeat_err) && empty($email_err) && empty($fullname_err) && empty($address_err) && empty($phonenumber_err)) {
                // Thêm thông tin thành viên vào bảng `thanhvien`
                $sql_add = "INSERT INTO thanhvien(TenDangNhap,MatKhau,Email,HoVaTen,DiaChi,SoDienThoai,NgayDangKi)
                            VALUES('$username', '".md5($password)."', '$email', '$fullname', '$address', '$phonenumber', '$NgayDangKi')";
                mysqli_query($mysqli, $sql_add);
                
                // Lấy ID của thành viên vừa được thêm vào
                $id_thanhvien = mysqli_insert_id($mysqli);
                
                // Thêm giỏ hàng rỗng cho thành viên
                $sql_insert_giohang = "INSERT INTO giohang(ID_ThanhVien) VALUES ($id_thanhvien)";
                mysqli_query($mysqli, $sql_insert_giohang);

                // Thông báo đăng ký thành công và chuyển hướng tới trang đăng nhập
                $checkRegister = "Đăng kí thành công!";
                echo "<script>
                alert('Đăng ký thành công'); 
                window.location.href='index.php?navigate=login';
                </script>";
                exit(); // Dừng script sau khi chuyển hướng
            }
        }
    } else {
        $checkRegister = "Vui lòng điền đầy đủ thông tin";
    }
}
?>

<section>
  <div class="container h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-12 col-xl-11">
        <div class="card text-black mt-4 mb-4" style="border-radius: 25px;">
          <div class="card-body p-md-5">
            <div class="row justify-content-center">
              <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
                <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Đăng ký</p>
                <p class="text-center text-danger">
                  <?php if (!empty($checkRegister)) {
                    echo $checkRegister;
                  } ?>
                </p>
                <form class="mx-1 mx-md-4" action="" method="POST">
                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fa fa-user fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <input type="text" id="form3Example1c" class="form-control" required name="fullname" placeholder="Họ tên" value="<?php echo htmlspecialchars($fullname); ?>"/>
                      <small class="text-danger"><?php echo $fullname_err; ?></small>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fa fa-home fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <input type="text" class="form-control" required name="address" placeholder="Địa chỉ" value="<?php echo htmlspecialchars($address); ?>"/>
                      <small class="text-danger"><?php echo $address_err; ?></small>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <input type="email" class="form-control" required name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>"/>
                      <small class="text-danger"><?php echo $email_err; ?></small>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fa fa-phone fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <input type="text" class="form-control" required name="phonenumber" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($phonenumber); ?>"/>
                      <small class="text-danger"><?php echo $phonenumber_err; ?></small>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fa fa-user-circle fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <input type="text" class="form-control" required name="username" placeholder="Tên đăng nhập" value="<?php echo htmlspecialchars($username); ?>"/>
                      <small class="text-danger"><?php echo $username_err; ?></small>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <input type="password" class="form-control" required name="password" placeholder="Mật khẩu"/>
                      <small class="text-danger"><?php echo $password_err; ?></small>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-key fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <input type="password" class="form-control" required name="password-repeat" placeholder="Nhập lại mật khẩu"/>
                      <small class="text-danger"><?php echo $password_repeat_err; ?></small>
                    </div>
                  </div>

                  <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                    <button type="submit" name="submit" class="btn btn-primary btn-lg">Đăng ký</button>
                  </div>
                </form>
              </div>
              <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                <img src="./assets/image/banner/signup.png" class="img-fluid" alt="Sample image">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
