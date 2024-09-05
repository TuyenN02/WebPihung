<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $ID_ChinhSach = intval($_POST['id']); // Bảo mật: ép kiểu ID_ChinhSach thành số nguyên
        $TieuDe = trim(mysqli_real_escape_string($mysqli, $_POST['TieuDe']));
        $NoiDung = trim(mysqli_real_escape_string($mysqli, $_POST['NoiDung']));

        // Lưu dữ liệu vào session
        $_SESSION['data'] = [
            'TieuDe' => $TieuDe,
            'NoiDung' => $NoiDung,
        ];

        // Kiểm tra tiêu đề chính sách có trùng không
        $check_policy_query = "SELECT ID_ChinhSach FROM chinhsach WHERE TieuDe='$TieuDe' AND ID_ChinhSach != $ID_ChinhSach";
        $check_policy_result = mysqli_query($mysqli, $check_policy_query);

        if (mysqli_num_rows($check_policy_result) > 0) {
            echo json_encode(['status' => 'error', 'message' => "Tiêu đề chính sách đã tồn tại!"]);
            exit();
        }

        // Cập nhật thông tin chính sách trong cơ sở dữ liệu
        $sql_update = "UPDATE chinhsach SET 
            TieuDe='$TieuDe', 
            NoiDung='$NoiDung'
            WHERE ID_ChinhSach=$ID_ChinhSach";

        if (mysqli_query($mysqli, $sql_update)) {
            unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi lưu thành công
            $_SESSION['success'] = "Cập nhật chính sách thành công!"; // Lưu thông báo thành công vào session
            echo json_encode(['status' => 'success', 'redirect' => 'index.php?policy=list-policy']);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Cập nhật thất bại. Vui lòng thử lại."]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "ID chính sách không hợp lệ."]);
    }
}
?>
