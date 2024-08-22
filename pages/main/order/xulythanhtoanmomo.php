<?php
header('Content-type: text/html; charset=utf-8');

// Hàm gửi yêu cầu POST tới URL với dữ liệu JSON
function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    // Thực thi yêu cầu POST
    $result = curl_exec($ch);
    // Đóng kết nối
    curl_close($ch);
    return $result;
}

// Thông tin cấu hình Momo
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = 'MOMOBKUN20180529'; // Thay đổi mã đối tác của bạn
$accessKey = 'klm05TvNBzhg7h7j'; // Thay đổi khóa truy cập của bạn
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'; // Thay đổi khóa bí mật của bạn

// Thông tin đơn hàng
$orderInfo = "Thanh toán qua mã QR MoMo";
$amount = $_POST['tongtien_vnd']; // Số tiền thanh toán
$orderId = time(); // ID đơn hàng
$redirectUrl = "http://localhost/pihung/index.php?navigate=finish"; // URL chuyển hướng sau khi thanh toán
$ipnUrl = "http://localhost/pihung/index.php?navigate=finish"; // URL thông báo kết quả thanh toán
$extraData = ""; // Dữ liệu thêm nếu có

$requestId = time(); // ID yêu cầu
$requestType = "captureWallet"; // Loại yêu cầu

// Tạo chữ ký HMAC SHA256
$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
$signature = hash_hmac("sha256", $rawHash, $secretKey);

// Dữ liệu gửi tới Momo
$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    'storeId' => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);

// Gửi yêu cầu tới API Momo
$result = execPostRequest($endpoint, json_encode($data));
$jsonResult = json_decode($result, true); // Giải mã kết quả JSON

// Kiểm tra kết quả trả về và chuyển hướng đến URL thanh toán
if (isset($jsonResult['payUrl']) && !empty($jsonResult['payUrl'])) {
    header('Location: ' . $jsonResult['payUrl']);
    exit();
} else {
    // Xử lý lỗi nếu không nhận được URL thanh toán
    echo "Có lỗi xảy ra khi tạo yêu cầu thanh toán.";
    // Có thể thêm mã lỗi và thông báo chi tiết nếu cần
}
?>
