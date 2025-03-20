<?php
$koneksi = mysqli_connect("localhost", "root", "", "ukk2025_todolist");
if (isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    if (!empty($task) && !empty($description) && !empty($priority) && !empty($due_date)) {
        mysqli_query($koneksi, "INSERT INTO task VALUES('','$task','$description','$priority','$due_date','0')");
        echo "<script>alert('Task berhasil ditambahkan')</script>";
    } else {
        echo "<script>alert('Task gagal ditambahkan')</script>";
        header("location: InputTask.php");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css"rel="stylesheet" >
</head>
<body>
    <div class="container">
        <h2>To Do List</h2>
        <div class="nav-buttons">
            <a href="InputTask.php" class="btn btn-success btn-custom">Input Task</a>
            <a href="TampilTask.php" class="btn btn-primary btn-custom">Tampil Task</a>
        </div>
        <form action="" method="post" class="border rounded p-3 bg-light">
            <label class="form-label">Nama Task</label>
            <input type="text" name="task" class="form-control" placeholder="Masukkan Task Baru" autocomplete="off" required>
            <label class="form-label mt-2">Deskripsi</label>
            <input type="text" name="description" class="form-control" placeholder="Masukkan Deskripsi" autocomplete="off" required>
            <label class="form-label mt-2">Prioritas</label>
            <select name="priority" class="form-select" required>
                <option value="">-- Pilih Prioritas --</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>
            <label class="form-label mt-2">Tanggal</label>
            <input type="date" name="due_date" class="form-control"
                value="<?php echo date('Y-m-d'); ?>"
                min="<?php echo date('Y-m-d'); ?>" required>
            <button class="btn btn-primary w-100 mt-3" name="add_task">Tambah Task</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>