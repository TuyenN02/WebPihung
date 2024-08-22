<?php


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql_policy = "SELECT * FROM chinhsach WHERE ID_ChinhSach='$id' LIMIT 1";
    $query_policy = mysqli_query($mysqli, $sql_policy);
    $row = mysqli_fetch_array($query_policy);
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h5><?php echo htmlspecialchars($row['TieuDe']); ?></h5>
        </div>
        <div class="card-body">
            <p><?php echo nl2br(htmlspecialchars($row['NoiDung'])); ?></p>
            <a href="index.php?policy=list-policy" class="btn btn-secondary">Quay lại danh sách</a>
        </div>
    </div>
</div>
