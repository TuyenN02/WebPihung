<?php

include("./admin/config/connection.php");

// Kiểm tra xem ID của chính sách có được truyền qua URL hay không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Lấy ID chính sách từ URL và bảo vệ khỏi SQL Injection
    $policy_id = intval($_GET['id']);

    // Truy vấn lấy thông tin chính sách từ cơ sở dữ liệu
    $sql_policy = "SELECT * FROM chinhsach WHERE ID_ChinhSach = ?";
    $stmt = $mysqli->prepare($sql_policy);
    $stmt->bind_param("i", $policy_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra xem chính sách có tồn tại hay không
    if ($result->num_rows > 0) {
        $policy = $result->fetch_assoc();
    } else {
        // Nếu không tìm thấy chính sách, chuyển hướng về trang lỗi hoặc trang khác
        header("Location: error.php");
        exit();
    }
} else {
    // Nếu không có ID hoặc ID không hợp lệ, chuyển hướng về trang chủ hoặc trang lỗi
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($policy['TieuDe']); ?></title>
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .post-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            position: relative;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 5px 12px;
            font-size: 14px;
            color: #fff;
            background-color: #6ab780;
            border-radius: 5px;
            text-decoration: none;
            position: absolute;
            top: 20px;
            left: 20px; /* Chuyển vị trí nút về bên trái */
        }
        .post-title {
            margin-bottom: 20px;
            font-size: 34px;
            font-weight: bold;
            color: #343a40;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
            text-align: center;
        }
        .post-content {
            font-size: 18px;
            line-height: 1.8;
            color: #495057;
            margin-top: 20px;
        }
        .post-image {
            width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        .footer {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <?php include("./admin/config/connection.php"); session_start(); ?>
    
    <?php include("./pages/menu.php") ?>
    <div class="post-container">
    <h1 class="post-title" style="text-align: center;"><?php echo htmlspecialchars($policy['TieuDe']); ?></h1>
       
        <div class="post-content">
        <?php echo nl2br(htmlspecialchars($policy['NoiDung'])); ?>
        </div>
        
        <!-- Nút "Quay lại" -->
        <a href="javascript:history.back()" class="back-button">Quay lại</a>

    </div>

    <?php include("./pages/footer.php"); ?>
</body>
</html>
