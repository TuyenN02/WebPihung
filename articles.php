<?php
include("./admin/config/connection.php");

// Truy vấn lấy danh sách bài viết từ cơ sở dữ liệu
$sql_posts = "SELECT ID_baiviet, Tenbaiviet, Img, Noidung FROM posts ORDER BY ID_baiviet DESC";
$result = $mysqli->query($sql_posts);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Danh sách bài viết</title>
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
        .posts-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        .post-item {
            margin-bottom: 40px;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
        }
        .post-image {
            max-width: 300px;
            width: 100%;
            height: auto;
            margin-right: 20px;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        .post-image:hover {
            transform: scale(1.05);
        }
        .post-title {
            font-size: 24px;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 10px;
            text-decoration: none;
        }
        .post-content {
            font-size: 16px;
            color: #495057;
            line-height: 1.6;
        }
        .post-content p {
            margin-bottom: 10px;
        }
        .read-more {
            font-size: 16px;
            color: #007bff;
            text-decoration: none;
        }
        .read-more:hover {
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
       
    </style>
</head>
<body>
<?php 
    include("./admin/config/connection.php");
    session_start();
?>
    <?php include("./pages/menu.php"); ?>

    <div class="posts-container">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post-item">
                <a href="articleDetail.php?id=<?php echo $row['ID_baiviet']; ?>">
                    <img src="./assets/image/supplier/<?php echo htmlspecialchars($row['Img']); ?>" alt="<?php echo htmlspecialchars($row['Tenbaiviet']); ?>" class="post-image">
                </a>
                <div>
                    <a href="articleDetail.php?id=<?php echo $row['ID_baiviet']; ?>" class="post-title"><?php echo htmlspecialchars($row['Tenbaiviet']); ?></a>
                    <div class="post-content">
                        <p><?php echo substr(strip_tags($row['Noidung']), 0, 50); ?>...</p>
                        <a href="articleDetail.php?id=<?php echo $row['ID_baiviet']; ?>" class="read-more">Xem thêm</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php include("./pages/footer.php"); ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
