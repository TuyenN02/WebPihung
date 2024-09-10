<?php
include("../../../admin/config/connection.php");
$ID_ThanhVien = $_POST['ID_ThanhVien'];
$Email = $_POST['Email'];
$SoDienThoai = $_POST['SoDienThoai'];

$response = ['emailExists' => false, 'phoneExists' => false];

// Kiểm tra trùng email
$sql_email = "SELECT * FROM thanhvien WHERE Email = '$Email' AND ID_ThanhVien != '$ID_ThanhVien'";
$query_email = mysqli_query($mysqli, $sql_email);
if (mysqli_num_rows($query_email) > 0) {
    $response['emailExists'] = true;
}

// Kiểm tra trùng số điện thoại
$sql_phone = "SELECT * FROM thanhvien WHERE SoDienThoai = '$SoDienThoai' AND ID_ThanhVien != '$ID_ThanhVien'";
$query_phone = mysqli_query($mysqli, $sql_phone);
if (mysqli_num_rows($query_phone) > 0) {
    $response['phoneExists'] = true;
}

echo json_encode($response);
?>