<?php


$error_message = '';

if (isset($_GET['vnp_Amount'])) {
    $Amount = $_GET['vnp_Amount'];
    $BankCode = isset($_GET['vnp_BankCode']) ? $_GET['vnp_BankCode'] : '';
    $BankTranNo = isset($_GET['vnp_BankTranNo']) ? $_GET['vnp_BankTranNo'] : '';
    $CardType = isset($_GET['vnp_CardType']) ? $_GET['vnp_CardType'] : '';
    $OrderInfo = isset($_GET['vnp_OrderInfo']) ? $_GET['vnp_OrderInfo'] : '';
    $PayDate = isset($_GET['vnp_PayDate']) ? $_GET['vnp_PayDate'] : '';
    $TmnCode = isset($_GET['vnp_TmnCode']) ? $_GET['vnp_TmnCode'] : '';
    $TransactionNo = isset($_GET['vnp_TransactionNo']) ? $_GET['vnp_TransactionNo'] : '';
    $CodeOrder = isset($_GET['CodeOrder']) ? $_GET['CodeOrder'] : '';

    // Kiểm tra số tiền và mã giao dịch
    if (empty($Amount) || empty($BankTranNo)) {
        $error_message = 'Thanh toán không thành công.';
    } else {
        $insert_vnpay = "INSERT INTO vnpay (Amount, BankCode, BankTranNo, CardType, OrderInfo, PayDate, TmnCode, TransactionNo, CodeOrder)
        VALUES ('$Amount', '$BankCode', '$BankTranNo', '$CardType', '$OrderInfo', '$PayDate', '$TmnCode', '$TransactionNo', '$CodeOrder')";
        $query_insert = mysqli_query($mysqli, $insert_vnpay);

        if (!$query_insert) {
            $error_message = 'Thanh toán không thành công.';
        }
    }

} else if (isset($_GET['partnerCode'])) {
    $CodeOrder = rand(1, 10000);
    $partnerCode = isset($_GET['partnerCode']) ? $_GET['partnerCode'] : '';
    $orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';
    $amount = isset($_GET['amount']) ? $_GET['amount'] : '';
    $orderInfo = isset($_GET['orderInfo']) ? $_GET['orderInfo'] : '';
    $orderType = isset($_GET['orderType']) ? $_GET['orderType'] : '';
    $transId = isset($_GET['transId']) ? $_GET['transId'] : '';
    $payType = isset($_GET['payType']) ? $_GET['payType'] : '';

    $insert_momo = "INSERT INTO momo (PartnerCode, OrderId, Amount, OrderInfo, OrderType, TransId, PayType, CodeOrder)
    VALUES ('$partnerCode', '$orderId', '$amount', '$orderInfo', '$orderType', '$transId', '$payType', '$CodeOrder')";
    $momo_query = mysqli_query($mysqli, $insert_momo);

    if ($momo_query) {
        $ID_ThanhVien = $_GET['ID_ThanhVien'];
        $ID_GioHang = $_GET['ID_GioHang'];
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $ThoiGianLap = date("Y-m-d H:i:s");
        $NguoiNhan = $_GET['NguoiNhan'];
        $DiaChi = $_GET['DiaChi'];
        $GiaTien = $_GET['allMoney'];
        $SoDienThoai = $_GET['SoDienThoai'];
        $GhiChu = isset($_GET['GhiChu']) ? $_GET['GhiChu'] : "";
        $HinhThucThanhToan = 'momo';

        // Kiểm tra số tiền và mã giao dịch
        if (empty($GiaTien) || empty($CodeOrder)) {
            $error_message = 'Thanh toán không thành công.';
        } else {
            $sql_insert_invoice = "INSERT INTO donhang(ID_ThanhVien, ThoiGianLap, DiaChi, GiaTien, SoDienThoai, NguoiNhan, HinhThucThanhToan, CodeOrder) VALUES('$ID_ThanhVien', '$ThoiGianLap', '$DiaChi', '$GiaTien', '$SoDienThoai', '$NguoiNhan', '$HinhThucThanhToan', '$CodeOrder')";
            $insert_invoice_result = mysqli_query($mysqli, $sql_insert_invoice);

            if ($insert_invoice_result) {
                $id_order = mysqli_insert_id($mysqli);
                $sql_cart = "SELECT * FROM chitietgiohang WHERE chitietgiohang.ID_GioHang = $ID_GioHang";
                $query_cart = mysqli_query($mysqli, $sql_cart);

                while ($row = mysqli_fetch_assoc($query_cart)) {
                    $id_sanpham = $row['ID_SanPham'];
                    $soluong = $row['SoLuong'];
                    $sql_sanpham = "SELECT * FROM sanpham WHERE ID_SanPham = $id_sanpham";
                    $query_sanpham = mysqli_query($mysqli, $sql_sanpham);
                    $row_sanpham = mysqli_fetch_assoc($query_sanpham);
                    $giaMua = $row_sanpham['GiaBan'];
                    $sql_insert_order_detail = "INSERT INTO chitietdonhang (ID_DonHang, ID_SanPham, SoLuong, CodeOrder, GiaMua) VALUES ('$id_order', '$id_sanpham', '$soluong', '$CodeOrder', '$giaMua')";
                    $insert_detail_result = mysqli_query($mysqli, $sql_insert_order_detail);
                    $sql_update = "UPDATE sanpham SET SoLuong = SoLuong - $soluong WHERE ID_SanPham = $id_sanpham";
                    $query_update = mysqli_query($mysqli, $sql_update);
                }

                unset($_GET['allMoney']);
            } else {
                $error_message = 'Thanh toán bị hủy.';
            }
        }
    } else {
        $error_message = 'Thanh toán bị hủy.';
    }
}

// Xóa đơn hàng nếu có lỗi thanh toán
if (!empty($error_message)) {
    if (isset($CodeOrder)) {
        $sql_delete_order = "DELETE FROM donhang WHERE CodeOrder = '$CodeOrder'";
        mysqli_query($mysqli, $sql_delete_order);
    }
    echo "<script>
        alert('$error_message');
        window.location.href = 'http://localhost/pihung/index.php?navigate=cart'; // Chuyển hướng về trang xác nhận đơn hàng
    </script>";
    exit();
}
?>

<div class="container min-height-100">
    <div class="text-center mt-60">
        <p>Cảm ơn bạn đã đặt hàng, đơn hàng của bạn đang được xét duyệt</p>
        <a class="btn btn-info" href="index.php?navigate=orderHistory">Xem lịch sử đơn hàng</a>
    </div>
</div>
