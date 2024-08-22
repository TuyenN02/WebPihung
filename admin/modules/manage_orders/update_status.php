<?php
include("../../config/connection.php");

// Set header to indicate JSON response
header('Content-Type: application/json');

$response = array('success' => false, 'message' => 'Lỗi không xác định');

if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $id = mysqli_real_escape_string($mysqli, $_POST['order_id']);
    $status = mysqli_real_escape_string($mysqli, $_POST['order_status']);

    // Update the order status
    $query = "UPDATE donhang SET XuLy = '$status' WHERE ID_DonHang = '$id'";

    if (mysqli_query($mysqli, $query)) {
        $response['success'] = true;
        $response['message'] = 'Cập nhật thành công';
    } else {
        $response['message'] = 'Cập nhật thất bại: ' . mysqli_error($mysqli);
    }
} else {
    $response['message'] = 'Dữ liệu không hợp lệ';
}

// Output the JSON response
echo json_encode($response);
?>