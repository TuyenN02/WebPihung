<?php
if (isset($_SESSION['ID_ThanhVien'])) {
    $id_cus = $_SESSION['ID_ThanhVien'];
}
$sql_product = "SELECT * FROM sanpham WHERE ID_SanPham='$_GET[id_product]' ORDER BY ID_SanPham DESC";
$query_product = mysqli_query($mysqli, $sql_product);
$row_product = mysqli_fetch_array($query_product);

$sql_comment = "SELECT * FROM binhluan,thanhvien 
WHERE binhluan.ID_SanPham='$_GET[id_product]' AND binhluan.ID_ThanhVien=thanhvien.ID_ThanhVien";
$query_comment = mysqli_query($mysqli, $sql_comment);

$id_ncc = $row_product['ID_NhaCungCap'];
$sql_ncc = "SELECT * FROM nhacungcap WHERE nhacungcap.ID_NCC = '$id_ncc'";
$query_ncc = mysqli_query($mysqli, $sql_ncc);

// Kiểm tra nếu nhà cung cấp không tồn tại
if (mysqli_num_rows($query_ncc) > 0) {
    $row_ncc = mysqli_fetch_array($query_ncc);
} else {
    $row_ncc = null; // Không có dữ liệu nhà cung cấp
}
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
            <input type="hidden" name="sp_ten" id="sp_ten" value="<?php echo $row_product['TenSanPham']; ?>">
            <input type="hidden" name="sp_gia" id="sp_gia" value="<?php echo $row_product['GiaBan']; ?>">
            <input type="hidden" name="hinhdaidien" id="hinhdaidien"
                value="<?php echo $row_product['Img']; ?>">
            <h1 class="text-center">
                <?php echo $row_product['TenSanPham']; ?>
            </h1>
            <div class="row">
                <div class="col-lg-5" id="pic-3">
                    <div class="card">
                        <img src="./assets/image/product/<?php echo $row_product['Img'];?>"
                             style="display: block; width: 100%; height: 360px; object-fit: cover; object-position: center center;">
                    </div>
                </div>
                <div class="col-lg-3 p-3">
                    <h5 class="text-center">Thông tin</h5>
                    <hr>
                    <h6>Giá hiện tại: <?php echo number_format($row_product['GiaBan'],0,',','.');?> VND/Cây</h6>
                    <p><i>Miễn phí giao hàng</i></p>
                    <?php if (isset($_SESSION['TenDangNhap'])) { ?>
                        <div class="form-group">
    <label for="soluong"><b>Số lượng:</b></label>
    <input type="number" class="form-control" id="soluong" name="soluong" value="1" min="1">
</div>
                        <?php if ($row_product['SoLuong'] > 0) { ?>
                            <div>
                                <input type="submit" class="btn btn-success" name="mua" value="Thêm vào giỏ hàng">
                            </div>
                        <?php } else { ?>
                            <p class="text-danger">Tạm thời hết hàng</p>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="col-lg-4">
                    <div class="card p-3">
                        <h5 class="text-center">Mô tả</h5>
                        <hr>
                        <p>
                            <?php echo $row_product['MoTa']; ?>
                        </p>
                        <h5 class="text-center">Hãng cung cấp</h5>
                        <hr>
                        <?php if ($row_ncc) { ?>
                            <p><?php echo $row_ncc['TenNCC']; ?></p>
                            <p>Địa chỉ: <?php echo $row_ncc['DiaChi']; ?></p>
                            <p>SĐT: <?php echo $row_ncc['SoDienThoai']; ?></p>
                            <p>Email: <?php echo $row_ncc['Email']; ?></p>
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
              action="pages/main/product/comment.php?id_product=<?php echo $row_product['ID_SanPham']; ?>"
              method="POST">
            <?php
            $i = 0;
            while ($row_comment = mysqli_fetch_array($query_comment)) {
                $i++;
                ?>
                <div class="alert alert-success" role="alert">
                    <small><label for="floatingInputValue" class="font-weight-bold">
                            <?php echo $row_comment['HoVaTen']; ?>
                        </label>
                        <label for="floatingInputValue">
                            <?php echo $row_comment['ThoiGianBinhLuan']; ?>
                        </label>
                    </small>
                    </br>
                    <label for="floatingInputValue">
                        <?php echo $row_comment['NoiDung']; ?>
                    </label>
                </div>
                <?php
            }
            ?>
            <?php if (isset($_SESSION['TenDangNhap'])) { ?>
                <div class="form">
                    <textarea class="form-control" placeholder="Hãy bình luận về cây, sản phẩm tại đây"
                              id="floatingTextarea2" name="NoiDung" style="height: 100px"></textarea>
                    </br>
                </div>
                <div class="action">
                    <input type="submit" class="btn btn-success" name="comment" value="Bình luận"
                           style="float:right">
                </div>
            <?php } ?>
        </form>
    </div>
</main>
