<?php


$ID_ThanhVien = $_SESSION['ID_ThanhVien'];
$sql_Cus = "SELECT * FROM thanhvien WHERE ID_ThanhVien = $ID_ThanhVien LIMIT 1";
$query_Cus = mysqli_query($mysqli, $sql_Cus);
$row = mysqli_fetch_array($query_Cus);

if (isset($_POST['sua'])) {
    $oldPassword = trim($_POST['old-password']);
    $newPassword = trim($_POST['new-password']);
    $newPasswordRepeat = trim($_POST['new-password-repeat']);

    // Khởi tạo mảng chứa lỗi
    $errors = array();

    // Kiểm tra mật khẩu cũ
    if (empty($oldPassword)) {
        $errors['old-password'] = "Mật khẩu cũ không được để trống.";
    }

    // Kiểm tra mật khẩu mới
    if (empty($newPassword)) {
        $errors['new-password'] = "Mật khẩu mới không được để trống.";
    } elseif (strlen($newPassword) < 8 || strlen($newPassword) > 20) {
        $errors['new-password'] = "Mật khẩu mới phải có từ 8 đến 20 ký tự.";
    }

    // Kiểm tra mật khẩu nhập lại
    if (empty($newPasswordRepeat)) {
        $errors['new-password-repeat'] = "Nhập lại mật khẩu không được để trống.";
    } elseif ($newPassword != $newPasswordRepeat) {
        $errors['new-password-repeat'] = "Mật khẩu không trùng khớp.";
    }

    // Nếu có lỗi, lưu lỗi vào session và hiển thị
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../../index.php?page=change-password");
        exit();
    } else {
        $oldPassword = md5($oldPassword);
        $newPassword = md5($newPassword);
        $MatKhau = $row['MatKhau'];

        // Kiểm tra mật khẩu cũ
        if ($oldPassword != $MatKhau) {
            $_SESSION['errors']['old-password'] = "Mật khẩu cũ không chính xác!";
            header("Location: ../../index.php?page=change-password");
            exit();
        } else {
            // Cập nhật mật khẩu mới
            $sql_update = "UPDATE thanhvien SET MatKhau='$newPassword' WHERE ID_ThanhVien='$ID_ThanhVien'";
            if (mysqli_query($mysqli, $sql_update)) {
                $_SESSION['message'] = "Đổi mật khẩu thành công!";
                $_SESSION['message_type'] = "success";
                header("Location: ../../index.php?page=change-password");
                exit();
            } else {
                $_SESSION['errors']['general'] = "Cập nhật mật khẩu thất bại!";
                header("Location: ../../index.php?page=change-password");
                exit();
            }
        }
    }
}
?>
<div class="container mt-60">
  <div class="card bg-light pt-3 pb-3">
    <article class="card-body mx-auto" style="max-width: 400px;">
      <h4 class="card-title text-center">Đổi mật khẩu</h4>
      <?php
      if (isset($_SESSION['errors'])) {
          foreach ($_SESSION['errors'] as $error) {
              echo "<div class='alert alert-danger'>$error</div>";
          }
          unset($_SESSION['errors']);
      }
      if (isset($_SESSION['message'])) {
          echo "<div class='alert alert-{$_SESSION['message_type']}'>{$_SESSION['message']}</div>";
          unset($_SESSION['message']);
          unset($_SESSION['message_type']);
      }
      ?>
      <form action="" method="POST">
        <label for="old-password"><b>Mật khẩu cũ</b></label><br>
        <input type="password" name="old-password" required style="width: 220px;"><br>
        <label for="new-password"><b>Mật khẩu mới</b></label><br>
        <input type="password" name="new-password" required style="width: 220px;"><br>
        <label for="new-password-repeat"><b>Nhập lại mật khẩu</b></label></br>
        <input type="password" name="new-password-repeat" required style="width: 220px;"></br>
        <input type="submit" class="btn btn-primary btn-block mt-3" name="sua" value="Sửa">
      </form>
    </article>
  </div>
</div>
