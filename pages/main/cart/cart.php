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
        <?php if (isset($_SESSION['ID_ThanhVien'])): ?>
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
                    <?php 
                    while($row = mysqli_fetch_array($query_cart)) {
                        $i++;
                        $product_id = $row['ID_SanPham'];
                        $current_quantity = $row['SoLuong'];
                        $stock_quantity = $row['StockQuantity'];
                        $product_total = $current_quantity * $row['GiaBan'];
                        $allMoney += $product_total;
                        $allAmount += $current_quantity;
                    ?>
                    <tr class="text-center">
                        <td><?= $i ?></td>
                        <td><?= $row['TenSanPham'] ?></td>
                        <td><img class="product-img" style="width: 100px" src="./assets/image/product/<?= $row['Img'] ?>"></td>
                        <td>
                            <input type="number" name="soluong[<?= $product_id ?>]" value="<?= $current_quantity ?>" min="1" class="text-center quantity-input" data-price="<?= $row['GiaBan'] ?>" data-id="<?= $product_id ?>" style="width: 60px;" onchange="checkQuantity(this, <?= $stock_quantity ?>, <?= $product_id ?>)" oninput="updateProductTotal(<?= $product_id ?>)">
                        </td>
                        <td id="product-total-<?= $product_id ?>"><?= number_format($product_total) ?> VND</td>
                        <td>
                            <a class="mr-2 ml-2" href="pages/main/cart/delete.php?id_delete=<?= $product_id ?>">Xóa</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tr>
                    <th colspan="3">Tổng tiền: <span id="totalMoney"><?= number_format($allMoney, 0, ',', '.') ?> VND</span></th>
                    <th colspan="2">Tổng số lượng: <span id="totalQuantity"><?= $allAmount ?></span></th>
                    <td>
                        <a class="btn btn-danger btn-small" href="pages/main/cart/delete.php?deleteAll">Xóa hết</a>
                    </td>              
                </tr>
                <tr>
                    <td class="w-100" colspan="6">
                        <input type="submit" class="btn btn-success btn-small w-100" name='update_cart' value="Đặt hàng">
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
        <?php else: ?>
        <h4 class="text-center">Vui lòng đăng nhập để xem giỏ hàng!</h4>
        <?php endif; ?>
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

<script>
function checkQuantity(input, maxQuantity, id) {
    const currentQuantity = parseInt(input.value, 10);
    if (currentQuantity > maxQuantity) {
        alert('Số lượng bạn nhập vượt quá số lượng tối đa.');
        input.value = maxQuantity; // Reset to the maximum allowed quantity after the alert
    }

    updateTotals(); // Update totals after checking quantity
    updateProductTotal(id);
    
}

function updateProductTotal(productId) {
    const input = document.querySelector(`input[data-id='${productId}']`);
    const price = parseFloat(input.dataset.price);
    const quantity = parseInt(input.value, 10);

    // Update the product's total price
    const productTotal = price * quantity;
    document.getElementById(`product-total-${productId}`).innerText = formatNumber(productTotal) + ' VND';

    // Update the overall totals
    updateTotals();
}

function updateTotals() {
    let totalMoney = 0;
    let totalQuantity = 0;

    // Get all quantity inputs
    const inputs = document.querySelectorAll('.quantity-input');

    inputs.forEach(input => {
        const quantity = parseInt(input.value, 10);
        const price = parseFloat(input.dataset.price);
        totalQuantity += quantity;
        totalMoney += quantity * price;
    });

    // Update the totals
    document.getElementById('totalMoney').innerText = formatNumber(totalMoney) + ' VND';
    document.getElementById('totalQuantity').innerText = totalQuantity;
}

function formatNumber(num) {
    return num.toLocaleString('en-US', { minimumFractionDigits: 0 });
}

// Update totals on page load
document.addEventListener('DOMContentLoaded', updateTotals);
</script>
