<?php 
include("../../config/connection.php");

if (isset($_GET['id'])) {
    $ID_BinhLuan = $_GET['id'];
    
    // Thực hiện xóa bình luận
    $sql = "DELETE FROM binhluan WHERE ID_BinhLuan = '".$ID_BinhLuan."'";
    if (mysqli_query($mysqli, $sql)) {
        // Lưu thông báo thành công vào session
        session_start();
        $_SESSION['success_message'] = "Xóa bình luận thành công!";
    }
    header('Location: ../../index.php?comment=comments');
    exit();
}
?>
