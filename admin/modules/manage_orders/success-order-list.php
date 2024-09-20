<?php
// Handle search
$tukhoa = '';
$trangThai = '';

if (isset($_POST['tukhoa'])) {
    // Loại bỏ khoảng trắng dư thừa và các ký tự đặc biệt
    $tukhoa = mysqli_real_escape_string($mysqli, trim($_POST['tukhoa']));
    $tukhoa = preg_replace('/\s+/', ' ', $tukhoa); // Loại bỏ khoảng trắng dư thừa giữa các từ
}

if (isset($_POST['trangthai'])) {
    // Loại bỏ khoảng trắng dư thừa và các ký tự đặc biệt
    $trangThai = mysqli_real_escape_string($mysqli, trim($_POST['trangthai']));
}

// Xây dựng câu truy vấn SQL dựa trên tìm kiếm và trạng thái
$sql = "SELECT * FROM donhang WHERE 1=1"; // Dùng 1=1 để dễ nối các điều kiện sau

if ($tukhoa !== '') {
    // Tìm kiếm theo mã đơn hàng
    $sql .= " AND (donhang.ID_DonHang LIKE '%" . $tukhoa . "%' 
         OR donhang.DiaChi LIKE '%" . $tukhoa . "%' 
         OR donhang.NguoiNhan LIKE '%" . $tukhoa . "%')";

}

if ($trangThai !== '') {
    // Lọc theo trạng thái đơn hàng
    $sql .= " AND donhang.XuLy = '" . $trangThai . "'"; // Chú ý thêm dấu nháy đơn nếu là chuỗi
}

// Nhóm kết quả theo ID đơn hàng và sắp xếp theo ID giảm dần
$sql .= " GROUP BY donhang.ID_DonHang";
$sql .= " ORDER BY donhang.ID_DonHang DESC";
// Thực hiện truy vấn
echo ($sql);
$query_order = mysqli_query($mysqli, $sql);

// Check status messages
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($status === 'success') {
    echo '<div class="alert alert-success" role="alert" id="status-alert">Duyệt đơn hàng thành công</div>';
} elseif ($status === 'error') {
    echo '<div class="alert alert-danger" role="alert" id="status-alert">Có lỗi xảy ra trong quá trình duyệt đơn hàng. Vui lòng thử lại</div>';
} elseif ($status === 'cancel_success') {
    echo '<div class="alert alert-success" role="alert" id="status-alert">Hủy đơn hàng thành công</div>';
}
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0" style="text-align: left; flex-grow: 1; font-size: 28px;">Danh sách đơn hàng</h5>
            <div class="form-search d-flex align-items-center" style = 'width: 405px;'>
                <form action="" method="POST" class="d-flex">
                    <input type="text" class="form-control form-search" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                    <select name="trangthai" class="form-control ml-2">
                        <option value="">Tất cả trạng thái</option>
                        <option value="0" <?php if ($trangThai == '0') echo 'selected'; ?>>Chưa xác nhận</option>
                        <option value="1" <?php if ($trangThai == '1') echo 'selected'; ?>>Đã xác nhận</option>
                        <option value="2" <?php if ($trangThai == '2') echo 'selected'; ?>>Đã hủy</option>
                        <option value="3" <?php if ($trangThai == '3') echo 'selected'; ?>>Chờ lấy hàng</option>
                        <option value="4" <?php if ($trangThai == '4') echo 'selected'; ?>>Đang giao hàng</option>
                        <option value="5" <?php if ($trangThai == '5') echo 'selected'; ?>>Giao hàng thành công</option>
                        <option value="6" <?php if ($trangThai == '6') echo 'selected'; ?>>Đã hoàn trả</option>
                        <option value="7" <?php if ($trangThai == '7') echo 'selected'; ?>>Lỗi vận chuyển</option>
                    </select>
                    <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                </form>
            </div>
        </div>

        <table class="table table-striped table-checkall">
            <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Mã Đơn Hàng</th>
                    <th scope="col">Người Nhận</th>
                    <th scope="col">Địa chỉ</th>
                    <th scope="col">Tổng Tiền</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Xem chi tiết</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if (mysqli_num_rows($query_order) > 0) {
                $num = 0;
                while ($row_Order = mysqli_fetch_array($query_order)) {
                    $num++;
                    $currentStatus = $row_Order['XuLy'];
            ?>
                <tr>
        <td><?php echo $num; ?></td>
        <td><?php echo htmlspecialchars($row_Order['ID_DonHang']); ?></td>

        <td><?php echo $row_Order['NguoiNhan']; ?></td>
        <td><?php echo htmlspecialchars($row_Order['DiaChi']); ?></td>
        <td><?php echo number_format((int)$row_Order['GiaTien'], 0, ',', '.'); ?> VND</td>
        <td>
                        <form id="status-form-<?php echo $row_Order['ID_DonHang']; ?>" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo (int)$row_Order['ID_DonHang']; ?>">
                            <select name="order_status" class="form-control" 
                                onchange="updateOrderStatus(<?php echo (int)$row_Order['ID_DonHang']; ?>, this.value)"
                                style="text-align: center; text-align-last: center; font-size: 15px; width: 164px;"
                                <?php echo ($currentStatus == 2) ? 'disabled' : ''; ?>
                                <?php echo ($currentStatus == 5) ? 'disabled' : ''; ?>>
                                <?php if ($currentStatus == 1) { ?>
                                    <option value="1" selected>Đã xác nhận</option>
                                    <option value="3">Chờ lấy hàng</option>
                                <?php } elseif ($currentStatus == 0) { ?>
                                    <option value="0"selected>Chưa xác nhận</option>
                                    <option value="1">Đã xác nhận</option>
                              
                                <?php } elseif ($currentStatus == 3) { ?>
                                    <option value="3" selected>Chờ lấy hàng</option>
                                    <option value="4">Đang giao hàng</option>
                                <?php } elseif ($currentStatus == 2) { ?>
                                    <option value="2" selected>Đã hủy</option>
                                <?php } elseif ($currentStatus == 4) { ?>
                                    <option value="4" selected>Đang giao hàng</option>
                                    <option value="5">Giao hàng thành công</option>
                                    <option value="8">Lỗi vận chuyển</option>
                                    <option value="6" >Đã hoàn trả</option>
                                <?php } elseif ($currentStatus == 5) { ?>
                                    <option value="5" selected>Giao thành công</option>
                                <?php } elseif ($currentStatus == 6) { ?>
                                    <option value="6" selected>Đã hoàn trả</option>
                                <?php } elseif ($currentStatus == 8) { ?>
                                    <option value="8" selected>Lỗi vận chuyển</option>
                                    <option value="4">Đang giao hàng</option>
                                    <option value="5">Giao hàng thành công</option>
                                <?php } ?>
                            </select>
                        </form>
                    </td>
                    <td><a href="?order=order-detail&id=<?php echo (int)$row_Order['ID_DonHang']; ?>">Xem chi tiết</a></td>
                </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="7">Danh sách đơn hàng trống!</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function confirmCancel(id) {
        if (confirm("Bạn có chắc chắn muốn hủy đơn hàng này?")) {
            window.location.href = 'modules/manage_orders/xulyHuy3.php?id=' + id;
        }
    }

    function updateOrderStatus(orderId, status) {
    if (confirm("Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này?")) {
        $.ajax({
            url: 'modules/manage_orders/update_status.php',
            type: 'POST',
            data: {
                order_id: orderId,
                order_status: status
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Thêm thông báo cập nhật thành công
                    var successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success';
                    successAlert.innerHTML = 'Cập nhật trạng thái đơn hàng thành công';
                    document.getElementById('content').prepend(successAlert);
                    
                    // Ẩn thông báo sau 3 giây
                    setTimeout(function() {
                        successAlert.style.display = 'none';
                    }, 3000);
                    
                    // Tải lại trang để cập nhật dữ liệu
                    setTimeout(function() {
                        window.location.reload();
                    }, 3500);
                } else {
                    alert("Có lỗi xảy ra khi cập nhật trạng thái.");
                }
            }
        });
    }
}
</script>

<style>
    .form-search {
        display: flex;
        align-items: center;
        margin-left: -130px; /* Dịch toàn bộ form về bên trái */
    }

    .form-control.form-search {
        margin-right: 1px; /* Điều chỉnh khoảng cách giữa các phần tử bên trong form */
    }

#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 100px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
/* Cập nhật CSS để điều chỉnh độ rộng của cột trạng thái */
.table th:nth-child(6),
.table td:nth-child(6) {
    width: 150px; /* Thay đổi giá trị này theo nhu cầu của bạn */
    overflow: hidden;
    text-overflow: ellipsis; /* Thêm dấu ... nếu nội dung vượt quá chiều rộng */
    white-space: nowrap; /* Ngăn không cho dòng mới xuất hiện trong cột */
}
table {
    font-size: 13px;
    width: 100%;
    border-collapse: collapse;
}

    /* Cập nhật kích thước của input và select */
    input[type="text"].form-control.form-search {
        width: 230px; /* Thay đổi chiều rộng của ô tìm kiếm */
    }

    select[name="trangthai"] {
        width: 200px; /* Thay đổi chiều rộng của select */
    }

    input[type="submit"].btn.btn-primary.ml-2 {
        width: 100px; /* Thay đổi chiều rộng của nút Tìm kiếm */
    }
    .alert {
    position: fixed;
    top: 50px;
    right: 130px;
    padding: 15px;
    border-radius: 5px;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.alert-success {
    background-color: #d4edda;
    color: #269963;
    border: 3px solid #269963;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
table {
    font-size: 13px;
    width: 100%;
    border-collapse: collapse; /* Đảm bảo không có khoảng cách giữa các đường viền của các ô */
}

table, th, td {
    border: 1px solid rgba(0, 0, 0, 0.1); /* Viền màu đen mờ */
}

th, td {
    padding: 8px; /* Thêm khoảng đệm cho các ô */
    text-align: center; /* Căn giữa nội dung */
    vertical-align: middle; /* Căn giữa theo chiều dọc */
    background-color: #f2f2f2; /* Màu nền cho tiêu đề bảng */
    white-space: nowrap; /* Ngăn tiêu đề xuống dòng */
}

thead {
    background-color: #f2f2f2; /* Màu nền cho tiêu đề bảng */
}

</style>
