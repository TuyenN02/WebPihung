<?php
if (isset($_SESSION['ID_ThanhVien'])) {
  $ID_ThanhVien = $_SESSION['ID_ThanhVien'];

  // Xử lý tìm kiếm
  $searchKeyword = "";
  if (isset($_POST['search'])) {
    // Loại bỏ dấu cách đầu và cuối của từ khóa tìm kiếm
    $searchKeyword = trim($_POST['keyword']);
    $sql_getOrder = "SELECT donhang.*, GROUP_CONCAT(sanpham.TenSanPham SEPARATOR ', ') AS TenSanPham, 
                     GROUP_CONCAT(chitietdonhang.SoLuong SEPARATOR ', ') AS SoLuong 
                     FROM donhang 
                     INNER JOIN chitietdonhang ON donhang.ID_DonHang = chitietdonhang.ID_DonHang 
                     INNER JOIN sanpham ON chitietdonhang.ID_SanPham = sanpham.ID_SanPham 
                     WHERE donhang.ID_ThanhVien = $ID_ThanhVien 
                     AND (donhang.ID_DonHang LIKE '%$searchKeyword%' 
                     OR donhang.NguoiNhan LIKE '%$searchKeyword%' 
                     OR donhang.XuLy LIKE '%$searchKeyword%'
                     OR sanpham.TenSanPham LIKE '%$searchKeyword%')
                     GROUP BY donhang.ID_DonHang
                     ORDER BY donhang.ID_DonHang DESC";
  } else {
    $sql_getOrder = "SELECT donhang.*, GROUP_CONCAT(sanpham.TenSanPham SEPARATOR ', ') AS TenSanPham, 
                     GROUP_CONCAT(chitietdonhang.SoLuong SEPARATOR ', ') AS SoLuong 
                     FROM donhang 
                     INNER JOIN chitietdonhang ON donhang.ID_DonHang = chitietdonhang.ID_DonHang 
                     INNER JOIN sanpham ON chitietdonhang.ID_SanPham = sanpham.ID_SanPham 
                     WHERE donhang.ID_ThanhVien = $ID_ThanhVien 
                     GROUP BY donhang.ID_DonHang
                     ORDER BY donhang.ID_DonHang DESC";
  }

  $query_getOrder = mysqli_query($mysqli, $sql_getOrder);
}
?>
 <style>
        /* CSS cho form tìm kiếm */
.search-form {
    width: 300px; /* Kích thước ngắn gọn hơn */
    margin-left: auto; /* Lùi về phía bên phải */
    margin-right: 0; /* Xóa khoảng cách bên phải */
    text-align: right; /* Căn chỉnh nội dung về phía bên phải */
}

.search-form .form-control {
    border-radius: 20px; /* Làm cho các góc của trường nhập liệu tròn hơn */
    padding: 10px; /* Thay đổi padding để form ngắn gọn hơn */
}

.search-form .btn {
    border-radius: 20px; /* Làm cho các góc của nút tròn hơn */
}
    </style>
<div class="container min-height-100">
  <div class="row">
    <div class="col-md-12 mt-3">
      <h2 class="text-center">Danh sách đơn hàng</h2>

      <!-- Form tìm kiếm -->
     <!-- Form tìm kiếm -->
<form method="POST" class="search-form mb-3">
  <div class="input-group">
    <input type="text" name="keyword" class="form-control" placeholder="Nhập từ khóa..." value="<?php echo htmlspecialchars($searchKeyword); ?>">
    <div class="input-group-append">
      <button class="btn btn-primary" type="submit" name="search">Tìm kiếm</button>
    </div>
  </div>
</form>

      <table cellpadding="5px" class="table-bordered w-100 bg-white">
        <thead>
          <tr class="text-center">
            <th scope="col">STT</th>
            <th scope="col">Mã ĐH</th>
            <th scope="col">Người nhận</th>
            <th scope="col">Thời gian đặt</th> 
            <th scope="col">Giá tiền</th> 
            <th scope="col">Trạng thái</th>
            <th scope="col">Tên sản phẩm</th>
            <th scope="col">Số lượng</th> <!-- Cột mới -->
            <th scope="col">Chi tiết</th> 
          </tr>
        </thead>
        <tbody>
          <?php
          if (isset($_SESSION['ID_ThanhVien'])) {
            $i = 0;
            while($row_getOrder = mysqli_fetch_array($query_getOrder)){
              $i++;
              if($row_getOrder['XuLy'] == 0) {$trangThai = "Chưa duyệt"; $style = "text-warning";}
              else if($row_getOrder['XuLy'] == 1) {$trangThai = "Đã duyệt"; $style = "text-warning";}
              else if($row_getOrder['XuLy'] == 3) {$trangThai = "Chờ lấy hàng"; $style = "text-warning";}
              else if($row_getOrder['XuLy'] == 4) {$trangThai = "Đang giao hàng"; $style = "text-warning";}
              else if($row_getOrder['XuLy'] == 5) {$trangThai = "Đơn hàng đã được giao"; $style = "text-success";}
              else if($row_getOrder['XuLy'] == 6) {$trangThai = "Đợi duyệt hoàn trả"; $style = "text-warning";}
              else if($row_getOrder['XuLy'] == 7) {$trangThai = "Đơn hàng đã được hoàn trả"; $style = "text-success";}
              else {$trangThai = "Đã hủy"; $style = "text-danger";}
          ?>
            <tr>
              <td><?php echo $i ?></td>
              <td><?php echo $row_getOrder['ID_DonHang']; ?></td> 
              <td><?php echo $row_getOrder['NguoiNhan']; ?></td> 
              <td><?php echo $row_getOrder['ThoiGianLap']; ?></td> 
              <td><?php echo number_format($row_getOrder['GiaTien'], 0, ',', '.') ?> VND</td>
              <td class="<?php echo $style ?>"><?php echo $trangThai ?></td>
              <td><?php echo $row_getOrder['TenSanPham']; ?></td>
              <td><?php echo $row_getOrder['SoLuong']; ?></td> <!-- Hiển thị số lượng -->
              <td><a href="index.php?navigate=order_detail&id=<?php echo $row_getOrder['ID_DonHang']; ?>">Xem</a></td>
            </tr>
          <?php
            }
          } else {
          ?>
            <tr>
              <td colspan="9" class="text-center">Không có lịch sử đặt hàng</td> <!-- Cập nhật colspan -->
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
