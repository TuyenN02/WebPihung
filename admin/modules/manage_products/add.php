<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy và làm sạch dữ liệu từ POST
    $TenSanPham = trim(mysqli_real_escape_string($mysqli, $_POST['TenSanPham']));
    
    // Xử lý giá tiền
    $GiaBanRaw = trim(mysqli_real_escape_string($mysqli, $_POST['GiaBan']));
    $GiaBan = str_replace('.', '', $GiaBanRaw); // Loại bỏ dấu chấm phân cách hàng nghìn
    $GiaBan = floatval($GiaBan); // Chuyển đổi thành số thực

    $SoLuong = floatval(trim(mysqli_real_escape_string($mysqli, $_POST['SoLuong'])));
    $MoTa = trim(mysqli_real_escape_string($mysqli, $_POST['MoTa']));
    $ID_DanhMuc = intval(trim(mysqli_real_escape_string($mysqli, $_POST['danhmuc'])));
    $ID_NhaCungCap = intval(trim(mysqli_real_escape_string($mysqli, $_POST['nhacungcap'])));

    // Xử lý hình ảnh chính
    $Img = $_FILES['Img']['name'];
    $Img_tmp = $_FILES['Img']['tmp_name'];

    // Lưu dữ liệu vào session
    $_SESSION['data'] = [
        'TenSanPham' => $TenSanPham,
        'GiaBan' => $GiaBan,
        'SoLuong' => $SoLuong,
        'MoTa' => $MoTa,
        'danhmuc' => $ID_DanhMuc,
        'nhacungcap' => $ID_NhaCungCap,
    ];

    // Kiểm tra tên sản phẩm có bị trùng
    $check_name_query = "SELECT ID_SanPham FROM sanpham WHERE TenSanPham='$TenSanPham'";
    $check_name_result = mysqli_query($mysqli, $check_name_query);

    if (mysqli_num_rows($check_name_result) > 0) {
        echo json_encode(['status' => 'error', 'message' => "Tên sản phẩm đã tồn tại!"]);
        exit();
    }

    // Kiểm tra và xử lý hình ảnh chính
    if (!empty($Img)) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imgExtension = strtolower(pathinfo($Img, PATHINFO_EXTENSION));
        if (in_array($imgExtension, $allowedExtensions)) {
            move_uploaded_file($Img_tmp, "../../../assets/image/product/" . $Img);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Định dạng hình ảnh không hợp lệ!"]);
            exit();
        }
    }

    // Thêm sản phẩm vào cơ sở dữ liệu
    $sql_insert = "INSERT INTO sanpham (TenSanPham, GiaBan, SoLuong, MoTa, ID_DanhMuc, ID_NhaCungCap, Img) 
                   VALUES ('$TenSanPham', '$GiaBan', '$SoLuong', '$MoTa', '$ID_DanhMuc', '$ID_NhaCungCap', '$Img')";

    if (mysqli_query($mysqli, $sql_insert)) {
        $product_id = mysqli_insert_id($mysqli); // Lấy ID của sản phẩm mới thêm vào

        // Xử lý ảnh mô tả
        if (isset($_FILES['ImgDescriptions'])) {
            $imgDescriptions = $_FILES['ImgDescriptions'];
            $imgCount = count($imgDescriptions['name']);

            for ($i = 0; $i < $imgCount; $i++) {
                $imgDescription = $imgDescriptions['name'][$i];
                $imgDescription_tmp = $imgDescriptions['tmp_name'][$i];

                if (!empty($imgDescription)) {
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    $imgExtension = strtolower(pathinfo($imgDescription, PATHINFO_EXTENSION));
                    if (in_array($imgExtension, $allowedExtensions)) {
                        move_uploaded_file($imgDescription_tmp, "../../../assets/image/product/" . $imgDescription);

                        // Thêm thông tin ảnh vào cơ sở dữ liệu
                        $sql_insert_img = "INSERT INTO hinhanh_sanpham (ID_SanPham, Anh) VALUES ('$product_id', '$imgDescription')";
                        mysqli_query($mysqli, $sql_insert_img);
                    }
                }
            }
        }

        unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi thêm thành công
        $_SESSION['success'] = "Thêm sản phẩm thành công!"; // Lưu thông báo thành công vào session
        echo json_encode(['status' => 'success', 'redirect' => 'index.php?product=list-product']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Thêm thất bại. Vui lòng thử lại."]);
    }
}
?>
