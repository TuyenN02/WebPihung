<?php
header('Content-Type: application/json');

// Include database connection
include('../../config/connection.php');

// Get monthly data
$query = "
    SELECT DATE_FORMAT(ThoiGianLap, '%Y-%m-%d') AS date2, SUM(GiaTien) AS sales2
    FROM donhang
    WHERE ThoiGianLap >= NOW() - INTERVAL 1 MONTH
    GROUP BY DATE_FORMAT(ThoiGianLap, '%Y-%m-%d')
";

$result = $mysqli->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
