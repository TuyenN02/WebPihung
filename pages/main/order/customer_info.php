<?php


$id_cus = $_SESSION['ID_ThanhVien'];
$sql_cart = "SELECT sanpham.ID_SanPham, chitietgiohang.SoLuong, sanpham.SoLuong AS Stock, sanpham.TenSanPham
FROM chitietgiohang
INNER JOIN sanpham ON chitietgiohang.ID_SanPham = sanpham.ID_SanPham
WHERE chitietgiohang.ID_GioHang = (SELECT ID_GioHang FROM giohang WHERE ID_ThanhVien = $id_cus)";
$query_cart = mysqli_query($mysqli, $sql_cart);

$errors = [];
while ($row = mysqli_fetch_array($query_cart)) {
    if ($row['SoLuong'] > $row['Stock']) {
        $errors[] = "Sản phẩm '{$row['TenSanPham']}' có số lượng yêu cầu vượt quá số lượng hiện có trong kho ({$row['Stock']} sản phẩm).";
    }
}


$ID_ThanhVien = $_SESSION['ID_ThanhVien'];
$sql_ThanhVien = "SELECT * FROM thanhvien WHERE ID_ThanhVien = $ID_ThanhVien";
$query_ThanhVien = mysqli_query($mysqli, $sql_ThanhVien);
$row = mysqli_fetch_array($query_ThanhVien);
$NguoiNhan = trim($row['HoVaTen']);
$DiaChi = trim($row['DiaChi']);
$GiaTien = $_SESSION['allMoney'];
$SoDienThoai = trim($row['SoDienThoai']);
?>
<div class="container mt-60 bg-white">
    <form id="orderForm" action="index.php?navigate=confirm_order" method="POST" onsubmit="return validateForm()">
        <p class="pt-3 text-center" style="font-size: 24px; font-weight: bold;">NHẬP THÔNG TIN NHẬN HÀNG</p>
        <div class="mt-2">
            <label>Người nhận hàng: </label>
            <input required class="form-control" type="text" name="NguoiNhan" id="NguoiNhan" value="<?php echo $NguoiNhan; ?>">
            <small id="errorNguoiNhan" class="text-danger"></small>
        </div>
        <div class="mt-2">
            <label>Địa chỉ: </label>
            <input required class="form-control" type="text" name="DiaChi" id="DiaChi" value="<?php echo $DiaChi; ?>">
            <small id="errorDiaChi" class="text-danger"></small>
        </div>
        <div class="mt-2">
            <label>Số điện thoại:</label>
            <input required class="form-control" type="text" name="SoDienThoai" id="SoDienThoai" value="<?php echo $SoDienThoai; ?>">
            <small id="errorSoDienThoai" class="text-danger"></small>
        </div>
        <div class="mt-2">
            <label>Ghi chú:</label>
            <input class="form-control" type="text" name="GhiChu">
        </div>
        <div class="d-flex justify-content-between mt-4">
            <a class="btn btn-danger w-45" href="index.php?navigate=cart">Quay lại giỏ hàng</a>
            <button class="btn btn-success w-45" type="submit">Xác nhận</button>
        </div>
    </form>
</div>

<script>
    function validateForm() {
        let isValid = true;

        const name = document.getElementById("NguoiNhan").value.trim();
        const address = document.getElementById("DiaChi").value.trim();
        const phone = document.getElementById("SoDienThoai").value.trim();

        const namePattern = /^[a-zA-Z\s]{2,50}$/;
        const phonePattern = /^0\d{9}$/;

        // Xóa thông báo lỗi cũ
        document.getElementById("errorNguoiNhan").innerText = "";
        document.getElementById("errorDiaChi").innerText = "";
        document.getElementById("errorSoDienThoai").innerText = "";

        // Kiểm tra tên người nhận
        if (!name) {
            document.getElementById("errorNguoiNhan").innerText = "Tên người nhận không được để trống.";
            isValid = false;
        }

        // Kiểm tra địa chỉ
        if (!address) {
            document.getElementById("errorDiaChi").innerText = "Địa chỉ không được để trống.";
            isValid = false;
        }

        // Kiểm tra số điện thoại
        if (!phonePattern.test(phone)) {
            document.getElementById("errorSoDienThoai").innerText = "Số điện thoại phải có 10 số và bắt đầu bằng số 0.";
            isValid = false;
        }

        return isValid;
    }
</script>
