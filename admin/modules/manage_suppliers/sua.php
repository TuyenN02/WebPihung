<?php
session_start();
include("../../config/connection.php");

if (isset($_POST['submit'])) {
    $ID_NCC = intval($_GET['id_NCC']); // Bảo mật: ép kiểu ID_NCC thành số nguyên

    // Lấy dữ liệu từ form và loại bỏ dấu cách
    $TenNCC = trim(mysqli_real_escape_string($mysqli, $_POST['name']));
    $MoTa = trim(mysqli_real_escape_string($mysqli, $_POST['MoTa']));
    $Email = trim(mysqli_real_escape_string($mysqli, $_POST['email']));
    $SoDienThoai = trim(mysqli_real_escape_string($mysqli, $_POST['SoDienThoai']));
    $DiaChi = trim(mysqli_real_escape_string($mysqli, $_POST['DiaChi']));
    $Img = $_FILES['image']['name'];
    $Img_tmp = $_FILES['image']['tmp_name'];

    // Lưu dữ liệu vào session
    $_SESSION['data'] = [
        'TenNCC' => $TenNCC,
        'MoTa' => $MoTa,
        'Email' => $Email,
        'SoDienThoai' => $SoDienThoai,
        'DiaChi' => $DiaChi,
    ];

    // Kiểm tra lỗi bỏ trống
    if (empty($TenNCC)) {
        $_SESSION['errors']['TenNCC'] = "Tên nhà cung cấp không được để trống.";
    }
    if (empty($Email)) {
        $_SESSION['errors']['email'] = "Email không được để trống.";
    }
    if (empty($SoDienThoai)) {
        $_SESSION['errors']['phone'] = "Số điện thoại không được để trống.";
    }
    if (empty($DiaChi)) {
        $_SESSION['errors']['DiaChi'] = "Địa chỉ không được để trống.";
    }

    // Kiểm tra độ dài tên nhà cung cấp
    if (strlen($TenNCC) > 50) {
        $_SESSION['errors']['TenNCC'] = "Tên nhà cung cấp không được vượt quá 50 ký tự.";
    }

    // Kiểm tra định dạng số điện thoại
    if (!preg_match('/^0[0-9]{9}$/', $SoDienThoai)) {
        $_SESSION['errors']['phone'] = "Vui lòng nhập đúng định dạng.";
    }

    // Kiểm tra định dạng email với ít nhất 4 ký tự trước @ và không quá 255 ký tự
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL) || strlen(explode('@', $Email)[0]) < 4 || strlen($Email) > 50) {
        $_SESSION['errors']['email'] = "Email chưa đúng định dạng!";
    }

    // Kiểm tra nếu có lỗi
    if (!empty($_SESSION['errors'])) {
        header("Location: ../../index.php?ncc=sua-ncc&id_NCC=$ID_NCC");
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
            move_uploaded_file($Img_tmp, "../../../assets/image/supplier/".$Img);
            $sql_update .= ", Img='$Img'";
        } else {
            $_SESSION['errors']['image'] = "Định dạng hình ảnh không hợp lệ! Vui lòng tải lên tệp .jpg, .jpeg hoặc .png.";
            header("Location: ../../index.php?ncc=sua-ncc&id_NCC=$ID_NCC");
            exit();
        }
    }

    $sql_update .= " WHERE ID_NCC=$ID_NCC";

    if (mysqli_query($mysqli, $sql_update)) {
        unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi lưu thành công
        
        // Thêm thông báo thành công
        $_SESSION['success'] = "Cập nhật nhà cung cấp thành công!";
        header("Location: ../../index.php?ncc=list-ncc");
    } else {
        $_SESSION['errors']['update'] = "Cập nhật thất bại. Vui lòng thử lại.";
        header("Location: ../../index.php?ncc=sua-ncc&id_NCC=$ID_NCC");
    }
}
?>
