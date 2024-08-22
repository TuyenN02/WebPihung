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
?>

<div id="content" class="container-fluid">
    <div class="row">
        <!-- Phần danh sách danh mục -->
        <div class="col-12">
            <div class="card">
                <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Danh sách danh mục</h5>
                    <div class="form-search form-inline">
                        <form action="" method="POST" class="d-flex">
                            <input type="text" class="form-control form-search" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo isset($tukhoa) ? htmlspecialchars($tukhoa) : ''; ?>">
                            <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                        </form>
                    </div>
                </div>
                <div class="card-body">
                  
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success" role="alert" id="alert-success">
                            <p><?php echo htmlspecialchars($_SESSION['success']); ?></p>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($isEmpty): ?>
                        <p class="text-center">Không tìm thấy danh mục nào!</p>
                    <?php else: ?>
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
        const alertErrors = document.getElementById('alert-errors');
        const alertSuccess = document.getElementById('alert-success');
        
        if (alertErrors) {
            setTimeout(() => {
                alertErrors.style.display = 'none';
            }, 3000); // 3 giây
        }
        
        if (alertSuccess) {
            setTimeout(() => {
                alertSuccess.style.display = 'none';
            }, 3000); // 3 giây
        }
    }

    // Gọi hàm khi trang tải
    document.addEventListener('DOMContentLoaded', hideAlerts);
</script>
