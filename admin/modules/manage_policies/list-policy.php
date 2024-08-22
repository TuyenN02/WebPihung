<?php
// Hiển thị thông báo nếu có
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    echo "<div class='alert alert-$message_type' id='message-alert'>$message</div>";
    // Xóa thông báo khỏi session
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Khởi tạo biến từ khóa tìm kiếm
$tukhoa = '';

// Kiểm tra xem có từ khóa tìm kiếm nào được gửi không
if (isset($_POST['tukhoa'])) {
    $tukhoa = mysqli_real_escape_string($mysqli, trim($_POST['tukhoa']));
}

// Truy vấn lấy danh sách chính sách
$sql_Policy = "SELECT * FROM chinhsach";
if ($tukhoa !== '') {
    $sql_Policy .= " WHERE TieuDe LIKE '%$tukhoa%'";
}

// Kiểm tra xem có trang nào được gửi không
if (isset($_GET['trang'])) {
    $page = $_GET['trang'];
} else {
    $page = 1;
}

$begin = ($page - 1) * 8;
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);
// Thêm phân trang vào truy vấn
$sql_Policy .= " ORDER BY ID_ChinhSach DESC LIMIT $begin, 8";
$query_Policy = mysqli_query($mysqli, $sql_Policy);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold d-flex justify-content-between align-items-center">
            <h5 class="m-0">Danh sách chính sách</h5>
            <div class="form-search">
                <form action="" method="POST" class="d-flex">
                    <input type="text" class="form-control" placeholder="Nhập từ khóa..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                    <input type="submit" name="btn-search" value="Tìm kiếm" class="btn btn-primary ml-2">
                </form>
            </div>
        </div>
        <div class="card-body">
            <?php if ($successMessage): ?>
                <div class="alert alert-success" id="success-message">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($query_Policy) > 0): ?>
                <table class="table table-striped table-checkall">
                    <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Tiêu đề</th>
                            <th scope="col">Nội dung</th>
                            <th scope="col">Sửa/Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = $begin;
                        while ($row_Policy = mysqli_fetch_array($query_Policy)) {
                            $i++;
                            $short_content = strlen($row_Policy['NoiDung']) > 100 ? substr($row_Policy['NoiDung'], 0, 100) . '...' : $row_Policy['NoiDung'];
                        ?>
                        <tr>
                            <th scope="row"><?php echo $i ?></th>
                            <td><?php echo htmlspecialchars($row_Policy['TieuDe']) ?></td>
                            <td>
                                <span class="short-content"><?php echo htmlspecialchars($short_content) ?></span>
                                <?php if (strlen($row_Policy['NoiDung']) > 100): ?>
                                <a href="index.php?policy=detail-policy&id=<?php echo $row_Policy['ID_ChinhSach'] ?>" class="btn btn-link btn-sm">Xem thêm</a>
                                <?php endif; ?>
                            </td>
                            <td class="d-flex justify-content-center">
                                <a href="?policy=edit-policy&id=<?php echo $row_Policy['ID_ChinhSach'] ?>" class="btn btn-success btn-sm rounded text-white mr-2" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row_Policy['ID_ChinhSach'] ?>);" class="btn btn-danger btn-sm rounded text-white" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Phân trang -->
                <?php
                if (!empty($tukhoa)) {
                    $sql_trang = "SELECT COUNT(*) AS total FROM chinhsach WHERE TieuDe LIKE '%$tukhoa%'";
                } else {
                    $sql_trang = "SELECT COUNT(*) AS total FROM chinhsach";
                }
                $result_trang = mysqli_query($mysqli, $sql_trang);
                $row_trang = mysqli_fetch_array($result_trang);
                $row_count = $row_trang['total'];
                $trang = ceil($row_count / 8);
                ?>
                <ul class="d-flex justify-content-center list-unstyled mt-3">
                    <?php for ($i = 1; $i <= $trang; $i++): ?>
                        <li class="p-2 m-1 bg-white <?php echo ($i == $page) ? 'active' : ''; ?>" style="cursor:pointer;">
                            <a class="text-dark" href="index.php?policy=list-policy&trang=<?php echo $i ?>&tukhoa=<?php echo urlencode($tukhoa) ?>"><?php echo $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>

            <?php else: ?>
                <div class="text-center" style="background-color: white; color: black;">
                    Không tìm thấy chính sách nào
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm("Bạn có chắc chắn muốn xóa chính sách này?")) {
            window.location.href = 'modules/manage_policies/delete-policy.php?id=' + id;
        }
    }

    // Ẩn thông báo thành công sau 3 giây
    setTimeout(function() {
        var alert = document.getElementById('message-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 3000);
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(function() {
                successMessage.style.opacity = 0;
                setTimeout(function() {
                    successMessage.style.display = 'none';
                }, 300);
            }, 3000);
        }
    });
</script>
