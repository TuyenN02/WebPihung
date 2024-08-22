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
     
    /* Giảm khoảng cách bên trái của nội dung */
    #page-body {
        display: flex;
        margin-top: 10px; /* Loại bỏ khoảng cách trên nếu cần */
    }

    #sidebar {
        flex: 0 0 310px; /* Độ rộng của sidebar */
        height: 200%;
        background-color: #4CAF50; /* Màu nền xanh lá cây */
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2); /* Đổ bóng nhẹ cho sidebar */
        padding: 20px; /* Padding xung quanh nội dung */
        margin-top: 20px; /* Loại bỏ khoảng cách trên nếu cần */
    }
    .topnav {
        display: flex;
        justify-content: space-between; /* Căn chỉnh các phần tử với khoảng cách đều */
        align-items: center; /* Căn giữa các phần tử theo chiều dọc */
        padding: 10px 20px; /* Padding cho thanh điều hướng */
    }

    #wp-content {
        flex: 100px; /* Chiếm hết không gian còn lại */
        padding: 1px; /* Padding xung quanh nội dung */
        margin-left: 1px; /* Giảm khoảng cách bên trái nếu cần */
        margin-top: 40px; /* Giảm khoảng cách trên nếu cần */
    }
    .logout-btn {
        margin-left: auto; /* Đẩy nút ra phía cuối cùng hàng */
        height: 35px;
    }

    </style>
    </head>
    <body>
        <div id="warpper" class="nav-fixed">
        <nav class="topnav shadow navbar-light bg-white d-flex">
                <div class="navbar-brand"><a href="index.php">QUẢN LÝ</a></div>
                <!-- Thêm nút đăng xuất với lớp "logout-btn" -->
                <a class="btn btn-danger logout-btn" href="logout.php">Đăng xuất</a>
            </nav>
            <div id="page-body" class="d-flex">
                <div id="sidebar" class="bg-white">
                    <ul id="sidebar-menu">
                    <li class="nav-link">
                            <a href="index.php">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                Thống kê
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="?ncc=list-ncc">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="far fa-folder"></i>
                                </div>
                                Quản lý nhà cung cấp
                            </a>
                            <i class="arrow fas fa-angle-right"></i>
                            <ul class="sub-menu">
                                <li><a href="?ncc=add-ncc">Thêm mới</a></li>
                                <li>
                                    <a href="?ncc=list-ncc">Danh sách</a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-link">
                            <a href="?product=list-product">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="far fa-folder"></i>
                                </div>
                                Quản lý sản phẩm
                            </a>
                            <i class="arrow fas fa-angle-right"></i>
                            <ul class="sub-menu">
                                <li>
                                    <a href="?product=add-product">Thêm mới</a>
                                </li>
                                <li>
                                    <a href="?product=list-product">Danh sách</a>
                                </li>
                            
                            </ul>
                        </li>
                        <li class="nav-link">
    <a href="?cat=list-cat">
        <div class="nav-link-icon d-inline-flex">
            <i class="far fa-folder"></i>
        </div>
        Quản lý danh mục
    </a>
    <i class="arrow fas fa-angle-right"></i>
    <ul class="sub-menu">
        <li><a href="?cat=add-cat">Thêm mới</a></li>
        <li><a href="?cat=list-cat">Danh sách</a></li>
    </ul>
</li>
                        <li class="nav-link">
    <a href="?info=info">
        <div class="nav-link-icon d-inline-flex">
            <i class="far fa-folder"></i>
        </div>
        Quản lý thông tin
    </a>
</li>
   
<li class="nav-link">
    <a href="?posts=list-posts">
        <div class="nav-link-icon d-inline-flex">
            <i class="far fa-folder"></i>
        </div>
        Quản lý bài viết
    </a>
    <i class="arrow fas fa-angle-right"></i>
    <ul class="sub-menu">
        <li><a href="?posts=add-post">Thêm mới</a></li>
        <li><a href="?posts=list-posts">Danh sách</a></li>
    </ul>
</li>
    <li class="nav-link">
    <a href="?policy=list-policy">
        <div class="nav-link-icon d-inline-flex">
            <i class="far fa-folder"></i>
        </div>
        Quản lý chính sách
    </a>
    <i class="arrow fas fa-angle-right"></i>
    <ul class="sub-menu">
        <li>
            <a href="?policy=add-policy">Thêm mới</a>
        </li>
        <li>
            <a href="?policy=list-policy">Danh sách</a>
        </li>
    </ul>
</li>

                        <li class="nav-link">
                            <a href="index.php?order=success-order-list">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="far fa-folder"></i>
                                </div>
                               Quản lý đơn hàng
                            </a>
                           
                        </li>
                        <li class="nav-link">
                            <a href="?user=list-user">
                                <div class="nav-link-icon d-inline-flex">
                                    <i class="far fa-folder"></i>
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
