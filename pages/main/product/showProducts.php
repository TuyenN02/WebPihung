<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
      .text-center::after {
        content: "";
        width: 100px;
        height: 3px;
        background: #007bff;
        display: block;
        margin: 10px auto;
      }
      .product-img {
        object-fit: cover;
        height: 200px;
        width: 100%;
      }
      .product-title, .product-price {
        cursor: pointer;
      }
      .product-container {
        background-color: #d7edd7; /* Màu nền xanh lá sáng */
        padding: 20px;
        border-radius: 10px;
      }
      .product-card {
        background-color: #C8E6C9; /* Màu nền xanh lá sáng cho thẻ sản phẩm */
        border: 1px solid #A5D6A7; /* Viền màu xanh lá nhạt cho thẻ sản phẩm */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background-color: #A5D6A7; /* Màu nền xanh lá nhạt khi hover */
      }
      .pagination-container {
        background-color: #C8E6C9; /* Màu nền xanh lá sáng cho phân trang */
        padding: 10px;
        border-radius: 10px;
      }
    </style>
<?php
  if(isset($_GET['trang'])){
    $page = $_GET['trang'];
  } else{
    $page = 1;
  }
  if($page == '' || $page == 1){
    $begin = 0;
  } else{
    $begin = ($page*8)-8;
  }
  if(isset($_POST['desc'])){
    $sql_product = "SELECT * FROM sanpham ORDER BY GiaBan  DESC LIMIT $begin,8";
  }
  else if(isset($_POST['asc'])){
    $sql_product = "SELECT * FROM sanpham ORDER BY GiaBan  ASC LIMIT $begin,8";
  }
  else if(isset($_POST['hot'])){
    $sql_product = "SELECT *
    FROM sanpham
    WHERE ID_SanPham IN (
        SELECT ID_SanPham
        FROM chitietdonhang
        GROUP BY ID_SanPham
        ORDER BY SUM(SoLuong) DESC
    )";
  }
  else {
    $sql_product = "SELECT * FROM sanpham ORDER BY ID_SanPham DESC LIMIT $begin,8";
  }
  $query_product = mysqli_query($mysqli,$sql_product);
?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-2 category-list">
        <?php include('pages/main/product/categoryList.php')?>
      </div>
      <div class="col-lg-10 product-container">
        <h2 class="text-center animate__animated animate__fadeInDown">Tất cả cây</h2>
        <div>
          <form action="" method="POST">
            <ul class="nav">
              <li class="nav-item mr-2 mb-2"><p class="btn btn-success">Sắp sếp theo:</p></li>
              <li class="nav-item mr-2 mb-2"><input class="btn btn-info" type="submit" value="Bán chạy" name="hot"></li>
              <li class="nav-item mr-2 mb-2"><input class="btn btn-info" type="submit" value="Giá giảm dần" name="desc"></li>
              <li class="nav-item mr-2 mb-2"><input class="btn btn-info" type="submit" value="Giá tăng dần" name="asc"></li>
            </ul>
          </form>
        </div>
        <div class="row min-height-100">
        <?php
        while($row_product = mysqli_fetch_array($query_product)){
        ?>    
        <form class="col-lg-3 col-md-4 col-sm-6 mb-4" action="./index.php?navigate=productInfo&id_product=<?php echo $row_product['ID_SanPham'];?>" method="POST">
          <div class="card text-center product-card mb-4">
            <a href="./index.php?navigate=productInfo&id_product=<?php echo $row_product['ID_SanPham'];?>">
              <img class="card-img-top product-img" src="./assets/image/product/<?php echo $row_product['Img'];?>"/>
            </a>
            <div class="card-body">
              <a href="./index.php?navigate=productInfo&id_product=<?php echo $row_product['ID_SanPham'];?>" class="product-title">
                <h5><?php echo $row_product['TenSanPham']; ?></h5>
              </a>
              <h6><?php echo number_format($row_product['GiaBan'] )?> VND</h6>
              <?php if(isset($_SESSION['TenDangNhap'])) { 
                ?>
              <?php }else{ ?>
              <input type="submit" class="btn btn-info" name='submit' value="Xem chi tiết">
              <?php 
              } 
              ?>
            </div>        
          </div>
        </form>
        <?php
        }
        ?>
      </div>
      <div class="pagination-container">
        <?php
        $sql_trang = mysqli_query($mysqli, "SELECT * FROM sanpham");
        $row_count = mysqli_num_rows($sql_trang);  
        $trang = ceil($row_count/8);
        ?>				
        <ul class="d-flex justify-content-center list-unstyled">
          <?php
            for($i=1;$i<=$trang;$i++){ 
          ?>
            <li class="m-1 bg-white" <?php if($i==$page){echo 'style="background: #ccc !important;"';}else{ echo '';}?>>
              <a class="d-block p-2 text-dark" href="index.php?navigate=showProducts&trang=<?php echo $i ?>"><?php echo $i ?></a>
            </li>
          <?php
          } 
          ?>
        </ul>
      </div>
      </div>
    </div>
  </div>
</body>
</html>
