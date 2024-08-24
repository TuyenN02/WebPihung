<?php
header('Content-Type: application/json');

// Include database connection
include('../../config/connection.php');

// Get monthly data
$query = "
    SELECT DATE_FORMAT(ThoiGianLap, '%Y-%m') AS date2, SUM(GiaTien) AS sales2
    FROM donhang
    WHERE ThoiGianLap >= NOW() - INTERVAL 1 MONTH
    AND XuLy = 5
    GROUP BY DATE_FORMAT(ThoiGianLap, '%Y-%m')
";

$result = $mysqli->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
