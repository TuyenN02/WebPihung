<?php

$id_cus = $_SESSION['ID_ThanhVien'];
$sql_cart = "SELECT sanpham.ID_SanPham, chitietgiohang.SoLuong, sanpham.Img, sanpham.TenSanPham, sanpham.GiaBan, sanpham.SoLuong AS StockQuantity
FROM giohang
INNER JOIN chitietgiohang ON giohang.id_GioHang = chitietgiohang.id_GioHang
INNER JOIN sanpham ON chitietgiohang.ID_SanPham = sanpham.id_sanpham
WHERE giohang.ID_ThanhVien = $id_cus";
$query_cart = mysqli_query($mysqli, $sql_cart);
?>
<div class="container min-height-100">
    <h1 class="text-center">Giỏ hàng</h1>
    <div>
        <?php
        if (isset($_SESSION['update_cart_errors']) && !empty($_SESSION['update_cart_errors'])) {
            echo '<div class="alert alert-danger">Vui lòng kiểm tra lỗi và nhập lại:</div>';
        }
        ?>
        <?php
        if (isset($_SESSION['ID_ThanhVien'])) {
        ?>
        <form method="POST" action="pages/main/cart/update_cart.php">
            <?php
            if (mysqli_num_rows($query_cart) > 0) {
                $i = 0;
                $allMoney = 0.0;
                $allAmount = 0.0;
                ?>
            <table class="bg-white table-bordered w-100" cellpadding="5px">           
                <thead>
                <tr class="text-center">
                    <th scope="col">STT</th>
                    <th scope="col">Tên sản phẩm</th>
                    <th scope="col">Hình ảnh</th>
                    <th scope="col">Số lượng</th>
                    <th scope="col">Giá bán</th>
                    <th scope="col">Tùy chọn</th>
                </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_array($query_cart)) {
                        $i++;
                        $product_id = $row['ID_SanPham'];
                        $current_quantity = isset($_SESSION['soluong'][$product_id]) ? $_SESSION['soluong'][$product_id] : $row['SoLuong'];
                    ?>
                    <tr class="text-center">
                        <td><?= $i ?></td>
                        <td><?= $row['TenSanPham'] ?></td>
                        <td><img class="product-img" style="width: 260px" src="./assets/image/product/<?= $row['Img'] ?>"></td>
                        <td>
                            <input type="number" name="soluong[<?= $product_id ?>]" value="<?= $current_quantity ?>" min="1" class="text-center" style="width: 60px;">
                            <?php
                            // Hiển thị lỗi nếu có
                            if (isset($_SESSION['update_cart_errors'][$product_id])): ?>
                                <div class="text-danger"><?= $_SESSION['update_cart_errors'][$product_id] ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($row['GiaBan']) ?> VND</td>
                        <td>
                            <a class="mr-2 ml-2" href="pages/main/cart/delete.php?id_delete=<?= $product_id ?>">Xóa</a>
                        </td>
                    </tr>
                    <?php
                    $Money = (float)$current_quantity * (float)$row['GiaBan'];
                    $allMoney += $Money;
                    $allAmount += (float)$current_quantity;
                    }
                    ?>
                </tbody>
                <tr>
                    <th colspan="3">Tổng tiền: <?= number_format($allMoney,0,',','.') ?> VND</th>
                    <th colspan="2">Tổng số lượng: <?= $allAmount ?></th>
                    <td>
                        <a class="btn btn-danger btn-small" href="pages/main/cart/delete.php?deleteAll">Xóa hết</a>
                    </td>              
                </tr>
                <tr>
                    <td class="w-100" colspan="6">
                        <input type="submit" class="btn btn-warning btn-small w-100" name='update_cart' value="Cập nhật giỏ hàng">
                    </td>
                </tr>
                <tr>
                    <td class="w-100" colspan="6">
                        <input type="submit" class="btn btn-success btn-small w-100" name='place_order' value="Đặt hàng">
                    </td>
                </tr>
            </table>
            <?php
                $_SESSION['allMoney'] = $allMoney;
                $_SESSION['allAmount'] = $allAmount;
            } else {
            ?>
            <h4 class="text-center">Không có sản phẩm trong giỏ hàng</h4>
            <?php
            }                       
            ?>
        </form>
        <a class="btn btn-primary btn-small w-100 mt-3" href="index.php?navigate=showProducts">Tiếp tục mua sắm</a>
        <?php
        } else {
        ?>
        <h4 class="text-center">Vui lòng đăng nhập để xem giỏ hàng!</h4>
        <?php
        }
        ?>
    </div>  
</div>

<style>
    .btn-small {
        padding: 5px 10px;
        font-size: 14px;
        width: auto;
        margin: 5px 0;
    }
    .btn-small.w-100 {
        width: 20%;
    }
    .product-img {
        width: 100px;
        height: auto;
    }
</style>
