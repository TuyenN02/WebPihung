<?php
include("../../config/connection.php");
session_start();

// Kiểm tra nếu yêu cầu là POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = $_POST['image'];

    // Xác thực và xử lý yêu cầu xóa ảnh
    if (!empty($imageName)) {
        // Xóa ảnh khỏi thư mục
        $filePath = '../assets/image/product/' . $imageName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Xóa ảnh khỏi cơ sở dữ liệu nếu cần (Tùy thuộc vào cấu trúc cơ sở dữ liệu của bạn)
      
      $sql = $mysqli->prepare("DELETE FROM hinhanh_sanpham WHERE Anh = ?");
        $sql->bind_param("s", $imageName);
        $sql->execute();

        // Trả về phản hồi JSON
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>


