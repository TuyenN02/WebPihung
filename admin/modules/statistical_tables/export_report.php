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
$sheet->mergeCells('A1:I1');

// Set time range info with bold and fill color
$timeRange = ($statType === 'date-range' ? ($startDate === $endDate ? "Ngày $startDate" : "Từ $startDate đến $endDate") : ($statType === 'specific-date' ? "Ngày $orderDate" : 'Thống kê toàn bộ'));
$sheet->setCellValue('A2', 'Thời Gian Báo Cáo: ' . $timeRange);
$sheet->getStyle('A2')->getFont()->setBold(true);
$sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Yellow fill
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->mergeCells('A2:I2');

// Set column headers for order details
$headers = ['ID Đơn Hàng', 'Ngày Lập', 'Tên Sản Phẩm', 'Số Lượng', 'Giá Tiền', 'Người Nhận', 'Số Điện Thoại', 'Địa Chỉ', 'Trạng Thái'];
$columnLetter = 'A';
foreach ($headers as $index => $header) {
    $sheet->setCellValue($columnLetter . '3', $header);
    $sheet->getStyle($columnLetter . '3')->getFont()->setBold(true);
    $sheet->getStyle($columnLetter . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
    $columnLetter++;
}

// Prepare query for sold orders
if ($statType === 'date-range') {
    $query = "
        SELECT dh.ID_DonHang, DATE(dh.ThoiGianLap) AS NgayLap, dh.NguoiNhan, dh.GiaTien, ctdh.SoLuong, sp.TenSanPham, dh.SoDienThoai, dh.DiaChi, dh.XuLy
        FROM donhang dh
        JOIN chitietdonhang ctdh ON dh.ID_DonHang = ctdh.ID_DonHang
        JOIN sanpham sp ON ctdh.ID_SanPham = sp.ID_SanPham
        WHERE DATE(dh.ThoiGianLap) BETWEEN ? AND ? AND dh.XuLy IN (2, 5, 6)
        ORDER BY dh.ThoiGianLap ASC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
} elseif ($statType === 'specific-date') {
    $query = "
        SELECT dh.ID_DonHang, DATE(dh.ThoiGianLap) AS NgayLap, dh.NguoiNhan, dh.GiaTien, ctdh.SoLuong, sp.TenSanPham, dh.SoDienThoai, dh.DiaChi, dh.XuLy
        FROM donhang dh
        JOIN chitietdonhang ctdh ON dh.ID_DonHang = ctdh.ID_DonHang
        JOIN sanpham sp ON ctdh.ID_SanPham = sp.ID_SanPham
        WHERE DATE(dh.ThoiGianLap) = ? AND dh.XuLy IN (2, 5, 6)
        ORDER BY dh.ThoiGianLap ASC
    ";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $orderDate);
} else {
    $query = "
        SELECT dh.ID_DonHang, DATE(dh.ThoiGianLap) AS NgayLap, dh.NguoiNhan, dh.GiaTien, ctdh.SoLuong, sp.TenSanPham, dh.SoDienThoai, dh.DiaChi, dh.XuLy
        FROM donhang dh
        JOIN chitietdonhang ctdh ON dh.ID_DonHang = ctdh.ID_DonHang
        JOIN sanpham sp ON ctdh.ID_SanPham = sp.ID_SanPham
        WHERE dh.XuLy IN (2, 5, 6)
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
$stockLevels = [];

// Define arrays to store statistics
while ($row = $result->fetch_assoc()) {
    $status = '';
    if ($row['XuLy'] == 5) {
        $status = 'Đã Bán';
        $revenue += $row['GiaTien'];
        $soldOrders++;
        $soldProducts[] = $row['TenSanPham'];
    } elseif ($row['XuLy'] == 2) {
        $status = 'Đã Hủy';
        $canceledOrders++;
    } elseif ($row['XuLy'] == 6) {
        $status = 'Hoàn Trả';
        $returnedOrders++;
    }
    
    $sheet->setCellValue("A$rowNumber", $row['ID_DonHang']);
    $sheet->setCellValue("B$rowNumber", $row['NgayLap']);
    $sheet->setCellValue("C$rowNumber", $row['TenSanPham']);
    $sheet->setCellValue("D$rowNumber", $row['SoLuong']);
    $sheet->setCellValue("E$rowNumber", $row['GiaTien']);
    $sheet->setCellValue("F$rowNumber", $row['NguoiNhan']);
    $sheet->setCellValue("G$rowNumber", $row['SoDienThoai']);
    $sheet->setCellValue("H$rowNumber", $row['DiaChi']);
    $sheet->setCellValue("I$rowNumber", $status);
    
    $rowNumber++;
}

// Add summary table
$summaryStartRow = $rowNumber + 2;

$sheet->setCellValue("A$summaryStartRow", "Tổng Đơn Hàng Đã Bán:");
$sheet->setCellValue("B$summaryStartRow", $soldOrders);
$sheet->setCellValue("A" . ($summaryStartRow + 1), "Doanh Thu:");
$sheet->setCellValue("B" . ($summaryStartRow + 1), $revenue);
$sheet->setCellValue("A" . ($summaryStartRow + 2), "Đơn Hàng Đã Hủy:");
$sheet->setCellValue("B" . ($summaryStartRow + 2), $canceledOrders);
$sheet->setCellValue("A" . ($summaryStartRow + 3), "Đơn Hàng Hoàn Trả:");
$sheet->setCellValue("B" . ($summaryStartRow + 3), $returnedOrders);

// Set headers to download the file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Report_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Write file to output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>