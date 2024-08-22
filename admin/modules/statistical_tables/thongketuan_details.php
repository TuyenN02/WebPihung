<?php
header('Content-Type: application/json');
include('../../config/connection.php');

// Truy vấn để lấy doanh thu hàng ngày trong tuần
$query = "
    SELECT 
        DATE(ThoiGianLap) AS date3,
        SUM(GiaTien) AS dailySales
    FROM donhang
    WHERE ThoiGianLap >= CURDATE() - INTERVAL 7 DAY
    GROUP BY DATE(ThoiGianLap)
    ORDER BY DATE(ThoiGianLap) ASC
";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'dayOfWeek' => date('l', strtotime($row['date3'])), // Ngày trong tuần
            'dailySales' => (float)$row['dailySales']
        ];
    }
    echo json_encode($data);
} else {
    echo json_encode([]);
}

// Đóng kết nối
$mysqli->close();
?>
