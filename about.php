<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Giới thiệu</title>
  <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous"/>
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/responsive.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    .image-container {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
    }

    .image-container img {
      width: 300px;
      height: 200px;
      object-fit: cover;
      transition: transform 0.3s ease-in-out;
    }

    .image-container img:hover {
      transform: scale(1.1);
    }

    .section-title {
      margin-top: 20px;
      font-size: 24px;
      font-weight: bold;
      text-align: center;
      color: #333;
      position: relative;
    }

    .section-title::after {
      content: "";
      width: 100px;
      height: 3px;
      background: #007bff;
      display: block;
      margin: 10px auto;
    }

    .section {
      margin: 20px 90px;
    }

    .section p {
      text-align: justify;
      font-size: 17px;

    }
  </style>
</head>
<body>
<?php 
    include("./admin/config/connection.php");
    session_start();
?>
<?php include("./pages/menu.php") ?>

<div class="container">
    <div class="section">
        <h2 class="section-title animate__animated animate__fadeInDown">Giới thiệu cửa hàng</h2>
        <p>Chào mừng quý khách đến với Shop Cây Cảnh Pi Hưng, một địa chỉ uy tín và chất lượng cho những ai yêu thích cây cảnh và muốn mang thiên nhiên vào không gian sống của mình. Tọa lạc tại trung tâm thành phố, Shop Cây Cảnh Pi Hưng tự hào là một trong những cửa hàng cây cảnh hàng đầu với đa dạng các loại cây cảnh, từ những loại cây phổ biến như cây kim ngân, cây lưỡi hổ, cây phú quý, đến những loại cây đặc biệt và hiếm có như cây bonsai, cây thủy sinh, và cây không khí.</p>
        <p>Shop Cây Cảnh Pi Hưng không chỉ cung cấp cây cảnh mà còn mang đến những giải pháp toàn diện cho việc trang trí và chăm sóc cây xanh. Chúng tôi hiểu rằng mỗi loại cây đều cần có những điều kiện chăm sóc riêng biệt để phát triển tốt nhất. Do đó, Pi Hưng cam kết cung cấp cho khách hàng những hướng dẫn chi tiết và tận tình về cách chăm sóc từng loại cây. Đội ngũ nhân viên của chúng tôi đều là những chuyên gia trong lĩnh vực cây cảnh, sẵn sàng tư vấn và giải đáp mọi thắc mắc của quý khách.</p>
    </div>
    
    <div class="section">
        <h2 class="section-title animate__animated animate__fadeInDown">Hình ảnh về cửa hàng</h2>
        <div class="image-container">
            <img src="./assets/image/logo/b5.jpg" alt="Image 1" class="animate__animated animate__fadeIn">
            <img src="./assets/image/logo/b6.jpg" alt="Image 2" class="animate__animated animate__fadeIn">
            <img src="./assets/image/logo/b7.jpg" alt="Image 3" class="animate__animated animate__fadeIn">
        </div>
        <div class="image-container">
            <img src="./assets/image/logo/b8.jpg" alt="Image 1" class="animate__animated animate__fadeIn">
            <img src="./assets/image/logo/b10.jpg" alt="Image 2" class="animate__animated animate__fadeIn">
            <img src="./assets/image/logo/b11.jpg" alt="Image 3" class="animate__animated animate__fadeIn">
        </div>
    </div>

    <div class="section">
        <h2 class="section-title animate__animated animate__fadeInDown">Chủ cửa hàng</h2>
        <p>Chủ cửa hàng: Khuất Thanh Ngoan </p>
        <div class="image-container">
            <img src="./assets/image/logo/b12.jpg" alt="Owner Image 1" class="animate__animated animate__fadeIn">
            <img src="./assets/image/logo/b13.jpg" alt="Owner Image 2" class="animate__animated animate__fadeIn">
        </div>
    </div>
</div>

<?php include("./pages/footer.php") ?>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
