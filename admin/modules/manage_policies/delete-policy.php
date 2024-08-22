<?php 
include("../../config/connection.php");
session_start();

if (isset($_GET['id'])) {
    $ID_ChinhSach = $_GET['id'];
    
    // Xóa chính sách khỏi cơ sở dữ liệu
    $sql = "DELETE FROM chinhsach WHERE ID_ChinhSach='$ID_ChinhSach'";
    if (mysqli_query($mysqli, $sql)) {
        // Lưu thông báo thành công vào session
        $_SESSION['message'] = 'Chính sách đã được xóa thành công!';
        $_SESSION['message_type'] = 'success';
    } else {
        // Nếu có lỗi khi xóa chính sách
        $_SESSION['message'] = 'Lỗi khi xóa chính sách từ cơ sở dữ liệu. Vui lòng thử lại.';
        $_SESSION['message_type'] = 'danger';
    }
    
    // Chuyển hướng về trang danh sách chính sách
    header('Location: ../../index.php?policy=list-policy');
    exit();
}
?>
