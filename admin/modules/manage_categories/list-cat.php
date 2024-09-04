<?php
include("config/connection.php");

// Truy vấn danh mục sản phẩm
$sql_category_product = "SELECT * FROM danhmuc ORDER BY ID_DanhMuc ASC";

// Xử lý tìm kiếm
if (isset($_POST['tukhoa'])) {
    $tukhoa = $_POST['tukhoa'];
    // Xóa tất cả dấu cách trong từ khóa tìm kiếm
    $tukhoa = str_replace(' ', '', $tukhoa);
    $sql_category_product = "SELECT * FROM danhmuc WHERE REPLACE(TenDanhMuc, ' ', '') LIKE '%" . mysqli_real_escape_string($mysqli, $tukhoa) . "%' ORDER BY ID_DanhMuc ASC";
}

$query_category_product = mysqli_query($mysqli, $sql_category_product);

// Kiểm tra kết quả tìm kiếm có rỗng không
$isEmpty = mysqli_num_rows($query_category_product) == 0;
// Kiểm tra xem có thông báo lỗi hoặc thành công trong session không
if (isset($_SESSION['errors']['database'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['errors']['database'] . '</div>';
    // Xóa thông báo lỗi sau khi hiển thị
    unset($_SESSION['errors']['database']);
}

?>

<div id="content" class="container-fluid">
    <div class="row">
        <!-- Phần danh sách danh mục -->
        <div class="col-12">
            <div class="card">
                  
            <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success" role="alert" id="alert-success">
                            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    
                <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
                <button class="btn btn-primary">
        <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?cat=add-cat">Thêm mới</a>
        </button>
        <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Danh sách danh mục</h5>
                    <div class="form-search form-inline">
                        <form action="" method="POST" class="d-flex">
                            <input type="text" class="form-control form-search" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo isset($tukhoa) ? htmlspecialchars($tukhoa) : ''; ?>">
                            <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                        </form>
                    </div>
                </div>
                <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">STT</th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Tên</th>
                                    <th scope="col">Mô Tả</th>
                                    <th scope="col">Sửa/Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0;
                                while ($row_category_product = mysqli_fetch_array($query_category_product)) {
                                    $i++;
                                ?>
                                    <tr>
                                        <th scope="row"><?php echo $i; ?></th>
                                        <td><?php echo htmlspecialchars($row_category_product['ID_DanhMuc']); ?></td>
                                        <td><?php echo htmlspecialchars($row_category_product['TenDanhMuc']); ?></td>
                                        <td><?php echo htmlspecialchars($row_category_product['Mota']); ?></td>
                                        <td class="d-flex">
                                            <a href="?cat=edit-cat&id=<?php echo htmlspecialchars($row_category_product['ID_DanhMuc']); ?>" class="btn btn-success btn-sm text-white mr-2" type="button" data-toggle="tooltip" data-placement="top" title="Sửa"><i class="fa fa-edit"></i></a>
                                            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo htmlspecialchars($row_category_product['ID_DanhMuc']); ?>);" class="btn btn-danger btn-sm text-white" type="button" data-toggle="tooltip" data-placement="top" title="Xóa"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>     
                            </tbody>
                        </table>
                        
                    <?php if ($isEmpty): ?>
                        <p class="text-center">Không tìm thấy danh mục nào!</p>
             
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa danh mục này?")) {
            window.location.href = 'modules/manage_categories/delete-cat.php?id=' + id;
        }
    }

    // Hàm để ẩn thông báo sau 3 giây
    function hideAlerts() {
        const alertErrors = document.querySelectorAll('.alert-danger');
        const alertSuccess = document.querySelectorAll('.alert-success');
        
        alertErrors.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = 0;
                setTimeout(() => alert.style.display = 'none', 500); // 0.5 giây cho hiệu ứng chuyển tiếp
            }, 3000); // 3 giây
        });
        
        alertSuccess.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = 0;
                setTimeout(() => alert.style.display = 'none', 500); // 0.5 giây cho hiệu ứng chuyển tiếp
            }, 3000); // 3 giây
        });
    }


    // Gọi hàm khi trang tải
    document.addEventListener('DOMContentLoaded', hideAlerts);
</script>
<style>

#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 100px;
}

.alert {
    position: fixed;
    top: 50px;
    right: 950px;
    padding: 10px;
    border-radius: 5px;
    z-index: 9999;
    opacity: 1;
    transition: opacity 0.5s ease-out;
}

.alert-success {
    background-color: #d4edda;
    color: #ff0000;
    border: 3px solid #ff0000;
}

.alert-danger {
    background-color: #f8d7da;
    color: #ff0000;
    border: 3px solid #ff0000;

}
</style>