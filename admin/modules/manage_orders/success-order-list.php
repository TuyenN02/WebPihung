<?php
// Handle search
$tukhoa = '';
$trangThai = '';
if (isset($_POST['tukhoa'])) {
    $tukhoa = mysqli_real_escape_string($mysqli, trim($_POST['tukhoa']));
    $tukhoa = preg_replace('/\s+/', ' ', $tukhoa); // Remove excess spaces between words
}

if (isset($_POST['trangthai'])) {
    $trangThai = mysqli_real_escape_string($mysqli, trim($_POST['trangthai']));
}

// Construct the SQL query based on search and status filter
$sql = "
    SELECT donhang.ID_DonHang, donhang.ID_ThanhVien, donhang.ThoiGianLap, donhang.DiaChi, donhang.GiaTien,
           chitietdonhang.ID_SanPham, sanpham.TenSanPham, chitietdonhang.SoLuong, donhang.XuLy
    FROM donhang
    INNER JOIN chitietdonhang ON donhang.ID_DonHang = chitietdonhang.ID_DonHang
    INNER JOIN sanpham ON chitietdonhang.ID_SanPham = sanpham.ID_SanPham
    WHERE donhang.XuLy IN ('0','1', '2', '3', '4', '5', '8')";

if (!empty($tukhoa)) {
    $sql .= " AND sanpham.TenSanPham LIKE '%" . $tukhoa . "%'";
}

if (!empty($trangThai)) {
    $sql .= " AND donhang.XuLy = '" . $trangThai . "'";
}

$sql .= " ORDER BY donhang.ID_DonHang DESC";

$query_order = mysqli_query($mysqli, $sql);

// Check status messages
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($status === 'success') {
    echo '<div class="alert alert-success" role="alert" id="status-alert">Duyệt đơn hàng thành công!</div>';
} elseif ($status === 'error') {
    echo '<div class="alert alert-danger" role="alert" id="status-alert">Có lỗi xảy ra trong quá trình duyệt đơn hàng. Vui lòng thử lại!</div>';
} elseif ($status === 'cancel_success') {
    echo '<div class="alert alert-success" role="alert" id="status-alert">Hủy đơn hàng thành công!</div>';
}
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Danh sách đơn hàng</h5>
            <div class="form-search d-flex align-items-center">
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
                        <option value="8" <?php if ($trangThai == '8') echo 'selected'; ?>>Lỗi vận chuyển</option>
                    </select>
                    <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                </form>
            </div>
        </div>

        <table class="table table-striped table-checkall">
            <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Tên sản phẩm</th>
                    <th scope="col">Số lượng</th>
                    <th scope="col">Địa chỉ</th>
                    <th scope="col">Giá tiền</th>
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
                    <td><?php echo $num ?></td>
                    <td><?php echo $row_Order['TenSanPham'] ?></td>
                    <td><?php echo $row_Order['SoLuong'] ?></td>
                    <td><?php echo $row_Order['DiaChi'] ?></td>
                    <td><?php echo number_format($row_Order['GiaTien'], 0, ',', '.') ?> VND</td>
                    <td>
                        <form id="status-form-<?php echo $row_Order['ID_DonHang']; ?>" method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $row_Order['ID_DonHang']; ?>">
                            <select name="order_status" class="form-control" 
        onchange="updateOrderStatus(<?php echo $row_Order['ID_DonHang']; ?>, this.value)"
        style="text-align: center; text-align-last: center;width: 190px;">
    <?php if ($currentStatus == 1) { ?>
        <option value="1" selected>Đã xác nhận!</option>
        <option value="3">Chờ lấy hàng...</option>
    <?php } elseif ($currentStatus == 0) { ?>
        <option value="0" selected>Chưa xác nhận!</option>
        <option value="1">Đã xác nhận!</option>
        <option value="2">Hủy đơn!</option>
    <?php } elseif ($currentStatus == 3) { ?>
        <option value="3" selected>Chờ lấy hàng...</option>
        <option value="4">Đang giao hàng...</option>
    <?php } elseif ($currentStatus == 2) { ?>
        <option value="2" selected>Hủy đơn!</option>
    <?php } elseif ($currentStatus == 4) { ?>
        <option value="4" selected>Đang giao hàng...</option>
        <option value="5">Giao hàng thành công!</option>
        <option value="8">Lỗi vận chuyển!</option>
    <?php } elseif ($currentStatus == 5) { ?>
        <option value="5" selected>Giao hàng thành công!</option>
    <?php } elseif ($currentStatus == 6) { ?>
        <option value="6" selected>Đã hoàn trả!</option>
    <?php } elseif ($currentStatus == 8) { ?>
        <option value="8" selected>Lỗi vận chuyển!</option>
        <option value="4">Đang giao hàng...</option>
        <option value="5">Giao hàng thành công!</option>
    <?php } ?>
</select>
                        </form>
                    </td>
                    <td><a href="?order=order-detail&id=<?php echo $row_Order['ID_DonHang'] ?>">Xem chi tiết</a></td>
                   
                       
                   
                </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="8">Danh sách đơn hàng trống</td></tr>';
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
                        window.location.reload();
                    } else {
                        alert("Có lỗi xảy ra khi cập nhật trạng thái.");
                    }
                }
            });
        }
    }

    setTimeout(function() {
        var alert = document.getElementById('status-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 3000);
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
    font-size: 14px;
    width: 100%;
    border-collapse: collapse;
}

td, th {
    vertical-align: middle;
    border: 1px solid #dee2e6;
    text-align: center;
}

thead th {
    background-color: #f8f9fa;
}

tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

tbody tr:hover {
    background-color: #e9ecef;
}

.form-control {
    font-size: 14px;
}
</style>