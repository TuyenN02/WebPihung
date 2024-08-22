<?php 
include("../../config/connection.php"); 

session_start(); // Khởi tạo session

// Khởi tạo biến lỗi
$errors = [];
$success = false;

if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form và loại bỏ dấu cách thừa
    $TenSanPham = trim($_POST['TenSanPham']);
    $GiaBan = trim($_POST['GiaBan']);
    $SoLuong = trim($_POST['SoLuong']);
    $MoTa = trim($_POST['MoTa']);
    $ID_DanhMuc = trim($_POST['danhmuc']);
    $ID_NCC = trim($_POST['nhacungcap']);
  
    $imageName = $_FILES['Img']['name'];
    $imageTemp = $_FILES['Img']['tmp_name'];

    // Kiểm tra các trường không được để trống
    if (empty($TenSanPham)) {
        $errors['empty_name'] = 'Tên sản phẩm không được bỏ trống.';
    } elseif (strlen($TenSanPham) < 3) {
        $errors['name_length'] = 'Tên sản phẩm phải có ít nhất 3 ký tự.';
    } elseif (strlen($TenSanPham) > 50) {
        $errors['product_name_length'] = 'Tên sản phẩm không được quá 50 ký tự.';
    } else {
        // Kiểm tra tên sản phẩm trùng
        $sql_check_name = "SELECT * FROM sanpham WHERE TenSanPham = '$TenSanPham'";
        $result_check_name = mysqli_query($mysqli, $sql_check_name);
        if (mysqli_num_rows($result_check_name) > 0) {
            $errors['duplicate_name'] = 'Tên sản phẩm đã tồn tại. Vui lòng chọn tên khác.';
        }
    }

    if (empty($GiaBan)) {
        $errors['empty_price'] = 'Giá không được bỏ trống.';
    } elseif (!is_numeric($GiaBan) || $GiaBan <= 0) {
        $errors['invalid_price'] = 'Giá phải là ký tự số dương.';
    }

    if (empty($SoLuong)) {
        $errors['empty_quantity'] = 'Số lượng không được bỏ trống.';
    } elseif (!is_numeric($SoLuong) || $SoLuong <= 0) {
        $errors['invalid_quantity'] = 'Số lượng phải là ký tự số dương.';
    }

    if (empty($MoTa)) {
        $errors['empty_description'] = 'Mô tả sản phẩm không được bỏ trống.';
    }
    
    // Kiểm tra định dạng tệp hình ảnh
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_extension = pathinfo($imageName, PATHINFO_EXTENSION);

    if (!in_array($file_extension, $allowed_extensions)) {
        $errors['invalid_image'] = 'Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.';
    }

    if (empty($errors)) {
        // Di chuyển tệp hình ảnh chính vào thư mục đích
        $targetDir = "../../../assets/image/product/";
        $targetFile = $targetDir . basename($imageName);
        if (move_uploaded_file($imageTemp, $targetFile)) {
            // Thực hiện truy vấn để thêm sản phẩm vào cơ sở dữ liệu
            $sql_add = "INSERT INTO sanpham(ID_DanhMuc, ID_NhaCungCap, TenSanPham, MoTa, GiaBan, SoLuong, Img) 
                        VALUES('$ID_DanhMuc', '$ID_NCC', '$TenSanPham', '$MoTa', '$GiaBan', '$SoLuong', '$imageName')";
            
            if (mysqli_query($mysqli, $sql_add)) {
                $last_id = mysqli_insert_id($mysqli); // Lấy ID của sản phẩm mới thêm
                
                // Xử lý ảnh phụ
                $imagePhuNames = $_FILES['ImgDesc']['name'];
                $imagePhuTemps = $_FILES['ImgDesc']['tmp_name'];
                
                foreach ($imagePhuNames as $index => $imagePhuName) {
                    if (!empty($imagePhuName)) {
                        $file_extension = pathinfo($imagePhuName, PATHINFO_EXTENSION);
                        
                        if (in_array($file_extension, $allowed_extensions)) {
                            $targetFilePhu = $targetDir . basename($imagePhuName);
                            if (move_uploaded_file($imagePhuTemps[$index], $targetFilePhu)) {
                                $sql_add_phu = "INSERT INTO hinhanh_sanpham(ID_SanPham, Anh) VALUES('$last_id', '$imagePhuName')";
                                mysqli_query($mysqli, $sql_add_phu);
                            } else {
                                $errors['file_upload_phu'] = 'Không thể tải lên tệp ảnh phụ: ' . $imagePhuName;
                            }
                        } else {
                            $errors['invalid_image_phu'] = 'Định dạng tệp ảnh phụ không hợp lệ: ' . $imagePhuName;
                        }
                    }
                }
                
                if (empty($errors)) {
                    $_SESSION['success'] = 'Sản phẩm đã được thêm thành công!';
                    header("Location:../../index.php?product=list-product");
                    exit();
                }
            } else {
                $errors['sql_error'] = 'Lỗi khi thêm sản phẩm vào cơ sở dữ liệu.';
            }
        } else {
            $errors['file_upload'] = 'Không thể tải lên tệp ảnh chính.';
        }
    }

    // Lưu lỗi vào session và quay lại trang form
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header("Location:../../index.php?product=add-product");
    exit();
}
?>
