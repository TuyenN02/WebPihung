<?php 
include("config/connection.php"); // Kết nối đến cơ sở dữ liệu

// Kiểm tra thông báo thành công
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']); // Xóa thông báo sau khi đã hiển thị
}



// Xử lý phân trang
if (isset($_GET['trang'])) {
    $page = $_GET['trang'];
} else {
    $page = 1;
}
$begin = ($page == '' || $page == 1) ? 0 : ($page * 8) - 8;

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
?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div id="success-message" class="alert alert-success" role="alert">
        <?php
        echo $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        ?>
    </div>
<?php endif; ?>

<script>
    // Tự động ẩn thông báo sau 2 giây
    setTimeout(function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 2000); // Đổi thành 2000ms = 2 giây
</script>
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
    if (strlen($noidung) > 50) {
        $noidung_cut = substr($noidung, 0, 30) . '......';
    } else {
        $noidung_cut = $noidung;
    }
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

<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 100px;
}
</style>