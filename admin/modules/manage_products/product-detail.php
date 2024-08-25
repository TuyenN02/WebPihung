<?php
include("../../config/connection.php"); // Kết nối đến cơ sở dữ liệu


if (isset($_GET['id'])) {
    $id_sanpham = intval($_GET['id']); // Lấy ID sản phẩm từ URL và chuyển thành số nguyên để bảo mật

    // Truy vấn thông tin chi tiết sản phẩm
    $sql_product_detail = "
        SELECT sanpham.*, 
               COALESCE(danhmuc.TenDanhMuc, 'Không có dữ liệu') AS TenDanhMuc, 
               COALESCE(nhacungcap.TenNCC, 'Không có dữ liệu') AS TenNCC
        FROM sanpham
        LEFT JOIN danhmuc ON sanpham.ID_DanhMuc = danhmuc.ID_DanhMuc
        LEFT JOIN nhacungcap ON sanpham.ID_NhaCungCap = nhacungcap.ID_NCC
        WHERE sanpham.ID_SanPham = $id_sanpham
        LIMIT 1";
    
    $query_product_detail = mysqli_query($mysqli, $sql_product_detail);

    if ($row = mysqli_fetch_array($query_product_detail)) {
        // Hiển thị thông tin chi tiết sản phẩm
        echo '<div class="container-fluid">';
        echo '<div class="row">';
        
        // Phần hình ảnh chính
        echo '<div class="col-md-6">';
        echo '<img style="width: 100%; height: auto; object-fit: cover;" src="../assets/image/product/' . htmlspecialchars($row['Img']) . '" alt="' . htmlspecialchars($row['TenSanPham']) . '"/>';
        echo '</div>';
        
        // Phần thông tin sản phẩm
        echo '<div class="col-md-6">';
        echo '<h3>ID: ' . htmlspecialchars($row['ID_SanPham']) . '</h3>';
        echo '<h3>' . htmlspecialchars($row['TenSanPham']) . '</h3>';
        echo '<p><strong>Danh mục:</strong> ' . htmlspecialchars($row['TenDanhMuc']) . '</p>';
        echo '<p><strong>Nhà cung cấp:</strong> ' . htmlspecialchars($row['TenNCC']) . '</p>';
        echo '<p><strong>Số lượng:</strong> ' . intval($row['SoLuong']) . '</p>';
        echo '<p><strong>Giá:</strong> ' . number_format($row['GiaBan'], 0, ',', '.') . ' VND</p>';
        echo '<p><strong>Mô tả:</strong> ' . htmlspecialchars($row['MoTa']) . '</p>';
        echo '</div>';
        
        echo '</div>'; // Đóng row

        // Truy vấn ảnh phụ
        $sql_sub_images = "
            SELECT Anh 
            FROM hinhanh_sanpham 
            WHERE ID_SanPham = $id_sanpham";
        
        $query_sub_images = mysqli_query($mysqli, $sql_sub_images);

        if (mysqli_num_rows($query_sub_images) > 0) {
            echo '<hr>'; // Kẻ một đường ngang giữa ảnh chính và ảnh phụ
            echo '<h4>Ảnh phụ:</h4>';
            echo '<div class="row">';

            while ($sub_img_row = mysqli_fetch_array($query_sub_images)) {
                echo '<div class="col-md-3">';
                echo '<img class="sub-image" src="../assets/image/product/' . htmlspecialchars($sub_img_row['Anh']) . '" alt="Ảnh phụ"/>';
                echo '</div>';
            }

            echo '</div>'; // Đóng row
        }

        echo '</div>'; // Đóng container
    } else {
        echo '<p class="text-center">Sản phẩm không tồn tại.</p>';
    }
} else {
    echo '<p class="text-center">ID sản phẩm không hợp lệ.</p>';
}
?>
<style>
.container-fluid {
    margin-top: 20px;
}

img {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
    background: #f9f9f9;
}

hr {
    margin: 20px 0;
}

.sub-image {
    width: 100%;
    height: 200px; /* Hoặc kích thước mong muốn */
    object-fit: cover;
    margin-bottom: 10px;
}
</style>