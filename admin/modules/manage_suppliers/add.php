<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $TenNCC = trim(mysqli_real_escape_string($mysqli, $_POST['TenNCC']));
    $MoTa = trim(mysqli_real_escape_string($mysqli, $_POST['MoTa']));
    $Email = trim(mysqli_real_escape_string($mysqli, $_POST['Email']));
    $SoDienThoai = trim(mysqli_real_escape_string($mysqli, $_POST['SoDienThoai']));
    $DiaChi = trim(mysqli_real_escape_string($mysqli, $_POST['DiaChi']));
    $Img = $_FILES['Img']['name'];
    $Img_tmp = $_FILES['Img']['tmp_name'];

    // Lưu dữ liệu vào session
    $_SESSION['data'] = [
        'TenNCC' => $TenNCC,
        'MoTa' => $MoTa,
        'Email' => $Email,
        'SoDienThoai' => $SoDienThoai,
        'DiaChi' => $DiaChi,
    ];

    // Kiểm tra email có trùng không
    $check_email_query = "SELECT ID_NCC FROM nhacungcap WHERE Email='$Email'";
    $check_email_result = mysqli_query($mysqli, $check_email_query);

    if (mysqli_num_rows($check_email_result) > 0) {
        echo json_encode(['status' => 'error', 'message' => "Email đã tồn tại!"]);
        exit();
    } 

    // Kiểm tra số điện thoại có trùng không
    $check_phone_query = "SELECT ID_NCC FROM nhacungcap WHERE SoDienThoai='$SoDienThoai'";
    $check_phone_result = mysqli_query($mysqli, $check_phone_query);

    if (mysqli_num_rows($check_phone_result) > 0) {
        echo json_encode(['status' => 'error', 'message' => "Số điện thoại đã tồn tại!"]);
        exit();
    }

    // Kiểm tra và xử lý hình ảnh
    if (!empty($Img)) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imgExtension = strtolower(pathinfo($Img, PATHINFO_EXTENSION));
        if (in_array($imgExtension, $allowedExtensions)) {
            move_uploaded_file($Img_tmp, "../../../assets/image/supplier/" . $Img);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Định dạng hình ảnh không hợp lệ!"]);
            exit();
        }
    }

    // Thêm nhà cung cấp vào cơ sở dữ liệu
    $sql_insert = "INSERT INTO nhacungcap (TenNCC, MoTa, Email, SoDienThoai, DiaChi, Img) 
                   VALUES ('$TenNCC', '$MoTa', '$Email', '$SoDienThoai', '$DiaChi', '$Img')";

    if (mysqli_query($mysqli, $sql_insert)) {
        unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi thêm thành công
        $_SESSION['success_message'] = "Thêm nhà cung cấp thành công!"; // Lưu thông báo thành công vào session
        echo json_encode(['status' => 'success', 'redirect' => 'index.php?ncc=list-ncc']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Thêm thất bại. Vui lòng thử lại."]);
    }
}
?>