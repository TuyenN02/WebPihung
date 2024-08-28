<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_baiviet'])) {
        $ID_baiviet = intval($_POST['id_baiviet']); // Bảo mật: ép kiểu ID_baiviet thành số nguyên
        $Tenbaiviet = trim(mysqli_real_escape_string($mysqli, $_POST['name']));
        $Noidung = trim(mysqli_real_escape_string($mysqli, $_POST['Noidung']));
        $Img = $_FILES['image']['name'];
        $Img_tmp = $_FILES['image']['tmp_name'];

        // Lưu dữ liệu vào session
        $_SESSION['data'] = [
            'Tenbaiviet' => $Tenbaiviet,
            'Noidung' => $Noidung,
        ];

      

        // Cập nhật thông tin bài viết trong cơ sở dữ liệu
        $sql_update = "UPDATE posts SET 
            Tenbaiviet='$Tenbaiviet', 
            Noidung='$Noidung'";

        // Thêm phần cập nhật hình ảnh nếu có
        if (!empty($Img)) {
            // Kiểm tra định dạng hình ảnh
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $imgExtension = strtolower(pathinfo($Img, PATHINFO_EXTENSION));
            if (in_array($imgExtension, $allowedExtensions)) {
                // Xóa hình ảnh cũ nếu cần
                $result = mysqli_query($mysqli, "SELECT Img FROM posts WHERE ID_baiviet=$ID_baiviet");
                $oldImg = mysqli_fetch_assoc($result)['Img'];
                if ($oldImg && file_exists("../../../assets/image/supplier/$oldImg")) {
                    unlink("../../../assets/image/supplier/$oldImg");
                }

                // Di chuyển hình ảnh mới
                move_uploaded_file($Img_tmp, "../../../assets/image/supplier/" . $Img);
                $sql_update .= ", Img='$Img'";
            } else {
                echo json_encode(['status' => 'error', 'message' => "Định dạng hình ảnh không hợp lệ!"]);
                exit();
            }
        }

        $sql_update .= " WHERE ID_baiviet=$ID_baiviet";

        if (mysqli_query($mysqli, $sql_update)) {
            unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi lưu thành công
            $_SESSION['success'] = "Cập nhật bài viết thành công!"; // Lưu thông báo thành công vào session
            echo json_encode(['status' => 'success', 'redirect' => 'index.php?posts=list-posts']);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Cập nhật thất bại. Vui lòng thử lại."]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "ID bài viết không hợp lệ."]);
    }
}
?>