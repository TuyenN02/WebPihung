<?php
// Include file kết nối cơ sở dữ liệu
$mysqli = new mysqli("localhost", "root", "", "pihung");

// Check connection
if ($mysqli->connect_errno) {
    echo "Kết nối thất bại: " . $mysqli->connect_error;
    exit();
}

require_once '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$filter_by_date = isset($_POST['filter_by_date']) ? $_POST['filter_by_date'] : '';
$full_report = isset($_POST['full_report']) ? $_POST['full_report'] : '';

if ($start_date && $end_date) {
    if ($start_date === $end_date) {
        $date_filter = "DATE(ThoiGianLap) = '$start_date'";
    } else {
        $date_filter = "DATE(ThoiGianLap) BETWEEN '$start_date' AND '$end_date'";
    }
} else {
    $date_filter = '';
}

$sql_sold = "SELECT COUNT(*) AS sold_orders FROM donhang WHERE XuLy = 5";
if ($filter_by_date === 'on' && $date_filter) {
    $sql_sold .= " AND $date_filter";
}
$result_sold = $mysqli->query($sql_sold);
$sold_orders = $result_sold->fetch_assoc()['sold_orders'];

$sql_shipping = "SELECT COUNT(*) AS shipping_orders FROM donhang WHERE XuLy = 4";
if ($filter_by_date === 'on' && $date_filter) {
    $sql_shipping .= " AND $date_filter";
}
$result_shipping = $mysqli->query($sql_shipping);
$shipping_orders = $result_shipping->fetch_assoc()['shipping_orders'];

$sql_stock = "SELECT SUM(SoLuong) AS total_products FROM sanpham";
$result_stock = $mysqli->query($sql_stock);
$total_products = $result_stock->fetch_assoc()['total_products'];

$sql_revenue = "SELECT SUM(GiaTien) AS total_revenue FROM donhang WHERE XuLy = 5";
if ($filter_by_date === 'on' && $date_filter) {
    $sql_revenue .= " AND $date_filter";
}
$result_revenue = $mysqli->query($sql_revenue);
$total_revenue = $result_revenue->fetch_assoc()['total_revenue'];

$sql_pending = "SELECT COUNT(*) AS pending_orders FROM donhang WHERE XuLy = 0";
if ($filter_by_date === 'on' && $date_filter) {
    $sql_pending .= " AND $date_filter";
}
$result_pending = $mysqli->query($sql_pending);
$pending_orders = $result_pending->fetch_assoc()['pending_orders'];

if (isset($_POST['export_report'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'Báo cáo thống kê');
    $sheet->setCellValue('A2', 'Thời gian báo cáo: ' . ($full_report === 'on' ? 'Tổng thống kê' : 'Từ ngày ' . $start_date . ' đến ngày ' . $end_date));

    $sheet->setCellValue('A4', 'Số đơn hàng đã bán:');
    $sheet->setCellValue('B4', $sold_orders);
    $sheet->setCellValue('A5', 'Số đơn hàng đang giao:');
    $sheet->setCellValue('B5', $shipping_orders);
    $sheet->setCellValue('A6', 'Số sản phẩm trong kho:');
    $sheet->setCellValue('B6', $total_products);
    $sheet->setCellValue('A7', 'Doanh thu:');
    $sheet->setCellValue('B7', number_format($total_revenue, 0, ',', '.'));

    $sheet->getColumnDimension('A')->setWidth(30);
    $sheet->getColumnDimension('B')->setWidth(30);
    
    $sql_order_details = "SELECT * FROM donhang WHERE XuLy = 5";
    if ($filter_by_date === 'on' && $date_filter) {
        $sql_order_details .= " AND $date_filter";
    }
    $result_order_details = $mysqli->query($sql_order_details);

    $sheet->setCellValue('A9', 'Danh sách sản phẩm');
    $sheet->setCellValue('A10', 'Mã đơn hàng');
    $sheet->setCellValue('B10', 'Tên sản phẩm');
    $sheet->setCellValue('C10', 'Số lượng');
    $sheet->setCellValue('D10', 'Giá bán');
    $sheet->setCellValue('E10', 'Người nhận');
    $sheet->setCellValue('F10', 'Số điện thoại');
    $sheet->setCellValue('G10', 'Địa chỉ');

    $row = 11;
    while ($row_data = $result_order_details->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $row_data['ID_DonHang']);
        $sheet->setCellValue('B' . $row, $row_data['NguoiNhan']);
        $sheet->setCellValue('C' . $row, $row_data['SoDienThoai']);
        $sheet->setCellValue('D' . $row, $row_data['DiaChi']);
        $sheet->setCellValue('E' . $row, number_format($row_data['GiaTien'], 0, ',', '.'));
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'bao_cao_' . date('YmdHis') . '.xlsx';
    $filePath = __DIR__ . '/vendor/' . $fileName;
    $writer->save($filePath);

    echo '<a href="' . $filePath . '" download>Download báo cáo</a>';
    exit();
}

$mysqli->close();
?>
