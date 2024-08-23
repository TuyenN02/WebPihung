<?php
	include("../../../admin/config/connection.php");
	require_once('config_vnpay.php');
	session_start();
	
	$ID_ThanhVien = $_SESSION['ID_ThanhVien'];
	$ID_GioHang = $_SESSION['ID_GioHang'];
    date_default_timezone_set('Asia/Ho_Chi_Minh');
	$ThoiGianLap = date("Y-m-d H:i:s");
    $NguoiNhan = $_SESSION['NguoiNhan'];
    $DiaChi = $_SESSION['DiaChi'];
	$GiaTien = $_SESSION['allMoney'];
    $SoDienThoai = $_SESSION['SoDienThoai'];
    $GhiChu = isset($_SESSION['GhiChu']) ? $_SESSION['GhiChu'] : "";
	
	$success = false; // Biến để theo dõi trạng thái thành công của thanh toán

	if(isset($_POST['cod'])){
		$HinhThucThanhToan = 'cod';
		$CodeOrder = rand(1, 10000);
		$sql_insert_invoice = "INSERT INTO donhang(ID_ThanhVien,ThoiGianLap,DiaChi,GiaTien,SoDienThoai,NguoiNhan,HinhThucThanhToan, CodeOrder) VALUES('".$ID_ThanhVien."','".$ThoiGianLap."','".$DiaChi."','".$GiaTien."','".$SoDienThoai."', '".$NguoiNhan."', '".$HinhThucThanhToan."', '$CodeOrder')";
		$insert_invoice_result = mysqli_query($mysqli, $sql_insert_invoice);
		
		if ($insert_invoice_result) {
			$id_order = mysqli_insert_id($mysqli);
			$_SESSION['ID_DonHang'] = $id_order;
			
			$sql_cart = "SELECT * FROM chitietgiohang WHERE chitietgiohang.ID_GioHang = $ID_GioHang";
			$query_cart = mysqli_query($mysqli, $sql_cart);
		
			while ($row = mysqli_fetch_assoc($query_cart)) {
				$id_sanpham = $row['ID_SanPham'];
				$soluong = $row['SoLuong'];
				$sql_sanpham = "SELECT * FROM sanpham WHERE ID_SanPham = $id_sanpham";s
				$query_sanpham = mysqli_query($mysqli, $sql_sanpham);
				$row_sanpham = mysqli_fetch_assoc($query_sanpham);
				$giaMua = $row_sanpham['GiaBan'];
				$sql_insert_order_detail = "INSERT INTO chitietdonhang (ID_DonHang, ID_SanPham, SoLuong, CodeOrder, GiaMua) VALUES ('$id_order', '$id_sanpham', '$soluong', '$CodeOrder', '$giaMua')";
				$insert_detail_result = mysqli_query($mysqli, $sql_insert_order_detail);
				$sql_update_product = "UPDATE sanpham 
				SET SoLuong = SoLuong - $soluong 
				WHERE ID_SanPham = $id_sanpham";
				$insert_detail_result1 = mysqli_query($mysqli, $sql_update_product);
				


			}
			// Không xóa sản phẩm trong giỏ hàng
			// $id_delete_cart = $_SESSION['ID_GioHang'];
			// $sql_delete_all_products = "DELETE FROM chitietgiohang WHERE ID_GioHang = $id_delete_cart";
			// $delete_result = mysqli_query($mysqli, $sql_delete_all_products);
			// unset($_SESSION['allMoney']);
			$sql_delete_cart_details1 = "DELETE FROM chitietgiohang WHERE ID_GioHang = $ID_GioHang";
				$insert_detail_result = mysqli_query($mysqli, $sql_delete_cart_details1);
			$success = true;
		} else {
			$_SESSION['error'] = "Đã xảy ra lỗi khi thêm đơn hàng vào cơ sở dữ liệu.";
		}
		
		if ($success) {
			header('Location: ../../../index.php?navigate=finish');
		} else {
			echo "<script>
				alert('Đã xảy ra lỗi khi thêm đơn hàng vào cơ sở dữ liệu.');
				window.location.href = 'http://localhost/pihung/pages/main/cart/cart_db.php'; // Đường dẫn tới trang giỏ hàng
			</script>";
			exit();
		}
	} else if(isset($_POST['vnpay'])) {
		$CodeOrder = rand(1, 10000);
		$_SESSION['CodeOrder'] = $CodeOrder;
		$vnp_TxnRef = $CodeOrder; // Mã giao dịch thanh toán tham chiếu của merchant
		$vnp_Amount = $GiaTien; // Số tiền thanh toán
		$vnp_Locale = "vn"; // Ngôn ngữ chuyển hướng thanh toán
		$vnp_BankCode = 'NCB'; // Mã phương thức thanh toán
		$vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // IP Khách hàng thanh toán
	
		$inputData = array(
			"vnp_Version" => "2.1.0",
			"vnp_TmnCode" => $vnp_TmnCode,
			"vnp_Amount" => $vnp_Amount * 100,
			"vnp_Command" => "pay",
			"vnp_CreateDate" => date('YmdHis'),
			"vnp_CurrCode" => "VND",
			"vnp_IpAddr" => $vnp_IpAddr,
			"vnp_Locale" => $vnp_Locale,
			"vnp_OrderInfo" => "Thanh toan GD:" . (string)$vnp_TxnRef,
			"vnp_OrderType" => "other",
			"vnp_ReturnUrl" => $vnp_Returnurl,
			"vnp_TxnRef" => $vnp_TxnRef,
			"vnp_ExpireDate" => $expire
		);
	
		if (isset($vnp_BankCode) && $vnp_BankCode != "") {
			$inputData['vnp_BankCode'] = $vnp_BankCode;
		}
	
		ksort($inputData);
		$query = "";
		$i = 0;
		$hashdata = "";
		foreach ($inputData as $key => $value) {
			if ($i == 1) {
				$hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
			} else {
				$hashdata .= urlencode($key) . "=" . urlencode($value);
				$i = 1;
			}
			$query .= urlencode($key) . "=" . urlencode($value) . '&';
		}
	
		$vnp_Url = $vnp_Url . "?" . $query;
		if (isset($vnp_HashSecret)) {
			$vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
			$vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
			$HinhThucThanhToan = 'vnpay';
			$sql_insert_invoice = "INSERT INTO donhang(ID_ThanhVien,ThoiGianLap,DiaChi,GiaTien,SoDienThoai,NguoiNhan,HinhThucThanhToan, CodeOrder) VALUES('".$ID_ThanhVien."','".$ThoiGianLap."','".$DiaChi."','".$GiaTien."','".$SoDienThoai."', '".$NguoiNhan."', '".$HinhThucThanhToan."', '$CodeOrder')";
			$insert_invoice_result = mysqli_query($mysqli, $sql_insert_invoice);
			
			if ($insert_invoice_result) {
				$id_order = mysqli_insert_id($mysqli);
				$_SESSION['ID_DonHang'] = $id_order;
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
				}
				// Không xóa sản phẩm trong giỏ hàng
				// $id_delete_cart = $_SESSION['ID_GioHang'];
				// $sql_delete_all_products = "DELETE FROM chitietgiohang WHERE ID_GioHang = $id_delete_cart";
				// $delete_result = mysqli_query($mysqli, $sql_delete_all_products);
				// unset($_SESSION['allMoney']);
				
				echo "<script>
					alert('Bạn đã được chuyển hướng đến trang thanh toán của VNPAY.');
					window.location.href = '$vnp_Url';
				</script>";
				exit();
			} else {
				$_SESSION['error'] = "Đã xảy ra lỗi khi thêm đơn hàng vào cơ sở dữ liệu.";
				echo "<script>
					alert('Đã xảy ra lỗi khi thêm đơn hàng vào cơ sở dữ liệu.');
					window.location.href = 'http://localhost/pihung/pages/main/cart/cart.php'; // Đường dẫn tới trang giỏ hàng
				</script>";
				exit();
			}
		}
	}
?>
