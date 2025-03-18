<?php
$koneksi = mysqli_connect("localhost", "root", "", "ukk2025_todolist");
if (isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    if (!empty($task) && !empty($priority) && !empty($due_date)) {
        mysqli_query($koneksi, "INSERT INTO task VALUES('','$task','$priority','$due_date','0')");
        echo "<script>alert('Task berhasil ditambahkan')</script>";
    } else {
        echo "<script>alert('Task gagal ditambahkan')</script>";
        header("location: index.php");
    }
}

if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    mysqli_query($koneksi, "UPDATE task SET status = '1' WHERE id = '$id'");
    echo "<script>alert('Task berhasil diselesaikan'); 
    window.location='index.php';
    </script>";
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM task WHERE id = '$id'");
    echo "<script>alert('Task berhasil dihapus');
    window.location='index.php';
    </script>";
}

$result = mysqli_query($koneksi, "SELECT * FROM task ORDER BY status ASC, priority DESC, due_date ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
            padding: 25px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0px 5px 12px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            font-weight: 600;
            color: #4a4a4a;
            margin-bottom: 25px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 12px;
            background-color: #f9fafb;
            font-size: 16px;
        }

        .btn-primary {
            background: #6C5DD3;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 12px;
            transition: 0.3s;
            font-size: 16px;
            color: white;
        }

        .btn-primary:hover {
            background: #5746af;
        }

        .table {
            margin-top: 25px;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
            font-size: 16px;
        }

        .table thead {
            background: #e3eaf3;
            font-size: 17px;
        }

        .table th,
        .table td {
            padding: 15px;
        }

        .btn-success {
            background: #4CAF50;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            padding: 7px 12px;
            color: white;
        }

        .btn-success:hover {
            background: #388E3C;
        }

        .btn-danger {
            background: #E57373;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            padding: 7px 12px;
            color: white;
        }

        .btn-danger:hover {
            background: #D32F2F;
        }

        .status-badge {
            font-size: 14px;
            padding: 7px 10px;
            border-radius: 6px;
            font-weight: 500;
        }

        .status-pending {
            background: #FFEBEE;
            color: #C62828;
        }

        .status-complete {
            background: #E8F5E9;
            color: #2E7D32;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>To Do List</h2>
        <form action="" method="post" class="border rounded p-3 bg-light">
            <label class="form-label">Nama Task</label>
            <input type="text" name="task" class="form-control" placeholder="Masukkan Task Baru" autocomplete="off" required>
            <label class="form-label mt-2">Prioritas</label>
            <select name="priority" class="form-select" required>
                <option value="">-- Pilih Prioritas --</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>
            <label class="form-label mt-2">Tanggal</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-d') ?>" required>
            <button class="btn btn-primary w-100 mt-3" name="add_task">Tambah Task</button>
        </form>

        <table class="table table-bordered text-center mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Task</th>
                    <th>Prioritas</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo $row['task']; ?></td>
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
                                    <a href="?complete=<?php echo $row['id'] ?>" class="btn btn-success btn-sm">✔ Selesai</a>
                                <?php } ?>
                                <a href="?delete=<?php echo $row['id'] ?>" class="btn btn-danger btn-sm">🗑 Hapus</a>
                            </td>
                        </tr>
                <?php }
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>