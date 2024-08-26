<?php

// Xử lý tìm kiếm
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $search = mysqli_real_escape_string($mysqli, $search);
    $search = preg_replace('/\s+/', ' ', $search); // Xóa dấu cách thừa
}

// Truy vấn dữ liệu
$sql_comment = "
    SELECT binhluan.*, sanpham.TenSanPham, thanhvien.HoVaTen 
    FROM binhluan 
    JOIN sanpham ON binhluan.ID_SanPham = sanpham.ID_SanPham 
    JOIN thanhvien ON binhluan.ID_ThanhVien = thanhvien.ID_ThanhVien 
    WHERE thanhvien.HoVaTen LIKE '%$search%' 
    OR sanpham.TenSanPham LIKE '%$search%'
    ORDER BY binhluan.ThoiGianBinhLuan DESC
";
$query_comment = mysqli_query($mysqli, $sql_comment);

// Kiểm tra số lượng bản ghi
$num_rows = mysqli_num_rows($query_comment);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Danh sách bình luận</h5>
            <form action="" method="GET" class="form-inline">
                <input type="hidden" name="comment" value="comments">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Nhập từ khóa" 
                    value="<?= htmlspecialchars($search); ?>"
                >
                <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
            </form>
        </div>
        <div class="card-body">
            <?php
            // Hiển thị thông báo thành công
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success" role="alert">';
                echo htmlspecialchars($_SESSION['success_message']);
                echo '</div>';
                unset($_SESSION['success_message']); // Xóa thông báo sau khi hiển thị
            }

            // Hiển thị thông báo lỗi
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger" role="alert">';
                echo htmlspecialchars($_SESSION['error_message']);
                echo '</div>';
                unset($_SESSION['error_message']); // Xóa thông báo sau khi hiển thị
            }
            ?>

            <table class="table table-striped table-checkall">
                <thead>
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">Tên khách hàng</th>
                        <th scope="col">Tên sản phẩm</th>
                        <th scope="col">Nội dung</th>
                        <th scope="col">Thời gian</th>
                        <th scope="col">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num_rows > 0) { 
                        $i = 0;
                        while ($row_comment = mysqli_fetch_array($query_comment)) {
                            $i++;
                    ?>
                    <tr>
                        <th scope="row"><?php echo $i; ?></th>
                        <td><?php echo htmlspecialchars($row_comment['HoVaTen']); ?></td>
                        <td><?php echo htmlspecialchars($row_comment['TenSanPham']); ?></td>
                        <td><?php echo htmlspecialchars($row_comment['NoiDung']); ?></td>
                        <td><?php echo htmlspecialchars($row_comment['ThoiGianBinhLuan']); ?></td>
                        <td>
                            <a href="modules/manage_comment/delete-comment.php?id=<?php echo $row_comment['ID_BinhLuan']; ?>" 
                               class="btn btn-danger btn-sm rounded text-white" 
                               type="button" 
                               data-toggle="tooltip" 
                               data-placement="top" 
                               title="Delete"
                               onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } 
                    } else { ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            Không tìm thấy bình luận nào!
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 60px;
}
</style>