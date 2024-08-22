<?php
// Kết nối đến cơ sở dữ liệu

// Lấy thông tin từ bảng thongtin
$sql_info = "SELECT * FROM thongtin LIMIT 1";
$query_info = mysqli_query($mysqli, $sql_info);
$info = mysqli_fetch_assoc($query_info);

// Lấy danh sách chính sách từ bảng chinhsach
$sql_policies = "SELECT * FROM chinhsach";
$query_policies = mysqli_query($mysqli, $sql_policies);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .footer {
            background-color: #2d3e50;
            color: #ffffff;
            padding: 40px 0;
        }
        .footer a {
            color: #ffffff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .footer .social-icons a {
            margin: 0 10px;
            font-size: 20px;
        }
        .footer .contact-info p, 
        .footer .contact-info a {
            margin-bottom: 10px;
        }
        .footer .policies ul {
            list-style: none;
            padding-left: 0;
        }
        .footer .policies ul li {
            margin-bottom: 10px;
        }
        .footer .policies ul li a {
            color: #ffffff;
            text-decoration: none;
        }
        .footer .policies ul li a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Footer -->
    <div class="footer mt-60">
        <div class="container">
            <div class="row">
                <!-- Contact Info -->
                <div class="col-md-4 col-sm-12 contact-info">
                    <h4>Thông tin liên hệ</h4>
                    <p><i class="fas fa-clock"></i> Giờ làm việc từ: <?php echo date("H:i", strtotime($info['gio_lam_viec'])); ?> </p>
                    <p><i class="fas fa-clock"></i> Giờ nghỉ: <?php echo date("H:i", strtotime($info['gio_nghi'])); ?> </p>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo $info['DiaChi']; ?></p>
                    <p><i class="fas fa-phone"></i> <a href="tel:+<?php echo $info['SDT']; ?>">Liên hệ: <?php echo $info['SDT']; ?></a></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?php echo $info['Email']; ?>">Email: <?php echo $info['Email']; ?></a></p>
                </div>
                <!-- Policies -->
                <div class="col-md-4 col-sm-12 policies">
                    <h4>Danh sách chính sách</h4>
                    <ul>
                        <?php while ($policy = mysqli_fetch_assoc($query_policies)): ?>
                            <li>
                                <a href="policy.php?id=<?php echo $policy['ID_ChinhSach']; ?>">
                                    <?php echo $policy['TieuDe']; ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <!-- Social Media -->
                <div class="col-md-4 col-sm-12">
                    <h4>Theo dõi chúng tôi</h4>
                    <div class="social-icons">
                        <a href="#!" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#!" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#!" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#!" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
</body>
</html>