<?php
session_start();
include("config/connection.php");
if (!isset($_SESSION['admin'])) {
    header('location: login.php');
}
?>

<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link
            rel="stylesheet"
            href="../assets/bootstrap/css/bootstrap.min.css"
        />
        <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
        <link
            rel="stylesheet"
            href="../assets/bootstrap/js/bootstrap.bundle.js"
        />
        <link
            rel="stylesheet"
            href="../assets/bootstrap/js/bootstrap.bundle.min.js"
        />
        <link
            rel="stylesheet"
            href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
            integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="css/style.css" />
        <title>Admintrator</title>
        <style>
            /* style.css */

/* Đảm bảo rằng thanh sidebar và nội dung nằm trên cùng một hàng và có cùng chiều cao */
#page-body {
    display: flex;
    flex: 1;
}

body {
    background-color: #a0e5a0; 
}

#warpper {
    background-color: #a0e5a0;
}

#page-body {
    background-color: #aeefce;
}

#sidebar {
    width: 250px; /* Điều chỉnh chiều rộng của sidebar */
    height: 100vh; /* Đảm bảo thanh sidebar dài bằng chiều cao của viewport */
    position: fixed; /* Cố định vị trí của sidebar */
    margin-top: 50px;
    left: 0;
    overflow-y: auto; /* Thêm thanh cuộn dọc nếu nội dung quá dài */
    
}

#wp-content {
    margin-left: 250px; /* Đẩy nội dung sang phải bằng chiều rộng của sidebar */
    flex: 1;
    padding: 5px; /* Thêm khoảng cách xung quanh nội dung */
}

/* Khoảng cách giữa các mục trong sidebar */
#sidebar-menu li {
    margin-bottom: 20px; /* Thay đổi giá trị để điều chỉnh khoảng cách */
}

.sub-menu li {
    margin-bottom: 5px; /* Thay đổi giá trị để điều chỉnh khoảng cách */
}

.topnav {
    display: flex;
    justify-content: space-between; /* Đẩy các phần tử ra hai bên */
    align-items: center; /* Căn giữa các phần tử theo chiều dọc */
    padding: 10px 20px; /* Giảm khoảng cách xung quanh thanh điều hướng */
    margin: 0; /* Loại bỏ margin để thanh điều hướng nằm sát mép trên */
    color:#7cc97c;
}

.topnav .navbar-brand {
    margin-right: 20px; /* Thay đổi giá trị để điều chỉnh khoảng cách */
}

.topnav .btn-danger {
    margin-left: 20px; /* Thay đổi giá trị để điều chỉnh khoảng cách */
}      </style>

    </head>
    <body>
        <div id="warpper" class="nav-fixed">
            <nav class="topnav shadow navbar-light bg-white d-flex">
            <div class="navbar-brand">
    <a href="index.php" style="color: #228B22;">Shop cây cảnh Pi Hưng - Admin</a>
</div>
               
            <a class="btn btn-danger mr-2" href="logout.php">Thoát</a>
            </div>
            </nav>
            <div id="page-body" class="d-flex">
                <div id="sidebar" class="bg-white">
                    <ul id="sidebar-menu">
                    <li class="nav-link">
                            <a href="index.php">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                Thống kê
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="?ncc=list-ncc">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="fas fa-city"></i>
                                </div>
                                Quản lý nhà cung cấp
                            </a>
                          
                        </li>

                        <li class="nav-link">
                            <a href="?product=list-product">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="fas fa-archive"></i>
                                </div>
                                Quản lý sản phẩm
                            </a>
                         
                            
                        </li>
                        <li class="nav-link">
    <a href="?cat=list-cat">
        <div class="nav-link-icon d-inline-flex">
            <i class="fas fa-tags"></i>
        </div>
        Quản lý danh mục
    </a>
  
</li>
                        <li class="nav-link">
    <a href="?info=info">
        <div class="nav-link-icon d-inline-flex">
            <i class="	far fa-address-book"></i>
        </div>
        Quản lý thông tin
    </a>
</li>
   
<li class="nav-link">
    <a href="?posts=list-posts">
        <div class="nav-link-icon d-inline-flex">
            <i class="far fa-newspaper"></i>
        </div>
        Quản lý bài viết
    </a>
   
</li>
    <li class="nav-link">
    <a href="?policy=list-policy">
        <div class="nav-link-icon d-inline-flex">
            <i class="fas fa-book"></i>
        </div>
        Quản lý chính sách
    </a>
   
</li>

                        <li class="nav-link">
                            <a href="index.php?order=success-order-list">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                               Quản lý đơn hàng
                            </a>
               
</li>
                        <li class="nav-link">
                            <a href="?user=list-user">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="fas fa-users"></i>
                                </div>
                                Quản lý tài khoản
                            </a>
                            
                        </li>
                        <li class="nav-link">
                          <a href="?comment=comments">
                          <div class="nav-link-icon d-inline-flex">
                          <i class="far fa-comments"></i>
                             </div>
                            Quản lý bình luận
                            </a>
                         </li>
       
                    </ul>
                </div>
                <div id="wp-content">
    <?php
    if (isset($_GET['user'])) {
        $user = $_GET['user'];
        include "modules/manage_users/{$user}.php";
    } else if (isset($_GET['product'])) {
        $product = $_GET['product'];
        include "modules/manage_products/{$product}.php";
    } else if (isset($_GET['cat'])) {
        $cat = $_GET['cat'];
        include "modules/manage_categories/{$cat}.php";
    }else if (isset($_GET['ncc'])) {
        $ncc = $_GET['ncc'];
        require "modules/manage_suppliers/{$ncc}.php";
    } else if (isset($_GET['order'])) {
        $order = $_GET['order'];
        require "modules/manage_orders/{$order}.php";
    } else if (isset($_GET['sta'])) {
        $sta = $_GET['sta'];
        require "modules/statistical_tables/{$sta}.php";
    } else if (isset($_GET['info'])) {
        $info = $_GET['info'];
        if ($info == 'info') {
            require "modules/manage_info/info.php";
        } else {
            require "modules/manage_info/{$info}.php";
        }
    }
    else if (isset($_GET['policy'])) {
        $policy = $_GET['policy'];
        require "modules/manage_policies/{$policy}.php";
    } 
    else if (isset($_GET['posts'])) {
        $posts = $_GET['posts'];
        require "modules/manage_posts/{$posts}.php";
    } else if (isset($_GET['comment'])) {
        require "modules/manage_comment/comments.php";
    } else {
        require "modules/statistical_tables/admin_dashboard.php";
    }
    ?>
</div>


            </div>
        </div>
    </body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"
    ></script>
    <script
        src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"
    ></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="js/app.js"></script>
    <script src="js/thongke.js"></script>
</html>
