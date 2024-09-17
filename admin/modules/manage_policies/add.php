
<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $TieuDe = trim(mysqli_real_escape_string($mysqli, $_POST['TieuDe']));
    $NoiDung = trim(mysqli_real_escape_string($mysqli, $_POST['NoiDung']));

    // Lưu dữ liệu vào session
    $_SESSION['data'] = [
        'TenDanhMuc' => $TieuDe,
        'NoiDung' => $NoiDung
    ];

    // Kiểm tra tên danh mục có trùng không
    $check_category_query = "SELECT ID_ChinhSach FROM chinhsach WHERE TieuDe='$TieuDe'";
    $check_category_result = mysqli_query($mysqli, $check_category_query);

    if (mysqli_num_rows($check_category_result) > 0) {
        echo json_encode(['status' => 'error', 'message' => "Tên chính sách đã tồn tại!"]);
        exit();
    }

    // Thêm danh mục vào cơ sở dữ liệu
    $sql_insert = "INSERT INTO chinhsach (TieuDe, NoiDung) 
                   VALUES ('$TieuDe', '$NoiDung')";

    if (mysqli_query($mysqli, $sql_insert)) {
        unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi thêm thành công
        $_SESSION['success'] = "Thêm chính sách thành công!"; // Lưu thông báo thành công vào session
        echo json_encode(['status' => 'success', 'redirect' => 'index.php?policy=list-policy']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Thêm thất bại. Vui lòng thử lại."]);
    }
}
?>
