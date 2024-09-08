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
        
    <div class="card-header d-flex align-items-center justify-content-between">
    <a href="index.php?policy=list-policy" class="btn btn-secondary btn-back">Quay lại</a>
    <h5 class="title"><?php echo htmlspecialchars($row['TieuDe']); ?></h5>
</div>
        <div class="card-body">
            <p><?php echo nl2br(htmlspecialchars($row['NoiDung'])); ?></p>
            
        </div>
    </div>
</div>
<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 100px;
}
.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between; /* Đưa nút và tiêu đề về hai phía */
    padding: 10px;
}

.btn-back {
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    background-color: #2682f2; /* Màu nền của nút */
    border: none;
}

.title {
    font-size: 1.5rem; /* Sử dụng rem để tương thích trên nhiều màn hình */
    text-align: center;
    margin: 0; /* Xóa khoảng cách mặc định của h5 */
    flex-grow: 1;
}

@media (max-width: 768px) {
    .title {
        font-size: 1.2rem; /* Thu nhỏ kích thước tiêu đề trên màn hình nhỏ */
    }
    .btn-back {
        padding: 8px 16px; /* Điều chỉnh kích thước nút trên màn hình nhỏ */
    }
}
</style>