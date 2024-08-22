<?php
include("../../config/connection.php");
session_start();

// Khởi tạo biến lỗi và thành công
$errors = [];
$success = false;

// Kiểm tra xem có yêu cầu cập nhật không
if (isset($_POST['update'])) {
    $ID_SanPham = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $TenSanPham = trim($_POST['TenSanPham']);
    $GiaBan = trim($_POST['GiaBan']);
    $SoLuong = trim($_POST['SoLuong']);
    $MoTa = trim($_POST['MoTa']);
    $ID_DanhMuc = (int)$_POST['danhmuc'];
    $ID_NCC = (int)$_POST['nhacungcap'];

    $imageName = $_FILES['image']['name'];
    $imageTemp = $_FILES['image']['tmp_name'];
    $targetDir = "../../../assets/image/product/";

    // Xử lý ảnh chính
    if (!empty($imageName)) {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        if (in_array($file_extension, $allowed_extensions)) {
            $targetFile = $targetDir . basename($imageName);
            if (move_uploaded_file($imageTemp, $targetFile)) {
                $newImageName = $imageName;
            } else {
                $errors['Img'] = 'Không thể tải lên tệp hình ảnh chính. Vui lòng thử lại.';
            }
        } else {
            $errors['Img'] = 'Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg, .jpeg, hoặc .png.';
        }
    } else {
        // Nếu không có hình ảnh mới, giữ hình ảnh cũ
        $result = $mysqli->query("SELECT Img FROM sanpham WHERE ID_SanPham=$ID_SanPham");
        $newImageName = $result ? $result->fetch_assoc()['Img'] : null;
    }

    // Xử lý ảnh phụ
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        foreach ($_FILES['additional_images']['name'] as $key => $name) {
            if ($name) {
                $tempName = $_FILES['additional_images']['tmp_name'][$key];
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $targetFile = $targetDir . basename($name);
                    if (move_uploaded_file($tempName, $targetFile)) {
                        // Lưu ảnh phụ vào cơ sở dữ liệu
                        $sql_insert_image = "INSERT INTO hinhanh_sanpham (ID_SanPham, Anh) VALUES (?, ?)";
                        $stmt = $mysqli->prepare($sql_insert_image);
                        $stmt->bind_param("is", $ID_SanPham, $name);
                        if (!$stmt->execute()) {
                            $errors['additional_images'][] = "Không thể lưu tệp hình ảnh phụ: $name";
                        }
                    } else {
                        $errors['additional_images'][] = "Không thể tải lên tệp hình ảnh phụ: $name";
                    }
                } else {
                    $errors['additional_images'][] = "Định dạng tệp không hợp lệ cho hình ảnh phụ: $name";
                }
            }
        }
    }

    // Kiểm tra các trường không được để trống
    if (empty($TenSanPham)) {
        $errors['TenSanPham'] = 'Tên sản phẩm không được bỏ trống.';
    } elseif (strlen($TenSanPham) < 3) {
        $errors['TenSanPham'] = 'Tên sản phẩm phải có ít nhất 3 ký tự.';
    } elseif (strlen($TenSanPham) > 50) {
        $errors['TenSanPham'] = 'Tên sản phẩm không được quá 50 ký tự.';
    } else {
        // Kiểm tra trùng tên sản phẩm
        $sql_checkTenSanPham = "SELECT * FROM sanpham WHERE TenSanPham=? AND ID_SanPham != ?";
        $stmt = $mysqli->prepare($sql_checkTenSanPham);
        $stmt->bind_param("si", $TenSanPham, $ID_SanPham);
        $stmt->execute();
        $result_checkTenSanPham = $stmt->get_result();

        if ($result_checkTenSanPham->num_rows > 0) {
            $errors['TenSanPham'] = 'Tên sản phẩm này đã tồn tại. Vui lòng chọn tên khác.';
        }
    }

    if (empty($GiaBan)) {
        $errors['GiaBan'] = 'Giá không được bỏ trống.';
    } elseif (!is_numeric($GiaBan) || $GiaBan <= 0) {
        $errors['GiaBan'] = 'Giá phải là số dương.';
    }

    if (empty($SoLuong)) {
        $errors['SoLuong'] = 'Số lượng không được bỏ trống.';
    } elseif (!is_numeric($SoLuong) || $SoLuong <= 0) {
        $errors['SoLuong'] = 'Số lượng phải là số dương.';
    }

    if (empty($MoTa)) {
        $errors['MoTa'] = 'Mô tả sản phẩm không được bỏ trống.';
    }

    if (empty($errors)) {
        // Thực hiện truy vấn để cập nhật sản phẩm vào cơ sở dữ liệu
        $sql_update = "UPDATE sanpham SET 
            ID_DanhMuc=?, 
            ID_NhaCungCap=?, 
            TenSanPham=?, 
            MoTa=?, 
            GiaBan=?, 
            SoLuong=?, 
            Img=? 
            WHERE ID_SanPham=?";
        $stmt = $mysqli->prepare($sql_update);
        $stmt->bind_param("iissdssi", $ID_DanhMuc, $ID_NCC, $TenSanPham, $MoTa, $GiaBan, $SoLuong, $newImageName, $ID_SanPham);

        if ($stmt->execute()) {
            // Lưu thông báo thành công vào session
            $_SESSION['success'] = 'Cập nhật sản phẩm thành công!';
            // Chuyển hướng đến trang danh sách sản phẩm
            header("Location:../../index.php?product=list-product");
            exit();
        } else {
            $errors['sql_error'] = 'Lỗi khi cập nhật. Vui lòng thử lại.';
        }
    }

    // Lưu lỗi vào session và quay lại trang form
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location:../../index.php?product=suaSanPham&id=$ID_SanPham");
    exit();
}
?>
