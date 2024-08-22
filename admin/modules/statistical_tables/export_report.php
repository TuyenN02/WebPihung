<?php
require_once '../../../vendor/autoload.php';
include('../../config/connection.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;


// Get parameters
$statType = $_GET['stat_type'];
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$orderDate = $_GET['date'];

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header with larger font and bold
$sheet->setCellValue('A1', 'Báo Cáo Thống Kê Đơn Hàng');
$sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->mergeCells('A1:H1');

// Set time range info with bold and fill color
$timeRange = ($statType === 'date-range' ? "Từ $startDate đến $endDate" : ($statType === 'specific-date' ? "Ngày $orderDate" : 'Tất Cả Thời Gian'));
$sheet->setCellValue('A2', 'Thời Gian Báo Cáo: ' . $timeRange);
$sheet->getStyle('A2')->getFont()->setBold(true);
$sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Yellow fill
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->mergeCells('A2:H2');

// Set column headers for order details
$headers = ['ID Đơn Hàng', 'Ngày Lập', 'Tên Sản Phẩm', 'Số Lượng', 'Giá Tiền', 'Người Nhận', 'Số Điện Thoại', 'Địa Chỉ'];
$columnLetter = 'A';
foreach ($headers as $index => $header) {
    $sheet->setCellValue($columnLetter . '3', $header);
    $sheet->getStyle($columnLetter . '3')->getFont()->setBold(true);
    $sheet->getStyle($columnLetter . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
    $columnLetter++;
}

// Prepare query based on report type
if ($statType === 'date-range') {
    $query = "
        SELECT dh.ID_DonHang, dh.ThoiGianLap, dh.NguoiNhan, dh.GiaTien, ctdh.SoLuong, sp.TenSanPham, dh.SoDienThoai, dh.DiaChi
        FROM donhang dh
        JOIN chitietdonhang ctdh ON dh.ID_DonHang = ctdh.ID_DonHang
        JOIN sanpham sp ON ctdh.ID_SanPham = sp.ID_SanPham
        WHERE dh.ThoiGianLap BETWEEN ? AND ? AND dh.XuLy = 5
        ORDER BY dh.ThoiGianLap ASC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
} elseif ($statType === 'specific-date') {
    $query = "
        SELECT dh.ID_DonHang, dh.ThoiGianLap, dh.NguoiNhan, dh.GiaTien, ctdh.SoLuong, sp.TenSanPham, dh.SoDienThoai, dh.DiaChi
        FROM donhang dh
        JOIN chitietdonhang ctdh ON dh.ID_DonHang = ctdh.ID_DonHang
        JOIN sanpham sp ON ctdh.ID_SanPham = sp.ID_SanPham
        WHERE DATE(dh.ThoiGianLap) = ? AND dh.XuLy = 5
        ORDER BY dh.ThoiGianLap ASC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $orderDate);
} else {
    $query = "
        SELECT dh.ID_DonHang, dh.ThoiGianLap, dh.NguoiNhan, dh.GiaTien, ctdh.SoLuong, sp.TenSanPham, dh.SoDienThoai, dh.DiaChi
        FROM donhang dh
        JOIN chitietdonhang ctdh ON dh.ID_DonHang = ctdh.ID_DonHang
        JOIN sanpham sp ON ctdh.ID_SanPham = sp.ID_SanPham
        WHERE dh.XuLy = 5
        ORDER BY dh.ThoiGianLap ASC
    ";
    $stmt = $mysqli->prepare($query);
}

// Execute query and fetch data
$stmt->execute();
$result = $stmt->get_result();
$rowNumber = 4;

$revenue = 0;
$soldOrders = 0;
$canceledOrders = 0;
$returnedOrders = 0;
$soldProducts = [];

while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue("A$rowNumber", $row['ID_DonHang']);
    $sheet->setCellValue("B$rowNumber", $row['ThoiGianLap']);
    $sheet->setCellValue("C$rowNumber", $row['TenSanPham']);
    $sheet->setCellValue("D$rowNumber", $row['SoLuong']);
    $sheet->setCellValue("E$rowNumber", $row['GiaTien']);
    $sheet->setCellValue("F$rowNumber", $row['NguoiNhan']);
    $sheet->setCellValue("G$rowNumber", $row['SoDienThoai']);
    $sheet->setCellValue("H$rowNumber", $row['DiaChi']);
    
    $revenue += $row['GiaTien'];
    $soldOrders++;
    $soldProducts[] = $row['TenSanPham'];
    $rowNumber++;
}

// Fetch order statistics
$statQuery = "
    SELECT 
        SUM(GiaTien) AS revenue,
        COUNT(CASE WHEN XuLy = 5 THEN 1 END) AS soldOrders,
        SUM(CASE WHEN XuLy = 2 THEN 1 ELSE 0 END) AS canceledOrders,
        SUM(CASE WHEN XuLy = 7 THEN 1 ELSE 0 END) AS returnedOrders
    FROM donhang
    WHERE (ThoiGianLap BETWEEN ? AND ? OR ? IS NULL) AND XuLy = 5
";
$statStmt = $mysqli->prepare($statQuery);
$statStmt->bind_param('sss', $startDate, $endDate, $orderDate);
$statStmt->execute();
$stats = $statStmt->get_result()->fetch_assoc();

$soldOrders = $stats['soldOrders'] ?? 0;
$revenue = $stats['revenue'] ?? 0;
$canceledOrders = $stats['canceledOrders'] ?? 0;
$returnedOrders = $stats['returnedOrders'] ?? 0;

// Add statistics table
$sheet->setCellValue('J3', 'Tổng Doanh Thu');
$sheet->setCellValue('K3', $revenue > 0 ? $revenue : '0');
$sheet->setCellValue('J4', 'Số Đơn Bán Được');
$sheet->setCellValue('K4', $soldOrders > 0 ? $soldOrders : '0');
$sheet->setCellValue('J5', 'Số Đơn Bị Hủy');
$sheet->setCellValue('K5', $canceledOrders > 0 ? $canceledOrders : '0');
$sheet->setCellValue('J6', 'Số Đơn Hoàn Trả');
$sheet->setCellValue('K6', $returnedOrders > 0 ? $returnedOrders : '0');
$sheet->setCellValue('J7', 'Tên Sản Phẩm Đã Bán');
$sheet->setCellValue('K7', !empty($soldProducts) ? implode(', ', array_unique($soldProducts)) : 'Chưa có sản phẩm nào bán');

// Style the statistics table
$sheet->getStyle('J3:K7')->getFont()->setBold(true);
$sheet->getStyle('J3:K7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('J3:K7')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

// Close statement and connection
$stmt->close();
$statStmt->close();
$mysqli->close();

// Set headers to download the file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Report_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Write file to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
