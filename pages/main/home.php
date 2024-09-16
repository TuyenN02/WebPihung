<?php



// Kiểm tra và hiển thị thông báo thành công
if (isset($_SESSION['successMessage'])) {
    $successMessage = $_SESSION['successMessage'];
    unset($_SESSION['successMessage']); // Xóa thông báo sau khi đã hiển thị
}

$sql_product = "SELECT * FROM sanpham
                ORDER BY ID_SanPham DESC
                LIMIT 8"; // Hiển thị 8 sản phẩm mới nhất

$query_product = mysqli_query($mysqli, $sql_product);

// Truy vấn lấy 3 bài viết mới nhất
$sql_posts = "SELECT ID_baiviet, Tenbaiviet, Img, Noidung FROM posts ORDER BY ID_baiviet DESC LIMIT 3";
$result = $mysqli->query($sql_posts);

// Kiểm tra truy vấn
if (!$query_product) {
    die("Truy vấn sản phẩm thất bại: " . mysqli_error($mysqli));
}

// Truy vấn nhà cung cấp
$sql_supplier = "SELECT * FROM nhacungcap LIMIT 3";
$query_supplier = mysqli_query($mysqli, $sql_supplier);

// Kiểm tra truy vấn
if (!$query_supplier) {
    die("Truy vấn nhà cung cấp thất bại: " . mysqli_error($mysqli));
}
?>
<?php if (isset($_SESSION['login_success'])): ?>
    <div class="alert alert-success alert-custom text-center" role="alert">
        <?php echo $_SESSION['login_success']; ?>
        <?php unset($_SESSION['login_success']); // Xóa thông báo sau khi hiển thị ?>
    </div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Shop Cây Cảnh Pi Hưng</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    /* Carousel Styles */
    .carousel-item img {
        object-fit: cover;
        height: 500px; /* Điều chỉnh chiều cao hình ảnh */
        width: 300%; /* Đảm bảo hình ảnh rộng đầy đủ */
    }
    .carousel-caption p {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 5px;
        padding: 10px;
        font-size: 1.25rem;
    }

    /* Product Cards */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .product-img {
        object-fit: cover;
        height: 300px;
        cursor: pointer; /* Thay đổi con trỏ khi di chuột qua ảnh */
    }

    .product-price, .product-title {
        cursor: pointer; /* Thay đổi con trỏ khi di chuột qua giá tiền hoặc tên sản phẩm */
    }

    /* Supplier Cards */
    .supplier .card {
        border-radius: 30px;
        overflow: hidden;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center; /* Căn giữa tất cả nội dung trong thẻ card */
        justify-content: center; /* Căn giữa tất cả nội dung trong thẻ card */
        height: 300px; /* Đặt chiều cao cố định cho card */
        width: 100%; /* Thẻ card chiếm toàn bộ chiều rộng cột */
        max-width: 350px; /* Giới hạn chiều rộng tối đa của card */
        margin: 0 auto; /* Căn giữa card trong cột */
    }

    .supplier .card-img-top {
        display: flex;
        justify-content: center; /* Căn giữa ảnh trong thẻ card */
        margin-bottom: 5px; /* Khoảng cách giữa ảnh và các phần tử khác */
    }

    .supplier .card-img-top img {
        width: 200px; /* Đặt chiều rộng cố định cho ảnh */
        height: 200px; /* Đặt chiều cao cố định cho ảnh */
        object-fit: cover; /* Đảm bảo ảnh không bị biến dạng */
        border-radius: 80%; /* Làm cho ảnh có hình tròn */
    }

    .supplier .card-body {
        text-align: center; /* Căn giữa văn bản trong thẻ card-body */
        flex: 1; /* Giúp card-body chiếm không gian còn lại */
    }

    /* General Styles */
    .text-dark {
        color: #343a40;
    }
    .text-secondary {
        color: #6c757d;
    }
    .mt-60 {
        margin-top: 60px;
    }
    .card-body p {
        height: 150px;
        overflow: hidden;
    }

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
        width: 90%;
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
    .footer {
        background-color: #007bff;
        color: #ffffff;
        padding: 20px;
        text-align: center;
    }
    /* Supplier Cards */
    .supplier .card-img-top img {
        object-fit: cover;
        height: 150px; /* Chiều cao ảnh */
        width: 150px; /* Chiều rộng ảnh */
        margin: 0 auto; /* Căn giữa hình ảnh trong thẻ card */
        border-radius: 5px; /* Bo tròn các góc ảnh */
    }

    /* Tiêu đề phần bài viết */
    .posts-container h2 {
        font-size: 32px; /* Điều chỉnh kích thước tiêu đề phần bài viết */
        font-weight: bold;
        color: #343a40;
    }

    /* Tiêu đề phần nhà cung cấp */
    .supplier h2 {
        font-size: 24px; /* Điều chỉnh kích thước tiêu đề phần nhà cung cấp */
        font-weight: bold;
        color: #343a40;
    }
/* Styles for success alert */
.alert-success {
    position: fixed; /* Fixed position to keep the alert in place */
    top: 10px; /* Distance from the top of the viewport */
    right: 10px; /* Distance from the right edge of the viewport */
    background-color: #d4edda; /* Light green background */
    color: #155724; /* Dark green text color */
    border: 1px solid #c3e6cb; /* Light green border */
    padding: 15px 25px; /* Padding around the message */
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for better visibility */
    z-index: 9999; /* Ensure it appears on top of other elements */
    opacity: 1; /* Make sure it's visible initially */
    transition: opacity 0.5s ease; /* Smooth transition for fading out */
}
.alert-success.fade-out {
    opacity: 0; /* Fade out effect */
}
</style>

    

</head>
<body>
    <!-- Carousel -->
    <div id="slides" class="carousel slide" data-ride="carousel">
        <ul class="carousel-indicators">
            <li data-target="#slides" data-slide-to="0" class="active"></li>
            <li data-target="#slides" data-slide-to="1"></li>
            <li data-target="#slides" data-slide-to="2"></li>
        </ul>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="./assets/image/banner/plant-7498330_1920.jpg" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                    <p>"Tạo không gian xanh cho ngôi nhà bạn!"</p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="./assets/image/banner/succulent-4713264_1920.jpg" alt="Second slide">
                <div class="carousel-caption d-none d-md-block">
                    <p>"Thêm phần nghệ thuật, độc đáo!"</p>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="./assets/image/banner/b1.png" alt="Third slide">
                <div class="carousel-caption d-none d-md-block">
                    <p>"Trang trí góc làm việc của bạn!"</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="container mt-60">
        <h2 class="text-dark text-center">Sản phẩm nổi bật</h2>
        <p class="text-secondary text-center">
            <i>Những sản phẩm được khách hàng ưu thích nhất</i>
        </p>
        <div class="row">
            <?php while ($row_product = mysqli_fetch_array($query_product)) { ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card text-center product-card">
                        <a href="index.php?navigate=productInfo&id_product=<?php echo $row_product['ID_SanPham']; ?>">
                            <img src="./assets/image/product/<?php echo $row_product['Img']; ?>" class="card-img-top product-img" alt="<?php echo $row_product['TenSanPham']; ?>">
                        </a>
                        <div class="card-body">
                            <a href="index.php?navigate=productInfo&id_product=<?php echo $row_product['ID_SanPham']; ?>" class="product-title">
                                <h5 class="card-title"><?php echo $row_product['TenSanPham']; ?></h5>
                            </a>
                            <p style="height: 22px">
                                Số lượng: 
                                <?php 
                                if ($row_product['SoLuong'] > 0) {
                                    echo htmlspecialchars($row_product['SoLuong']);
                                } else {
                                    echo '<span style="color: red;">Đã hết hàng</span>';
                                }
                                ?>
                            </p>

                            
                            <h6 class="text-danger"> 
                            </h6>
                            <a href="index.php?navigate=productInfo&id_product=<?php echo $row_product['ID_SanPham']; ?>" class="product-price">
                                <p style = "height: 22px"> Giá bán: <?php echo number_format($row_product['GiaBan'] ) ?> VND</p>
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="posts-container">
    
    <h2 class="text-dark text-center">Bài viết mới</h2>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="post-item">
                <a href="articleDetail.php?id=<?php echo $row['ID_baiviet']; ?>">
                    <img src="./assets/image/supplier/<?php echo htmlspecialchars($row['Img']); ?>" alt="<?php echo htmlspecialchars($row['Tenbaiviet']); ?>" class="post-image">
                </a>
                <div>
                    <a href="articleDetail.php?id=<?php echo $row['ID_baiviet']; ?>" class="post-title"><?php echo htmlspecialchars($row['Tenbaiviet']); ?></a>
                    <div class="post-content">
                        <p><?php echo substr(strip_tags($row['Noidung']), 0, 150); ?>...</p> <!-- Hiển thị 150 ký tự đầu tiên của nội dung -->
                        <a href="articleDetail.php?id=<?php echo $row['ID_baiviet']; ?>" class="read-more">Xem thêm</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        
    </div>
<!-- Suppliers -->
<!-- Suppliers -->
<section class="supplier mt-60">
    <div class="container text-center">
        <h2 class="text-dark">Một số nhà cung cấp</h2>
        <p class="text-dark">
            <i>Những đối tác đáng tin cậy cung cấp cây cảnh chất lượng cho không gian của bạn!</i>
        </p>
    </div>
    <div class="row">
        <?php while ($row_supplier = mysqli_fetch_array($query_supplier)) { ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-img-top">
                        <img src="./assets/image/supplier/<?php echo htmlspecialchars($row_supplier['Img']); ?>" 
                             alt="<?php echo htmlspecialchars($row_supplier['TenNCC']); ?>">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row_supplier['TenNCC']); ?></h5>
                        <p class="card-text text-muted mb-1">
                            <i class="fas fa-phone-alt"></i> SĐT: <?php echo htmlspecialchars($row_supplier['SoDienThoai']); ?>
                        </p>
                        <p class="card-text text-muted">
                            <i class="fas fa-envelope"></i> Email: <?php echo htmlspecialchars($row_supplier['Email']); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var alert = document.querySelector('.alert-custom');
    if (alert) {
        setTimeout(function() {
            alert.classList.add('fade-out');
        }, 2000); // 2 giây
    }
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($successMessage)) { ?>
            Swal.fire({
                title: 'Thành công!',
                text: '<?php echo $successMessage; ?>',
                icon: 'success',
                timer: 2000, // Thời gian hiển thị thông báo (2 giây)
                showConfirmButton: false
            });
        <?php } ?>
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($successMessage)) { ?>
            Swal.fire({
                title: 'Thành công!',
                text: '<?php echo $successMessage; ?>',
                icon: 'success',
                timer: 2000, // Thời gian hiển thị thông báo (2 giây)
                showConfirmButton: false
            });
        <?php } ?>

        <?php if (isset($_SESSION['loginSuccessMessage'])) { ?>
            Swal.fire({
                title: 'Đăng nhập thành công!',
                text: '<?php echo $_SESSION['loginSuccessMessage']; ?>',
                icon: 'success',
                timer: 2000, // Thời gian hiển thị thông báo (2 giây)
                showConfirmButton: false
            });
            <?php unset($_SESSION['loginSuccessMessage']); // Xóa thông báo sau khi đã hiển thị ?>
        <?php } ?>
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var alert = document.getElementById('login-success-alert');
        if (alert) {
            setTimeout(function() {
                alert.classList.add('fade-out');
            }, 2000); // 2000 milliseconds = 2 seconds
        }
    });
</script>
</body>
</html>
