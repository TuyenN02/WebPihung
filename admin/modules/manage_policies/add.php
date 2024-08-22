<?php
session_start();
include("../../config/connection.php");

if (isset($_POST['submit'])) {
    $TieuDe = trim(mysqli_real_escape_string($mysqli, $_POST['TieuDe']));
    $NoiDung = trim(mysqli_real_escape_string($mysqli, $_POST['NoiDung']));

    $_SESSION['data'] = [
        'TieuDe' => $TieuDe,
        'NoiDung' => $NoiDung,
    ];

    $errors = [];

    if (empty($TieuDe)) {
        $errors['TieuDe'] = "Tiêu đề không được bỏ trống.";
    } elseif (strlen($TieuDe) < 4) {
        $errors['TieuDe'] = "Tiêu đề phải có ít nhất 3 ký tự.";
    } elseif (strlen($TieuDe) > 100) {
        $errors['TieuDe'] = "Tiêu đề không được vượt quá 100 ký tự.";
    }

    if (empty($NoiDung)) {
        $errors['NoiDung'] = "Nội dung không được bỏ trống.";
    } elseif (strlen($NoiDung) < 10) {
        $errors['NoiDung'] = "Nội dung chính sách phải có ít nhất 10 ký tự.";
    }

    $check_title_query = "SELECT * FROM chinhsach WHERE TieuDe = '$TieuDe'";
    $check_title_result = mysqli_query($mysqli, $check_title_query);
    if (mysqli_num_rows($check_title_result) > 0) {
        $errors['TieuDe'] = "Tiêu đề chính sách đã tồn tại.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../../index.php?policy=add-policy");
        exit();
    }

    $sql = "INSERT INTO chinhsach(TieuDe, NoiDung) VALUES('$TieuDe', '$NoiDung')";
    mysqli_query($mysqli, $sql);
    unset($_SESSION['data']);
    $_SESSION['success'] = "Thêm mới chính sách thành công!";
    header("Location: ../../index.php?policy=list-policy");
    exit();
}
?>
