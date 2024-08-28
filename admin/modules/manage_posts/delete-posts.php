<?php 
include("../../config/connection.php"); 

if (isset($_GET['id_baiviet'])) {

    $ID_baiviet = $_GET['id_baiviet'];

    // Lấy tên ảnh từ cơ sở dữ liệu
    $query_select_image = "SELECT Img FROM posts WHERE ID_baiviet = $ID_baiviet";
    $result_select_image = mysqli_query($mysqli, $query_select_image);
    $row_select_image = mysqli_fetch_assoc($result_select_image);
    $imageToDelete = $row_select_image['Img'];

    // Xóa ảnh khỏi thư mục
    unlink("../../../assets/image/supplier/" . $imageToDelete);

    // Xóa nhà cung cấp khỏi cơ sở dữ liệu
    $sql_xoa = "DELETE FROM posts WHERE ID_baiviet = '$ID_baiviet'";
    if (mysqli_query($mysqli, $sql_xoa)) {
        // Lưu thông báo thành công vào session
        session_start();
        $_SESSION['success'] = "Xóa bài viết thành công.";
    }
}

// Chuyển hướng về trang danh sách nhà cung cấp
header('Location: ../../index.php?posts=list-posts');
exit();
?>
