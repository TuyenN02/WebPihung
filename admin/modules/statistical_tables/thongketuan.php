
<?php
header('Content-Type: application/json');

// Include database connection
include('../../config/connection.php');

// Get weekly data
$query = "
    SELECT DATE_FORMAT(ThoiGianLap, '%Y-%m-%d') AS date3, SUM(GiaTien) AS sales3
    FROM donhang
    WHERE ThoiGianLap >= NOW() - INTERVAL 1 WEEK
    GROUP BY DATE_FORMAT(ThoiGianLap, '%Y-%m-%d')
";

$result = $mysqli->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>