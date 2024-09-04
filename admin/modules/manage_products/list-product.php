<?php
include("config/connection.php"); // Kết nối đến cơ sở dữ liệu

// Xử lý phân trang
if (isset($_GET['trang'])) {
    $page = $_GET['trang'];
} else {
    $page = 1;
}
$begin = ($page == '' || $page == 1) ? 0 : ($page * 8) - 8;

// Khởi tạo biến để lưu từ khóa tìm kiếm
$tukhoa = '';

// Truy vấn sản phẩm với thông tin danh mục và nhà cung cấp
$sql_product = "
    SELECT sanpham.*, 
           COALESCE(danhmuc.TenDanhMuc, 'Không có dữ liệu') AS TenDanhMuc, 
           COALESCE(nhacungcap.TenNCC, 'Không có dữ liệu') AS TenNCC
    FROM sanpham
    LEFT JOIN danhmuc ON sanpham.ID_DanhMuc = danhmuc.ID_DanhMuc
    LEFT JOIN nhacungcap ON sanpham.ID_NhaCungCap = nhacungcap.ID_NCC
    ORDER BY sanpham.ID_SanPham DESC
    LIMIT $begin, 8";

if (isset($_POST['tukhoa'])) {
    $tukhoa = trim(mysqli_real_escape_string($mysqli, $_POST['tukhoa']));
    if (!empty($tukhoa)) {
        $sql_product = "
            SELECT sanpham.*, 
                   COALESCE(danhmuc.TenDanhMuc, 'Không có dữ liệu') AS TenDanhMuc, 
                   COALESCE(nhacungcap.TenNCC, 'Không có dữ liệu') AS TenNCC
            FROM sanpham
            LEFT JOIN danhmuc ON sanpham.ID_DanhMuc = danhmuc.ID_DanhMuc
            LEFT JOIN nhacungcap ON sanpham.ID_NhaCungCap = nhacungcap.ID_NCC
            WHERE sanpham.TenSanPham LIKE '%$tukhoa%'
            ORDER BY sanpham.ID_SanPham DESC
            LIMIT $begin, 8";
    }
}

$query_product = mysqli_query($mysqli, $sql_product);
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);
?>
<div id="content" class="container-fluid">
    <?php if ($success_message) { ?>
        <div class="alert alert-success" id="success-message">
            <?php echo htmlspecialchars($success_message, ENT_QUOTES); ?>
        </div>
        <script>
            setTimeout(function() {
                var successMessage = document.getElementById('success-message');
                if (successMessage) {
                    successMessage.style.display = 'none';
                }
            }, 2000); // 2000ms = 2 giây
        </script>
    <?php } ?>
   
   
    <div id="content" class="container-fluid">
    <!-- Thêm liên kết đến trang "Add Product" -->
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
        <button class="btn btn-primary">
        <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?product=add-product">Thêm mới</a>
        </button>
        <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Danh sách sản phẩm</h5>
            <div class="d-flex">
                
                <div class="form-search form-inline ml-2">
                    <form action="" method="POST" class="d-flex">
                        <input type="text" class="form-control form-search" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                        <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary">
                    </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <style>
                    /* Adjust header and cell styles */
                    .table thead th {
                        font-size: 15px; /* Font size for table headers */
                        white-space: nowrap; /* Prevent text from wrapping */
                        overflow: hidden; /* Hide overflow text */
                        text-overflow: ellipsis; /* Add ellipsis for overflow text */
                        text-align: center; /* Center align text in headers */
                        padding: 8px; /* Adjust padding to make columns closer */
                        border: 2px solid #dee2e6; /* Add border for clarity */
                    }
                  #wp-content {
                            margin-left: 250px;
                            flex: 1;
                            padding: 10px;
                            margin-top: 40px;
                        }
                    .table tbody td {
                        font-size: 14px; /* Font size for table cells */
                        overflow: hidden; /* Hide overflow text */
                        text-overflow: ellipsis; /* Add ellipsis for overflow text */
                        padding: 8px; /* Adjust padding to make columns closer */
                        border: 2px solid #dee2e6; /* Add border for clarity */
                    }
                   

                    .alert {
    position: fixed;
    top: 50px;
    right: 950px;
    padding: 15px;
    border-radius: 5px;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.alert-success {
    background-color: #d4edda;
    color: #ff0000;
    border: 3px solid #ff0000;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
/* Điều chỉnh chiều cao của phần tử chính */
#content {
    min-height: 80vh; /* Tăng chiều cao tối thiểu của phần tử chính */
}

/* Tăng khoảng cách trên và dưới của card */
.card {
    margin-top: 20px;
    margin-bottom: 20px;
}

/* Điều chỉnh chiều cao của modal */
.modal-lg {
    max-height: 90vh; /* Tăng chiều cao tối đa của modal */
}

/* Thay đổi padding và margin cho các phần tử khác */
.tableInfo {
    padding: 20px; /* Thêm padding cho phần nội dung bảng */
}

.btn-container {
    margin-top: 10px; /* Thêm khoảng cách trên cho nút */
}
                    /* Style for 'Xem chi tiết' button */
                    .btn-detail {
                        padding: 0; /* Remove padding */
                        font-size: 12px; /* Smaller font size */
                        color: #007bff; /* Blue color for text */
                        border: none; /* Remove border */
                        background: none; /* No background */
                        cursor: pointer; /* Pointer cursor on hover */
                        text-decoration: underline; /* Underline text */
                    }
                </style>

                <div class="tableInfo">
                    
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">STT</th>
                                    <th scope="col">Tên sản phẩm</th>
                                    <th scope="col">Danh mục</th>
                                    <th scope="col">Nhà cung cấp</th>
                                    <th scope="col">Số lượng</th>
                                    <th scope="col">Hình ảnh</th>
                                    <th scope="col">Giá</th>
                                    <th scope="col">Xem chi tiết</th>
                                    <th scope="col">Sửa/Xóa</th>
                                </tr>
                            </thead>
                      
                            <tbody>
                                <?php
                                $i = $begin + 1; // Bắt đầu từ giá trị $begin + 1 cho STT
                                while ($row = mysqli_fetch_array($query_product)) {
                                    $description = htmlspecialchars($row['MoTa']);
                                    $short_description = (strlen($description) > 20) ? substr($description, 0, 20) . '...' : $description;
                                ?>
                                                        <tr>
                            <td><?php echo $i++; ?></td>
                        
                            <td><?php echo htmlspecialchars($row['TenSanPham']); ?></td>
                        
                            <td><?php echo htmlspecialchars($row['TenDanhMuc']); ?></td>
                            <td><?php echo htmlspecialchars($row['TenNCC']); ?></td>
                            <td><?php echo $row['SoLuong']; ?></td>
                            <td><img style="width: 180px; height: 180px; object-fit: cover; object-position: center center;" src="../assets/image/product/<?php echo htmlspecialchars($row['Img']); ?>"/></td>
                            <td><?php echo number_format($row['GiaBan'], 0, ',', '.'); ?></td>
                            <td>
                            <a href="javascript:void(0);" class="btn-detail" onclick="showProductDetail(<?php echo $row['ID_SanPham']; ?>)">Xem thêm</a>
                                    </td>
                                        <td>
                    <div class="btn-container">
                        <a href="javascript:void(0);" class="btn btn-danger btn-custom" onclick="confirmDelete(<?php echo $row['ID_SanPham']; ?>)">Xóa</a>
                        <a href="?product=suaSanPham&id=<?php echo $row['ID_SanPham']; ?>" class="btn btn-warning btn-custom">Sửa</a>
                    </div>
                </td>
            </tr>
                 <?php } ?>
                 </tbody>
                        </table>
                        <?php if (mysqli_num_rows($query_product) == 0): ?>
                        <p class="text-center">Không tìm thấy sản phẩm nào.</p>
                    
                    <?php endif; ?>
                </div>
                <!-- Phân trang -->
                <?php
                if (empty($tukhoa)) {
                    $sql_trang = mysqli_query($mysqli, "SELECT * FROM sanpham");
                    $row_count = mysqli_num_rows($sql_trang);
                    $trang = ceil($row_count / 8);
                ?>
                    <ul class="d-flex justify-content-center list-unstyled">
                        <?php
                        for ($i = 1; $i <= $trang; $i++) {
                        ?>
                            <li class="p-2 m-1 bg-white" <?php if ($i == $page) { echo 'style="background: #ccc !important;"'; } ?>>
                                <a class="text-dark" href="index.php?product=list-product&trang=<?php echo $i ?>"><?php echo $i ?></a>
                            </li>
                        <?php
                        }
                    }
                    ?>
                    </ul>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">Chi tiết sản phẩm</h5>
                <!-- Nút đóng (X) -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="productDetailContent">
                    <!-- Nội dung chi tiết sản phẩm sẽ được tải vào đây -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
            window.location.href = 'modules/manage_products/delete-product.php?id_pro=' + id;
        }
    }

    function showFullDescription(description) {
        alert(description);
    }
</script>
<script>
function showProductDetail(id) {
    // Gửi yêu cầu AJAX để lấy thông tin sản phẩm
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'modules/manage_products/product-detail.php?id=' + id, true);
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 400) {
            // Nhận dữ liệu từ server
            document.getElementById('productDetailContent').innerHTML = xhr.responseText;
            // Hiển thị modal
            var myModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
            myModal.show();
        } else {
            console.error('Lỗi khi tải dữ liệu sản phẩm.');
        }
    };
    xhr.onerror = function() {
        console.error('Lỗi mạng khi gửi yêu cầu.');
    };
    xhr.send();
}
</script>
<!-- Bootstrap CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" />
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
