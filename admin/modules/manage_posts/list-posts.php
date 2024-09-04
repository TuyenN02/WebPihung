<?php

// Xử lý phân trang
$page = isset($_GET['trang']) ? (int)$_GET['trang'] : 1;
$begin = ($page - 1) * 8;

// Khởi tạo biến từ khóa tìm kiếm
$tukhoa = '';

// Truy vấn bài viết
$sql_NCC = "SELECT * FROM posts ORDER BY Tenbaiviet DESC LIMIT $begin, 8";

if (isset($_POST['tukhoa'])) {
    $tukhoa = mysqli_real_escape_string($mysqli, trim($_POST['tukhoa']));
    $sql_NCC = "SELECT * FROM posts WHERE Tenbaiviet LIKE '%$tukhoa%' ORDER BY Tenbaiviet DESC LIMIT $begin, 8";
}
$query_NCC = mysqli_query($mysqli, $sql_NCC);

// Kiểm tra số lượng bản ghi trả về
$total_records = mysqli_num_rows($query_NCC);

// Hiển thị thông báo nếu có
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">
                <a style="color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px;" href="?posts=add-post">Thêm mới</a>
            </button>
            <h5 class="m-0" style="text-align: center; flex-grow: 1; font-size: 28px;">Danh sách bài viết</h5>
            <div class="form-search form-inline">
                <form action="" method="POST" class="d-flex">
                    <input type="text" class="form-control form-search" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                    <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                </form>
            </div>
        </div>
        
        <div class="card-body">
            <table class="table table-striped table-checkall">
                <thead>
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">Tên bài viết</th>
                        <th scope="col">Nội dung</th>
                        <th scope="col">Sửa/Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = $begin;
                    while ($row = mysqli_fetch_array($query_NCC)) {
                        $i++;
                    ?>
                        <tr>
                            <td scope="row"><?php echo $i; ?></td>
                            <td><?php echo htmlspecialchars($row['Tenbaiviet']); ?></td>
                            <td>
                                <?php 
                                $noidung = htmlspecialchars($row['Noidung']);
                                $noidung_cut = strlen($noidung) > 50 ? substr($noidung, 0, 30) . '......' : $noidung;
                                echo $noidung_cut;
                                ?>
                            </td>
                            <td class="d-flex">
                                <a href="?posts=edit-posts&id_baiviet=<?php echo $row['ID_baiviet']; ?>" class="btn btn-success btn-sm text-white mr-2" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-sm text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete" onclick="confirmDelete(<?php echo $row['ID_baiviet']; ?>)"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php if ($total_records == 0): ?>
                <p class="text-center">Không tìm thấy bài viết nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa bài viết này?")) {
            window.location.href = 'modules/manage_posts/delete-posts.php?id_baiviet=' + id;
        }
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = 0;
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 2000);
        });
    });
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
    right: 970px;
    padding: 15px;
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
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>