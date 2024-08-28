<?php 
include("../../config/connection.php"); 

if (isset($_GET['id_NCC'])) {
    $ID_NCC = $_GET['id_NCC'];

    // Lấy tên ảnh từ cơ sở dữ liệu
    $query_select_image = "SELECT Img FROM nhacungcap WHERE ID_NCC = $ID_NCC";
    $result_select_image = mysqli_query($mysqli, $query_select_image);
    $row_select_image = mysqli_fetch_assoc($result_select_image);
    $imageToDelete = $row_select_image['Img'];

    // Xóa ảnh khỏi thư mục
    unlink("../../../assets/image/supplier/" . $imageToDelete);

    // Xóa nhà cung cấp khỏi cơ sở dữ liệu
    $sql_xoa = "DELETE FROM nhacungcap WHERE ID_NCC = '$ID_NCC'";
    if (mysqli_query($mysqli, $sql_xoa)) {
        // Lưu thông báo thành công vào session
        session_start();
        $_SESSION['success_message'] = "Xóa nhà cung cấp thành công!";
    }
}

// Chuyển hướng về trang danh sách nhà cung cấp
header('Location: ../../index.php?ncc=list-ncc');
exit();
?>