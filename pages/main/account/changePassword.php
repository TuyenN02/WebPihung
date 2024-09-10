<?php
$ID_ThanhVien = $_SESSION['ID_ThanhVien'];
$sql_Cus = "SELECT * FROM thanhvien WHERE ID_ThanhVien = $ID_ThanhVien";
$query_Cus = mysqli_query($mysqli, $sql_Cus);
$row = mysqli_fetch_array($query_Cus);

$errors = array(); // Mảng lưu lỗi

// Nếu có lỗi, ưu tiên hiển thị giá trị từ $_POST
$oldPassword = isset($_POST['old-password']) ? $_POST['old-password'] : '';
$newPassword = isset($_POST['new-password']) ? $_POST['new-password'] : '';
$newPasswordRepeat = isset($_POST['new-password-repeat']) ? $_POST['new-password-repeat'] : '';

if (isset($_POST['sua'])) {
    // Kiểm tra mật khẩu cũ
    if (empty($oldPassword)) {
        $errors['old-password'] = "Mật khẩu cũ không được để trống.";
    } else {
        $oldPasswordHashed = md5($oldPassword);
        $MatKhau = $row['MatKhau'];
        if ($oldPasswordHashed != $MatKhau) {
            $errors['old-password'] = "Mật khẩu cũ không chính xác!";
        }
    }

    // Kiểm tra mật khẩu mới
    if (empty($newPassword)) {
        $errors['new-password'] = "Mật khẩu mới không được để trống.";
    } elseif ( strlen($newPassword) >= 20) {
        $errors['new-password'] = "Mật khẩu không quá 20 ký tự.";
    }

    // Kiểm tra mật khẩu nhập lại
    if (empty($newPasswordRepeat)) {
        $errors['new-password-repeat'] = "Nhập lại mật khẩu không được để trống.";
    } elseif ($newPassword != $newPasswordRepeat) {
        $errors['new-password-repeat'] = "Mật khẩu không trùng khớp.";
    }

    if (empty($errors)) {
        $newPasswordHashed = md5($newPassword);
        $sql_update = "UPDATE thanhvien SET MatKhau='$newPasswordHashed' WHERE ID_ThanhVien='$ID_ThanhVien'";
        if (mysqli_query($mysqli, $sql_update)) {
            $_SESSION['success_message'] = "Đổi mật khẩu thành công!";
            $_SESSION['message_type'] = "success";
            echo "<script>
                   
                    window.location.href = 'index.php?navigate=profile';
                  </script>";
            exit(); // Dừng thực thi các dòng code phía dưới
        } else {
            $errors['general'] = "Cập nhật mật khẩu thất bại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" href="../../assets/css/styles.css"> <!-- Điều chỉnh đường dẫn CSS nếu cần -->
    <script>
    function redirectTo(url) {
        window.location.href = url;
    }
    </script>
</head>
<body>
    <div class="container mt-60">
      <div class="card bg-light pt-3 pb-3">
        <article class="card-body mx-auto" style="max-width: 400px;">
          <h4 class="card-title text-center">Đổi mật khẩu</h4>
          <?php
          if (isset($_SESSION['message'])) {
              echo "<div class='alert alert-{$_SESSION['message_type']}'>{$_SESSION['message']}</div>";
              unset($_SESSION['message']);
              unset($_SESSION['message_type']);
          }
          ?>
          <form id="changePasswordForm" action="" method="POST">
            <label for="old-password"><b>Mật khẩu cũ</b></label><br>
            <input id="old-password" name="old-password" type="password" style="width: 220px;" value="<?php echo htmlspecialchars($oldPassword); ?>">
            <?php
            if (isset($errors['old-password'])) {
                echo "<div class='text-danger'>{$errors['old-password']}</div>";
            }
            ?><br>
            <label for="new-password"><b>Mật khẩu mới</b></label><br>
            <input id="new-password" name="new-password" type="password" style="width: 220px;" value="<?php echo htmlspecialchars($newPassword); ?>">
            <?php
            if (isset($errors['new-password'])) {
                echo "<div class='text-danger'>{$errors['new-password']}</div>";
            }
            ?><br>
            <label for="new-password-repeat"><b>Nhập lại mật khẩu</b></label><br>
            <input id="new-password-repeat" name="new-password-repeat" type="password" style="width: 220px;" value="<?php echo htmlspecialchars($newPasswordRepeat); ?>">
            <?php
            if (isset($errors['new-password-repeat'])) {
                echo "<div class='text-danger'>{$errors['new-password-repeat']}</div>";
            }
            ?><br>
            <input type="submit" class="btn btn-primary btn-block mt-3" name="sua" value="Sửa">
            <button type="button" class="btn btn-secondary btn-block mt-2" onclick="window.history.back();">Hủy</button>
          </form>
          <?php if (isset($redirect)): ?>
            <script>
            window.onload = function() {
                redirectTo("<?php echo $redirect; ?>");
            };
            </script>
          <?php endif; ?>
        </article>
      </div>
    </div>
</body>
</html>
