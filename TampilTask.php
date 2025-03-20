<?php
$koneksi = mysqli_connect("localhost", "root", "", "ukk2025_todolist");
$limit = 5; 
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman - 1) * $limit;
$total_data_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM task");
$total_data_row = mysqli_fetch_assoc($total_data_query);
$total_data = $total_data_row['total'];
$total_halaman = ceil($total_data / $limit);

if (isset($_POST['edit_task'])) {
    $id = $_POST['id'];
    $task = $_POST['task'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    if (!empty($task) && !empty($description) && !empty($priority) && !empty($due_date)) {
        mysqli_query($koneksi, "UPDATE task SET task='$task', description='$description', priority='$priority', due_date='$due_date' WHERE id='$id'");
        echo "<script>alert('Task berhasil diperbarui');
        window.location='TampilTask.php';
        </script>";
    } else {
        echo "<script>alert('Gagal memperbarui task, pastikan semua kolom terisi.');</script>";
    }
}

if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    mysqli_query($koneksi, "UPDATE task SET status = '1' WHERE id = '$id'");
    echo "<script>alert('Task berhasil diselesaikan'); 
    window.location='TampilTask.php';
    </script>";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM task WHERE id = '$id'");
    echo "<script>alert('Task berhasil dihapus');
    window.location='TampilTask.php';
    </script>";
}

if (isset($_GET['undo'])) {
    $id = $_GET['undo'];
    mysqli_query($koneksi, "UPDATE task SET status = '0' WHERE id = '$id'");
    echo "<script>alert('Task dikembalikan ke status belum selesai'); 
    window.location='TampilTask.php';
    </script>";
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$priority_filter = isset($_GET['priority']) ? $_GET['priority'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$query = "SELECT * FROM task WHERE 1";

if (!empty($search)) {
    $query .= " AND (task LIKE '%$search%' OR description LIKE '%$search%')";
}
if (!empty($priority_filter)) {
    $query .= " AND priority = '$priority_filter'";
}
if ($status_filter !== '') {
    $query .= " AND status = '$status_filter'";
}

$query .= " ORDER BY status ASC, priority DESC, due_date ASC LIMIT $mulai, $limit";
$result = mysqli_query($koneksi, $query);
$total_data_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM task WHERE 1");

if (!empty($search)) {
    $total_data_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM task WHERE task LIKE '%$search%' OR description LIKE '%$search%'");
}
if (!empty($priority_filter)) {
    $total_data_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM task WHERE priority = '$priority_filter'");
}
if ($status_filter !== '') {
    $total_data_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM task WHERE status = '$status_filter'");
}

$total_data_row = mysqli_fetch_assoc($total_data_query);
$total_data = $total_data_row['total'];
$total_halaman = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>To Do List</h2>
        <div class="nav-buttons">
            <a href="InputTask.php" class="btn btn-primary btn-custom">Input Task</a>
            <a href="TampilTask.php" class="btn btn-success btn-custom">Tampil Task</a>
        </div>
        <form method="GET" class="mt-3 d-flex gap-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari tugas..." value="<?php echo $search; ?>">
                <button type="submit" class=" btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <button type="button" class="btn btn-secondary" onclick="toggleFilter()"><i class="bi bi-funnel"></i></button>
        </form>
        <div id="filterBox" class="mt-3 p-3 border rounded d-none" style="background-color: #f8f9fa;">
            <form method="GET">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Prioritas</label>
                        <select name="priority" class="form-select">
                            <option value="">Semua</option>
                            <option value="1" <?php echo ($priority_filter == "1") ? "selected" : ""; ?>>Low</option>
                            <option value="2" <?php echo ($priority_filter == "2") ? "selected" : ""; ?>>Medium</option>
                            <option value="3" <?php echo ($priority_filter == "3") ? "selected" : ""; ?>>High</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="0" <?php echo ($status_filter == "0") ? "selected" : ""; ?>>Belum Selesai</option>
                            <option value="1" <?php echo ($status_filter == "1") ? "selected" : ""; ?>>Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Terapkan</button>
                    <a href="TampilTask.php" class="btn btn-danger">Reset</a>
                </div>
            </form>
        </div>
        <table class="table table-bordered text-center mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Task</th>
                    <th>Deskripsi</th>
                    <th>Prioritas</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $no = $mulai + 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo $row['task']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td>
                                <?php
                                if ($row['priority'] == 1) {
                                    echo "Low";
                                } elseif ($row['priority'] == 2) {
                                    echo "Medium";
                                } else {
                                    echo "High";
                                }
                                ?>
                            </td>
                            <td><?php echo $row['due_date'] ?></td>
                            <td>
                                <?php
                                if ($row['status'] == 0) {
                                    echo "<span class='status-badge status-pending'>Belum Selesai</span>";
                                } else {
                                    echo "<span class='status-badge status-complete'>Selesai</span>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 0) { ?>
                                    <a href="?complete=<?php echo $row['id'] ?>" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle"></i>
                                    </a>
                                <?php } else { ?>
                                    <a href="?undo=<?php echo $row['id'] ?>" class="btn btn-undo btn-sm">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                <?php } ?>

                                <a href="?delete=<?php echo $row['id'] ?>" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <a href="#editModal<?php echo $row['id']; ?>" data-bs-toggle="modal" class="btn btn-edit btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Task</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" method="post">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <label class="form-label">Nama Task</label>
                                            <input type="text" name="task" class="form-control" value="<?php echo $row['task']; ?>" required>
                                            <label class="form-label mt-2">Deskripsi</label>
                                            <input type="text" name="description" class="form-control" value="<?php echo $row['description']; ?>" required>
                                            <label class="form-label mt-2">Prioritas</label>
                                            <select name="priority" class="form-select" required>
                                                <option value="1" <?php echo ($row['priority'] == 1) ? 'selected' : ''; ?>>Low</option>
                                                <option value="2" <?php echo ($row['priority'] == 2) ? 'selected' : ''; ?>>Medium</option>
                                                <option value="3" <?php echo ($row['priority'] == 3) ? 'selected' : ''; ?>>High</option>
                                            </select>
                                            <label class="form-label mt-2">Tanggal</label>
                                            <input type="date" name="due_date" class="form-control" value="<?php echo $row['due_date']; ?>" min="<?php echo date('Y-m-d'); ?>" disabled>
                                            <button class="btn btn-primary w-100 mt-3" name="edit_task">Simpan Perubahan</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    <?php }
                }
                    ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                if ($halaman > 1) { ?>
                    <li class="page-item">
                        <a class="page-link" href="?halaman=<?php echo $halaman - 1; ?>&search=<?php echo $search; ?>&priority=<?php echo $priority_filter; ?>&due_date=<?php echo $date_filter; ?>&status=<?php echo $status_filter; ?>">
                            Previous
                        </a>
                    </li>
                <?php } ?>
                <?php
                for ($i = 1; $i <= $total_halaman; $i++) { ?>
                    <li class="page-item <?php echo ($halaman == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?halaman=<?php echo $i; ?>&search=<?php echo $search; ?>&priority=<?php echo $priority_filter; ?>&due_date=<?php echo $date_filter; ?>&status=<?php echo $status_filter; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php } ?>
                <?php
                if ($halaman < $total_halaman) { ?>
                    <li class="page-item">
                        <a class="page-link" href="?halaman=<?php echo $halaman + 1; ?>&search=<?php echo $search; ?>&priority=<?php echo $priority_filter; ?>&due_date=<?php echo $date_filter; ?>&status=<?php echo $status_filter; ?>">
                            Next
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
    <script>
        function toggleFilter() {
            var filterBox = document.getElementById("filterBox");
            if (filterBox.classList.contains("d-none")) {
                filterBox.classList.remove("d-none");
            } else {
                filterBox.classList.add("d-none");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>