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
    <title>Chính Sách: <?php echo htmlspecialchars($policy['TieuDe']); ?></title>
    <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous"/>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .policy-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        .policy-title {
            margin-bottom: 20px;
            font-size: 32px;
            font-weight: bold;
            color: #343a40;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .policy-content {
            font-size: 18px;
            line-height: 1.8;
            color: #495057;
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
        }
    </style>
</head>
<body>
<?php 
    include("./admin/config/connection.php");
    session_start();
?>
    <?php include("./pages/menu.php"); ?>
    
    <div class="policy-container">
        <h1 class="policy-title"><?php echo htmlspecialchars($policy['TieuDe']); ?></h1>
        <div class="policy-content">
            <?php echo nl2br(htmlspecialchars($policy['NoiDung'])); ?>
        </div>
    </div>

    <?php include("./pages/footer.php") ?>


</body>
</html>
