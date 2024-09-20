<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Danh mục cây</title>
    <style>
   .category-list {
    background-color: #33CC66; /* Màu nền tối */
    padding: 1px; /* Đã tăng khoảng cách padding */
    border-radius: 80px;
   
    margin: 0 auto; /* Căn giữa trong container */
    
}

        .category-list p {
            font-size: 0.8rem; /* Tăng kích thước chữ tiêu đề */
            font-weight: bold;
            color: white; /* Màu chữ trắng */
        }
        .category-list a {
            display: block;
            padding: 10px;
            color: white; /* Màu chữ trắng */
            text-decoration: none;
            transition: background-color 0.3s ease, font-size 0.3s ease, font-weight 0.3s ease;
            font-size: 0.8rem; /* Tăng kích thước chữ liên kết */
            font-weight: bold; /* Làm chữ đậm hơn */
            white-space: nowrap; /* Không cho phép xuống dòng */
        }
        .category-list a:hover {
            background-color: #339999; /* Màu nền khi hover */
            border-radius: 80px;
            font-size: 0.8rem; /* Tăng kích thước chữ khi hover */
            font-weight: bolder; /* Làm chữ đậm hơn khi hover */
            text-decoration: none; /* Loại bỏ gạch chân khi hover */
        }
    </style>
</head>
<body>
<?php
$sql_getList = "SELECT * FROM danhmuc ORDER BY ID_DanhMuc ASC";
$query_getList = $mysqli->query($sql_getList);
?>
<div class="container mt-5">
    <div class="category-list text-white">
        <p><i class="fas fa-list"></i> DANH MỤC CÂY</p>
        <ul class="list-unstyled">
            <?php while ($row_getList = $query_getList->fetch_assoc()) { ?>
                <li>
                    <a href="index.php?navigate=category&id=<?php echo $row_getList['ID_DanhMuc']; ?>">
                        <?php echo $row_getList['TenDanhMuc']; ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
</body>
</html>
