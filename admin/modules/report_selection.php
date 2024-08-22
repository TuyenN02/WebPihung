<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Thời Gian Báo Cáo</title>
</head>
<body>
    <div style="width: 50%; margin: auto; padding: 20px; background: #f9f9f9; border-radius: 8px;">
        <h2>Chọn Thời Gian Báo Cáo</h2>
        <form action="generate_report.php" method="POST">
            <label for="start_date">Từ Ngày:</label>
            <input type="date" id="start_date" name="start_date" required>
            <br><br>
            <label for="end_date">Đến Ngày:</label>
            <input type="date" id="end_date" name="end_date" required>
            <br><br>
            <input type="submit" value="Xác Nhận In">
        </form>
    </div>
</body>
</html>
