<?php
// Bắt đầu session nếu chưa được bắt đầu
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối cơ sở dữ liệu
include(dirname(__FILE__) . '/../../../admin/config/connection.php');

// Kiểm tra biến $_SESSION
if (!isset($_SESSION['ID_ThanhVien']) || !isset($_SESSION['ID_GioHang'])) {
    die('Thông tin phiên không hợp lệ. Vui lòng thử lại.');
}

// Kiểm tra biến $mysqli
if (!isset($mysqli) || !$mysqli) {
    die('Kết nối cơ sở dữ liệu không hợp lệ.');
}

// Lấy thông tin giỏ hàng
$id_cart = $_SESSION['ID_GioHang'];
$sql_cart = "SELECT * FROM giohang WHERE ID_GioHang = $id_cart";
$query_cart = mysqli_query($mysqli, $sql_cart);
$row = mysqli_fetch_array($query_cart);

// Lấy chi tiết giỏ hàng
$sql_cart_detail = "SELECT chitietgiohang.ID_SanPham, chitietgiohang.SoLuong,
    sanpham.TenSanPham, sanpham.GiaBan
    FROM giohang
    INNER JOIN chitietgiohang ON giohang.ID_GioHang = chitietgiohang.ID_GioHang
    INNER JOIN sanpham ON chitietgiohang.ID_SanPham = sanpham.ID_SanPham
    WHERE giohang.ID_GioHang = $id_cart";
$query_cart_detail = mysqli_query($mysqli, $sql_cart_detail);

// Lưu thông tin đơn hàng vào session nếu chưa có
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['NguoiNhan'] = $_POST['NguoiNhan'] ?? '';
    $_SESSION['DiaChi'] = $_POST['DiaChi'] ?? '';
    $_SESSION['SoDienThoai'] = $_POST['SoDienThoai'] ?? '';
    $_SESSION['GhiChu'] = $_POST['GhiChu'] ?? '';
}

$tongtien_vnd = isset($_SESSION['allMoney']) ? $_SESSION['allMoney'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../../../path/to/your/scripts.js"></script> <!-- Đảm bảo đường dẫn chính xác -->
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-8 mt-5">
            <table class="table-bordered w-100" cellpadding="5px">           
                <tr class="text-center">
                    <td colspan="4"><h4>ĐƠN HÀNG</h4></td>
                </tr>
                <tr>
                    <td colspan="4">Người nhận: <?php echo htmlspecialchars($_SESSION['NguoiNhan'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td colspan="2">Địa chỉ: <?php echo htmlspecialchars($_SESSION['DiaChi'] ?? ''); ?></td>
                    <td colspan="2">Số điện thoại: <?php echo htmlspecialchars($_SESSION['SoDienThoai'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td colspan="4">Ghi chú: <?php echo htmlspecialchars($_SESSION['GhiChu'] ?? ''); ?></td>
                </tr>
                <tr class="text-center">
                    <th scope="col">STT</th>
                    <th scope="col">Tên sản phẩm</th>
                    <th scope="col">Số lượng</th>
                    <th scope="col">Giá mua</th>
                </tr>
                <?php 
                $i = 0; 
                $allMoney = 0;
                while ($row_detail = mysqli_fetch_assoc($query_cart_detail)) {
                    $i++;
                ?>
                    <tr class="text-center">
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($row_detail['TenSanPham']) ?></td>
                        <td><?= htmlspecialchars($row_detail['SoLuong']) ?></td>
                        <td><?= number_format($row_detail['GiaBan']) ?> VND</td>
                    </tr>
                <?php 
                    $Money = (int)$row_detail['SoLuong'] * (int)$row_detail['GiaBan'];
                    $allMoney += $Money;
                } 
                ?>
                <tr>
                    <th colspan="4">Tổng tiền: <?= number_format($allMoney, 0, ',', '.') ?> VND</th>
                </tr>
            </table>
            <a class="mt-5 btn btn-danger" href="index.php?navigate=cart">Quay lại giỏ hàng</a>
        </div>
        <div class="col-lg-4 mt-5">
            <div>
                <form method="POST" action="pages/main/order/xulythanhtoan.php">
                    <p class="mt-2 text-center">HÌNH THỨC THANH TOÁN</p>
                    <input class="d-block btn btn-success mt-3 w-100" type="submit" name="cod" value="Thanh toán khi nhận hàng">
                    <input class="d-block btn btn-primary mt-3 w-100" type="submit" name="vnpay" value="Thanh toán qua VNPAY">
                </form>
                <form method="POST" target="_blank" enctype="application/x-www-form-urlencoded" action="pages/main/order/xulythanhtoanmomo.php">
                    <input type="hidden" name="tongtien_vnd" value="<?php echo $tongtien_vnd ?>">
                    <input class="btn text-light mt-3 w-100" style="background-color: #ae2170; border-color: #ae2170;" type="submit" value="Thanh toán qua MOMO QRCode">
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
