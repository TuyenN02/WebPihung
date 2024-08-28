<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_NCC'])) {
        $ID_NCC = intval($_POST['id_NCC']); // Bảo mật: ép kiểu ID_NCC thành số nguyên
        $TenNCC = trim(mysqli_real_escape_string($mysqli, $_POST['TenNCC']));
        $MoTa = trim(mysqli_real_escape_string($mysqli, $_POST['MoTa']));
        $Email = trim(mysqli_real_escape_string($mysqli, $_POST['Email']));
        $SoDienThoai = trim(mysqli_real_escape_string($mysqli, $_POST['SoDienThoai']));
        $DiaChi = trim(mysqli_real_escape_string($mysqli, $_POST['DiaChi']));
        $Img = $_FILES['Img']['name'];
        $Img_tmp = $_FILES['Img']['tmp_name'];

        // Lưu dữ liệu vào session
        $_SESSION['data'] = [
            'TenNCC' => $TenNCC,
            'MoTa' => $MoTa,
            'Email' => $Email,
            'SoDienThoai' => $SoDienThoai,
            'DiaChi' => $DiaChi,
        ];

        // Kiểm tra email có trùng không
        $check_email_query = "SELECT ID_NCC FROM nhacungcap WHERE Email='$Email' AND ID_NCC != $ID_NCC";
        $check_email_result = mysqli_query($mysqli, $check_email_query);

        if (mysqli_num_rows($check_email_result) > 0) {
            echo json_encode(['status' => 'error', 'message' => "Email đã tồn tại!"]);
            exit();
        }

        // Kiểm tra số điện thoại có trùng không
        $check_phone_query = "SELECT ID_NCC FROM nhacungcap WHERE SoDienThoai='$SoDienThoai' AND ID_NCC != $ID_NCC";
        $check_phone_result = mysqli_query($mysqli, $check_phone_query);

        if (mysqli_num_rows($check_phone_result) > 0) {
            echo json_encode(['status' => 'error', 'message' => "Số điện thoại đã tồn tại!"]);
            exit();
        }

        // Cập nhật thông tin nhà cung cấp trong cơ sở dữ liệu
        $sql_update = "UPDATE nhacungcap SET 
            TenNCC='$TenNCC', 
            MoTa='$MoTa', 
            Email='$Email', 
            SoDienThoai='$SoDienThoai', 
            DiaChi='$DiaChi'";

        // Thêm phần cập nhật hình ảnh nếu có
        if (!empty($Img)) {
            // Kiểm tra định dạng hình ảnh
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $imgExtension = strtolower(pathinfo($Img, PATHINFO_EXTENSION));
            if (in_array($imgExtension, $allowedExtensions)) {
                // Xóa hình ảnh cũ nếu cần
                $result = mysqli_query($mysqli, "SELECT Img FROM nhacungcap WHERE ID_NCC=$ID_NCC");
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

        $sql_update .= " WHERE ID_NCC=$ID_NCC";

        if (mysqli_query($mysqli, $sql_update)) {
            unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi lưu thành công
            $_SESSION['success_message'] = "Cập nhật nhà cung cấp thành công!"; // Lưu thông báo thành công vào session
            echo json_encode(['status' => 'success', 'redirect' => 'index.php?ncc=list-ncc']);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Cập nhật thất bại. Vui lòng thử lại."]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "ID nhà cung cấp không hợp lệ."]);
    }
}
?>
