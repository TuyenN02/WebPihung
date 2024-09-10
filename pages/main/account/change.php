<?php
session_start();
include("../../../admin/config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ID_ThanhVien = $_GET['id'];
    $HoVaTen = trim($_POST['HoVaTen']);
    $Email = trim($_POST['Email']);
    $SoDienThoai = trim($_POST['SoDienThoai']);
    $DiaChi = trim($_POST['DiaChi']);

    $errors = [];

    // Kiểm tra trùng email
    $sqlCheckEmail = "SELECT * FROM thanhvien WHERE Email = ? AND ID_ThanhVien != ?";
    $stmt = $mysqli->prepare($sqlCheckEmail);
    $stmt->bind_param("si", $Email, $ID_ThanhVien);
    $stmt->execute();
    $resultEmail = $stmt->get_result();

    if ($resultEmail->num_rows > 0) {
        $errors[] = 'Email đã tồn tại!';
    }

    // Kiểm tra trùng số điện thoại
    $sqlCheckPhone = "SELECT * FROM thanhvien WHERE SoDienThoai = ? AND ID_ThanhVien != ?";
    $stmt = $mysqli->prepare($sqlCheckPhone);
    $stmt->bind_param("si", $SoDienThoai, $ID_ThanhVien);
    $stmt->execute();
    $resultPhone = $stmt->get_result();

    if ($resultPhone->num_rows > 0) {
        $errors[] = 'Số điện thoại đã tồn tại!';
    }

    if (!empty($errors)) {
        // Trả về thông báo lỗi
        $response = array('status' => 'error', 'message' => implode(' ', $errors));
    } else {
        // Cập nhật thông tin thành viên
        $sqlUpdate = "UPDATE thanhvien SET HoVaTen = ?, Email = ?, SoDienThoai = ?, DiaChi = ? WHERE ID_ThanhVien = ?";
        $stmt = $mysqli->prepare($sqlUpdate);
        $stmt->bind_param("ssssi", $HoVaTen, $Email, $SoDienThoai, $DiaChi, $ID_ThanhVien);

        if ($stmt->execute()) {
            // Lưu thông báo thành công vào session
            $_SESSION['success_message'] = 'Cập nhật thành công!';
            $response = array('status' => 'success', 'redirect' => 'profile.php');
        } else {
            $response = array('status' => 'error', 'message' => 'Cập nhật thất bại, vui lòng thử lại!');
        }
    }

    echo json_encode($response);
}
?>
