<?php
// session_start(); // Đảm bảo session đã được khởi động

if (isset($_SESSION['ID_ThanhVien'])) {
    $id_cus = $_SESSION['ID_ThanhVien'];
} else {
    $id_cus = null;
}
// Fetch additional images
$sql_images = "SELECT Anh FROM hinhanh_sanpham WHERE ID_SanPham = '".mysqli_real_escape_string($mysqli, $_GET['id_product'])."'";
$query_images = mysqli_query($mysqli, $sql_images);
// Fetch product details
$sql_product = "SELECT * FROM sanpham WHERE ID_SanPham = '".mysqli_real_escape_string($mysqli, $_GET['id_product'])."'";
$query_product = mysqli_query($mysqli, $sql_product);
$row_product = mysqli_fetch_array($query_product);

// Fetch comments for the product
$sql_comment = "
    SELECT binhluan.*, thanhvien.HoVaTen 
    FROM binhluan 
    JOIN thanhvien ON binhluan.ID_ThanhVien = thanhvien.ID_ThanhVien 
    WHERE binhluan.ID_SanPham = '".mysqli_real_escape_string($mysqli, $_GET['id_product'])."'
";
$query_comment = mysqli_query($mysqli, $sql_comment);

// Fetch supplier information
$id_ncc = $row_product['ID_NhaCungCap'];
$sql_ncc = "SELECT * FROM nhacungcap WHERE ID_NCC = '".mysqli_real_escape_string($mysqli, $id_ncc)."'";
$query_ncc = mysqli_query($mysqli, $sql_ncc);

// Fetch product quantity in the cart
$cart_quantity = 0;
if ($id_cus) {
    $sql_cart = "
        SELECT chitietgiohang.SoLuong 
        FROM giohang 
        JOIN chitietgiohang ON giohang.ID_GioHang = chitietgiohang.ID_GioHang
        WHERE giohang.ID_ThanhVien = '".mysqli_real_escape_string($mysqli, $id_cus)."'
        AND chitietgiohang.ID_SanPham = '".mysqli_real_escape_string($mysqli, $_GET['id_product'])."'
    ";
    $query_carts = mysqli_query($mysqli, $sql_cart);
    if ($row_cart = mysqli_fetch_array($query_carts)) {
        $cart_quantity = (int)$row_cart['SoLuong'];
    }
}

$show_quantity_input = $row_product['SoLuong'] > $cart_quantity;

// Check if the supplier exists
$row_ncc = mysqli_num_rows($query_ncc) > 0 ? mysqli_fetch_array($query_ncc) : null;
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php if (isset($_SESSION['error'])) { ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php } ?>

<?php if (isset($_SESSION['success'])) { ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php } ?>

<a href="javascript:history.back()" class="back-button">Quay lại</a>

<main role="main">
    <div class="container">
    <form name="frmsanphamchitiet" id="frmsanphamchitiet" method="post"
      action="pages/main/cart/add.php?id=<?php echo $row_product['ID_SanPham']; ?>">

    <input type="hidden" name="sp_ten" id="sp_ten" value="<?php echo htmlspecialchars($row_product['TenSanPham']); ?>">
    <input type="hidden" name="sp_gia" id="sp_gia" value="<?php echo htmlspecialchars($row_product['GiaBan']); ?>">
    <input type="hidden" name="hinhdaidien" id="hinhdaidien" value="<?php echo htmlspecialchars($row_product['Img']); ?>">
    <input type="hidden" name="action_type" id="action_type" value="">

    <h1 class="text-center"><?php echo htmlspecialchars($row_product['TenSanPham']); ?></h1>

    <div class="row">
        <div class="col-lg-5" id="pic-3">
            <div class="card">
                <img src="./assets/image/product/<?php echo htmlspecialchars($row_product['Img']); ?>"
                     style="display: block; width: 100%; height: 360px; object-fit: cover; object-position: center center;">
            </div>
            <div class="additional-images mt-3">
        <div class="row">
            <?php while ($row_image = mysqli_fetch_array($query_images)) { ?>
                <div class="col-4 mb-3">
                    <a href="./assets/image/product/<?php echo htmlspecialchars($row_image['Anh']); ?>" data-lightbox="product-gallery" data-title="<?php echo htmlspecialchars($row_product['TenSanPham']); ?>">
                        <img src="./assets/image/product/<?php echo htmlspecialchars($row_image['Anh']); ?>"
                             style="width: 100%; height: 120px; object-fit: cover; object-position: center center;">
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
        </div>

        <div class="col-lg-3 p-3">
            <h5 class="text-center">Thông tin</h5>
            <hr>
            <h6>Giá hiện tại: <?php echo number_format($row_product['GiaBan'], 0, ',', '.'); ?> VND</h6>
            <h6>Số lượng sản phẩm: <?php echo number_format($row_product['SoLuong']); ?> </h6>
            <p><i>Miễn phí giao hàng</i></p>
            <?php if ($row_product['SoLuong'] <= 0) { ?>
                <p class="text-danger">Sản phẩm đã hết hàng.</p>
            <?php } else { ?>
                <?php if ($show_quantity_input) { ?>
                    <div class="form-group">
                        <label for="soluong"><b>Số lượng:</b></label>
                        <input type="number" class="form-control" id="soluong" name="soluong" value="1" 
                            min="1" max="<?php echo $row_product['SoLuong']; ?>" onblur="checkQuantity(this)">
                    </div>
                    <div class = " d-flex">
                        <button type="submit" class="btn btn-success mr-2" name="mua" value="add_to_cart" onclick="setAction('add_to_cart')">Thêm vào giỏ hàng</button>
                        <button type="submit" class="btn btn-success" name="mua" value="buy_now" onclick="setAction('buy_now')">Mua Ngay</button>
                    </div>
                <?php } else { ?>
                    <p class="text-danger">Sản phẩm đã có trong giỏ hàng với số lượng tối đa.</p>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="col-lg-4">
            <div class="card p-3">
                <h5 class="text-center">Mô tả</h5>
                <hr>
                <p><?php echo htmlspecialchars($row_product['MoTa']); ?></p>
                
                <h5 class="text-center">Hãng cung cấp</h5>
                <hr>
                
                <?php if ($row_ncc) { ?>
                    <p><?php echo htmlspecialchars($row_ncc['TenNCC']); ?></p>
                    <p>Địa chỉ: <?php echo htmlspecialchars($row_ncc['DiaChi']); ?></p>
                    <p>SĐT: <?php echo htmlspecialchars($row_ncc['SoDienThoai']); ?></p>
                    <p>Email: <?php echo htmlspecialchars($row_ncc['Email']); ?></p>
                <?php } else { ?>
                    <p class="text-warning">Không có dữ liệu nhà cung cấp</p>
                <?php } ?>
            </div>
        </div>
    </div>
</form>
    </div>

    <div class="container mt-60">
        <h3>Bình luận về cây, sản phẩm</h3>
        <form class="form-floating"
              action="pages/main/product/comment.php?id_product=<?php echo $row_product['ID_SanPham']; ?>" method="POST">
            <?php
            while ($row_comment = mysqli_fetch_array($query_comment)) {
                ?>
                <div class="alert alert-success" role="alert">
                    <small>
                        <label for="floatingInputValue" class="font-weight-bold"><?php echo htmlspecialchars($row_comment['HoVaTen']); ?></label>
                        <label for="floatingInputValue"><?php echo htmlspecialchars($row_comment['ThoiGianBinhLuan']); ?></label>
                    </small>
                    <br>
                    <label for="floatingInputValue"><?php echo htmlspecialchars($row_comment['NoiDung']); ?></label>
                </div>
            <?php } ?>
            
            <?php if (isset($_SESSION['TenDangNhap'])) { ?>
                <div class="form">
                    <textarea class="form-control" placeholder="Hãy bình luận về cây, sản phẩm tại đây"
                              id="floatingTextarea2" name="NoiDung" style="height: 100px"></textarea>
                    <br>
                    
                </div>
                <div class="action">
                    <input type="submit" class="btn btn-success" name="comment" value="Bình luận" style="float:right">
                </div>
            <?php } ?>
        </form>
    </div>
</main>
<!-- Lightbox2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />

<!-- Lightbox2 JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    function setAction(action) {
    document.getElementById('action_type').value = action;
    }
    function checkQuantity(input) {
        const maxQuantity = parseInt(input.max, 10);
        const currentQuantity = parseInt(input.value, 10);
        const currentCartQuantity = <?php echo $cart_quantity; ?>;

        if (currentQuantity + currentCartQuantity > maxQuantity) {
            setTimeout(() => {
                alert('Bạn đã nhập quá số lượng cho phép trong giỏ hàng.');
                input.value = maxQuantity - currentCartQuantity; // Điều chỉnh giá trị input cho phù hợp
            }, 0);
        } else if (currentQuantity > maxQuantity) {
            setTimeout(() => {
                alert('Bạn đã nhập quá số lượng cho phép.');
                input.value = maxQuantity; // Reset lại giá trị input về số lượng tối đa
            }, 0);
        } 
    }
</script>
