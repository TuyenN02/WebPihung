<?php 
include("../../config/connection.php");
session_start();

if (isset($_GET['id_pro'])) {
    $ID_SanPham = $_GET['id_pro'];
    
    // Lấy tên hình ảnh của sản phẩm để xóa
    $query_select_image = "SELECT Img FROM sanpham WHERE ID_SanPham = $ID_SanPham";
    $result_select_image = mysqli_query($mysqli, $query_select_image);
    $row_select_image = mysqli_fetch_assoc($result_select_image);
    $imageToDelete = $row_select_image['Img'];
    
    // Xóa hình ảnh từ thư mục
    if ($imageToDelete && file_exists("../../../assets/image/product/" . $imageToDelete)) {
        unlink("../../../assets/image/product/" . $imageToDelete);
    }
    
    // Xóa bình luận liên quan đến sản phẩm
    $sql_comment = "DELETE FROM binhluan WHERE ID_SanPham='$ID_SanPham'";
    mysqli_query($mysqli, $sql_comment);
    
    // Xóa sản phẩm khỏi cơ sở dữ liệu
    $sql = "DELETE FROM sanpham WHERE ID_SanPham='$ID_SanPham'";
    if (mysqli_query($mysqli, $sql)) {
        // Lưu thông báo thành công vào session
        $_SESSION['success'] = 'Xóa sản phẩm thành công!';
    } else {
        // Nếu có lỗi khi xóa sản phẩm
        $_SESSION['errors'] = ['sql_error' => 'Lỗi khi xóa sản phẩm. Vui lòng thử lại.'];
    }
    
    // Chuyển hướng về trang danh sách sản phẩm
    header('Location: ../../index.php?product=list-product');
    exit();
}
?>
