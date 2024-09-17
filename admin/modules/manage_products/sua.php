<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $ID_SanPham = intval($_POST['id']);
        $ID_DanhMuc = intval($_POST['ID_DanhMuc']);
        $ID_NhaCungCap = intval($_POST['ID_NhaCungCap']);
        $TenSanPham = trim(mysqli_real_escape_string($mysqli, $_POST['TenSanPham']));
        $MoTa = trim(mysqli_real_escape_string($mysqli, $_POST['MoTa']));
        $GiaBanRaw = trim(mysqli_real_escape_string($mysqli, $_POST['GiaBan']));
        $GiaBan = str_replace('.', '', $GiaBanRaw); // Loại bỏ dấu chấm phân cách hàng nghìn
        $GiaBan = floatval($GiaBan); // Chuyển đổi thành số thực
        $SoLuong = floatval($_POST['SoLuong']);
        $Img = $_FILES['Img']['name'];
        $Img_tmp = $_FILES['Img']['tmp_name'];
        $ImgDescriptions = $_FILES['ImgDescriptions']['name'];
        $ImgDescriptions_tmp = $_FILES['ImgDescriptions']['tmp_name'];

        // Save data to session
        $_SESSION['data'] = [
            'ID_DanhMuc' => $ID_DanhMuc,
            'ID_NhaCungCap' => $ID_NhaCungCap,
            'TenSanPham' => $TenSanPham,
            'MoTa' => $MoTa,
            'GiaBan' => $GiaBan,
            'SoLuong' => $SoLuong,
        ];

        // Check for existing product name
        $check_name_query = "SELECT ID_SanPham FROM sanpham WHERE TenSanPham='$TenSanPham' AND ID_SanPham != $ID_SanPham";
        $check_name_result = mysqli_query($mysqli, $check_name_query);

        if (mysqli_num_rows($check_name_result) > 0) {
            echo json_encode(['status' => 'error', 'message' => "Tên sản phẩm đã tồn tại!"]);
            exit();
        }

        // Update product information
        $sql_update = "UPDATE sanpham SET 
            ID_DanhMuc='$ID_DanhMuc', 
            ID_NhaCungCap='$ID_NhaCungCap', 
            TenSanPham='$TenSanPham', 
            MoTa='$MoTa', 
            GiaBan='$GiaBan', 
            SoLuong='$SoLuong'";

        // Handle main image
        if (!empty($Img)) {
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $imgExtension = strtolower(pathinfo($Img, PATHINFO_EXTENSION));
            if (in_array($imgExtension, $allowedExtensions)) {
                // Delete old image if it exists
                $result = mysqli_query($mysqli, "SELECT Img FROM sanpham WHERE ID_SanPham=$ID_SanPham");
                $oldImg = mysqli_fetch_assoc($result)['Img'];
                if ($oldImg && file_exists("../../../assets/image/product/$oldImg")) {
                    unlink("../../../assets/image/product/$oldImg");
                }

                // Move new image
                move_uploaded_file($Img_tmp, "../../../assets/image/product/" . $Img);
                $sql_update .= ", Img='$Img'";
            } else {
                echo json_encode(['status' => 'error', 'message' => "Định dạng hình ảnh không hợp lệ!"]);
                exit();
            }
        }

        $sql_update .= " WHERE ID_SanPham=$ID_SanPham";

        if (mysqli_query($mysqli, $sql_update)) {
            // Handle description images
            if (!empty($ImgDescriptions[0])) {
                // Delete existing description images for this product
                mysqli_query($mysqli, "DELETE FROM hinhanh_sanpham WHERE ID_SanPham=$ID_SanPham");
                
                foreach ($ImgDescriptions as $key => $imgDesc) {
                    if ($imgDesc) {
                        $imgDesc_tmp = $ImgDescriptions_tmp[$key];
                        $allowedExtensions = ['jpg', 'jpeg', 'png'];
                        $imgDescExtension = strtolower(pathinfo($imgDesc, PATHINFO_EXTENSION));
                        if (in_array($imgDescExtension, $allowedExtensions)) {
                            // Move the uploaded description image
                            move_uploaded_file($imgDesc_tmp, "../../../assets/image/product/" . $imgDesc);

                            // Insert into database
                            $sql_insert_desc = "INSERT INTO hinhanh_sanpham (ID_SanPham, Anh) VALUES ('$ID_SanPham', '$imgDesc')";
                            mysqli_query($mysqli, $sql_insert_desc);
                        } else {
                            echo json_encode(['status' => 'error', 'message' => "Định dạng hình ảnh mô tả không hợp lệ!"]);
                            exit();
                        }
                    }
                }
            }

            unset($_SESSION['data']);
            $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
            echo json_encode(['status' => 'success', 'redirect' => 'index.php?product=list-product']);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Cập nhật thất bại. Vui lòng thử lại."]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "ID sản phẩm không hợp lệ."]);
    }
}