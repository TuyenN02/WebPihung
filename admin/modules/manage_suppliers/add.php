<?php
session_start();
include("../../config/connection.php");

if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form và xử lý trim()
    $TenNCC = trim(mysqli_real_escape_string($mysqli, $_POST['TenNCC']));
    $MoTa = trim(mysqli_real_escape_string($mysqli, $_POST['MoTa']));
    $Email = trim(mysqli_real_escape_string($mysqli, $_POST['Email']));
    $SoDienThoai = trim(mysqli_real_escape_string($mysqli, $_POST['SoDienThoai']));
    $DiaChi = trim(mysqli_real_escape_string($mysqli, $_POST['DiaChi']));
    $Img = trim($_FILES['Img']['name']);
    $Img_tmp = $_FILES['Img']['tmp_name'];

    // Lưu dữ liệu vào session
    $_SESSION['data'] = [
        'TenNCC' => $TenNCC,
        'MoTa' => $MoTa,
        'Email' => $Email,
        'SoDienThoai' => $SoDienThoai,
        'DiaChi' => $DiaChi,
    ];

    // Kiểm tra lỗi
    $errors = [];

    // Kiểm tra các trường thông tin trống
    if (empty($TenNCC)) {
        $errors['TenNCC'] = "Tên nhà cung cấp không được để trống!";
    } elseif (strlen($TenNCC) < 3) {
        $errors['TenNCC'] = "Tên nhà cung cấp phải có ít nhất 3 ký tự!";
    }

    if (empty($Email)) {
        $errors['Email'] = "Email không được để trống.";
    }
    if (empty($SoDienThoai)) {
        $errors['SoDienThoai'] = "Số điện thoại không được để trống!";
    }
    if (empty($DiaChi)) {
        $errors['DiaChi'] = "Địa chỉ không được để trống!";
    }
    if (empty($Img)) {
        $errors['Img'] = "Hình ảnh không được để trống!";
    } else {
        $_SESSION['data']['Img'] = $Img;
    }

    // Kiểm tra độ dài tên nhà cung cấp
    if (strlen($TenNCC) > 50) {
        $errors['TenNCC'] = "Tên nhà cung cấp không được vượt quá 50 ký tự!";
    }

    // Kiểm tra định dạng số điện thoại
    if (!preg_match('/^0[0-9]{9}$/', $SoDienThoai)) {
        $errors['SoDienThoai'] = "Vui lòng nhập đúng định dạng!";
    }

    // Kiểm tra định dạng email
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $errors['Email'] = "Email chưa đúng định dạng!";
    } else {
        list($username, $domain) = explode('@', $Email);

        if (strlen($username) < 4 || strlen($Email) >50) {
            $errors['Email'] = "Email chưa đúng định dạng!";
        }

        if (!checkdnsrr($domain, 'A')) {
            $errors['Email'] = "Email chưa đúng định dạng!";
        }

        if (preg_match('/[^a-zA-Z0-9.-]/', $domain)) {
            $errors['Email'] = "Email chưa đúng định dạng!";
        }
    }

    $check_name_query = "SELECT * FROM nhacungcap WHERE TenNCC = '$TenNCC'";
    $check_name_result = mysqli_query($mysqli, $check_name_query);
    if (mysqli_num_rows($check_name_result) > 0) {
        $errors['TenNCC'] = "Tên nhà cung cấp đã tồn tại!";
    }

    $check_email_query = "SELECT * FROM nhacungcap WHERE Email = '$Email'";
    $check_email_result = mysqli_query($mysqli, $check_email_query);
    if (mysqli_num_rows($check_email_result) > 0) {
        $errors['Email'] = "Email này đã được sử dụng!";
    }

    $check_phone_query = "SELECT * FROM nhacungcap WHERE SoDienThoai = '$SoDienThoai'";
    $check_phone_result = mysqli_query($mysqli, $check_phone_query);
    if (mysqli_num_rows($check_phone_result) > 0) {
        $errors['SoDienThoai'] = "Số điện thoại đã được sử dụng !";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../../index.php?ncc=add-ncc");
        exit();
    }

    if (isset($_SESSION['data']['Img']) && empty($Img)) {
        $Img = $_SESSION['data']['Img'];
    } else {
        if (move_uploaded_file($Img_tmp, "../../../assets/image/supplier/".$Img)) {
            $_SESSION['data']['Img'] = $Img;
        } else {
            $_SESSION['errors']['upload'] = "Không thể tải lên hình ảnh. Vui lòng thử lại.";
            header("Location: ../../index.php?ncc=add-ncc");
            exit();
        }
    }

    $sql = "INSERT INTO nhacungcap(TenNCC, MoTa, Email, SoDienThoai, DiaChi, Img) 
            VALUES('$TenNCC', '$MoTa', '$Email', '$SoDienThoai', '$DiaChi', '$Img')";
    if (mysqli_query($mysqli, $sql)) {
        unset($_SESSION['data']);
        $_SESSION['success'] = "Thêm nhà cung cấp thành công!";
        header("Location: ../../index.php?ncc=list-ncc");
        exit();
    } else {
        $_SESSION['errors']['database'] = "Lỗi cơ sở dữ liệu: " . mysqli_error($mysqli);
        header("Location: ../../index.php?ncc=add-ncc");
        exit();
    }
}
?>
