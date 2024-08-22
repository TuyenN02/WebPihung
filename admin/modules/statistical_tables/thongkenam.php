<?php
header('Content-Type: application/json');

// Include database connection
include('../../config/connection.php');
// Get yearly data
$query = "
    SELECT DATE_FORMAT(ThoiGianLap, '%Y') AS date1, SUM(GiaTien) AS sales1
    FROM donhang
    WHERE ThoiGianLap >= NOW() - INTERVAL 1 YEAR
    GROUP BY DATE_FORMAT(ThoiGianLap, '%Y')
";

$result = $mysqli->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
