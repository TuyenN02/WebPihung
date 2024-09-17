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
</head>
<body>

<?php
if (isset($_GET['id'])) {
    // Lấy tên danh mục dựa vào ID_DanhMuc
    $sql_category_name = "SELECT TenDanhMuc FROM danhmuc WHERE ID_DanhMuc='$_GET[id]'";
    $query_category_name = mysqli_query($mysqli, $sql_category_name);
    $row_category_name = mysqli_fetch_array($query_category_name);

    // Lấy sản phẩm theo ID_DanhMuc
    $sql_category_product = "SELECT * FROM sanpham WHERE ID_DanhMuc='$_GET[id]' ORDER BY ID_SanPham DESC";
    $query_category_product = mysqli_query($mysqli, $sql_category_product);
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-2 category-list">
            <?php include('pages/main/product/categoryList.php')?>
        </div>
        <div class="col-lg-10 product-container">
            <!-- Thay "Tất cả cây" bằng tên danh mục -->
            <h1 class="text-center"><?php echo isset($row_category_name['TenDanhMuc']) ? $row_category_name['TenDanhMuc'] : 'Tất cả cây'; ?></h1>
            <div class="row min-height-100">             
            <?php
            if (isset($_GET['id'])) {
                while ($row_category_product = mysqli_fetch_array($query_category_product)) {
            ?>
                <form class="col-lg-3 col-md-4 col-sm-6 mb-4" action="./index.php?navigate=productInfo&id_product=<?php echo $row_category_product['ID_SanPham']; ?>" method="POST">
                  <div class="card text-center product-card">
                      <a href="./index.php?navigate=productInfo&id_product=<?php echo $row_category_product['ID_SanPham'];?>" class="d-block">
                          <img class="product-img" src="./assets/image/product/<?php echo $row_category_product['Img'];?>" alt="<?php echo $row_category_product['TenSanPham'];?>">
                      </a>
                      <div class="card-body">
                          <a href="./index.php?navigate=productInfo&id_product=<?php echo $row_category_product['ID_SanPham'];?>" class="d-block">
                              <h5><?php echo $row_category_product['TenSanPham']; ?></h5>
                          </a>
                         
                          <h6>
                              <a href="./index.php?navigate=productInfo&id_product=<?php echo $row_category_product['ID_SanPham'];?>" class="d-block">
                                  <?php echo number_format($row_category_product['GiaBan'] ,0,',','.')?> VND
                              </a>
                          </h6>
                      </div>        
                  </div>
                </form>
            <?php
                }
            }
            ?>
          </div>
        </div>
    </div>
</div>
</body>
</html>
