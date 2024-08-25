<?php
include("../../config/connection.php");
session_start();

// Kiểm tra nếu yêu cầu là POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = isset($_POST['image']) ? trim($_POST['image']) : '';

    // Xác thực và xử lý yêu cầu xóa ảnh
    if (!empty($imageName)) {
        // Xóa ảnh khỏi thư mục
        $filePath = '../../../assets/image/product/' . $imageName;
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                // Xóa ảnh khỏi cơ sở dữ liệu
                $sql = $mysqli->prepare("DELETE FROM hinhanh_sanpham WHERE Anh = ?");
                $sql->bind_param("s", $imageName);
                if ($sql->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Xóa ảnh thành công.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa ảnh khỏi cơ sở dữ liệu.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa tệp ảnh khỏi thư mục.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Tệp ảnh không tồn tại.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tên ảnh không hợp lệ.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
}
?>
