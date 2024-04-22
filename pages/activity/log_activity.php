<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;



// Koneksi ke database
include '../../db/db_connection.php';

// Inisialisasi variabel untuk sorting
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$sort_icon = ($sort_order == 'DESC') ? '▼' : '▲';

// Variabel untuk rentang tanggal yang dipilih
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Query untuk mengambil data transaksi dengan sorting berdasarkan tanggal transaksi
$query = "SELECT transaksi.id, transaksi.tanggal_pembuatan, GROUP_CONCAT(transaksi_produk.nama_produk) AS nama_barang, transaksi.total_harga, transaksi.uang_pelanggan, transaksi.kembalian 
          FROM transaksi 
          JOIN transaksi_produk ON transaksi.id = transaksi_produk.id_transaksi";

// Filter berdasarkan rentang tanggal yang dipilih
if (!empty($start_date) && !empty($end_date)) {
    $query .= " WHERE transaksi.tanggal_pembuatan BETWEEN '$start_date' AND '$end_date'";
}

$query .= " GROUP BY transaksi.id
            ORDER BY transaksi.tanggal_pembuatan $sort_order"; // Sorting berdasarkan tanggal pembuatan

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/logo.png.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Tamias | Activity</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #28343b;
            color: #fff;
        }
        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }
        .table .thead-dark th {
            color: #fff;
            background-color: #343a40;
            border-color: #454d55;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }
        .btn {
            display: inline-block;
            font-weight: 400;
            color: #212529;
            text-align: center;
            vertical-align: middle;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            color: #fff;
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            color: #fff;
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                    <a class="nav-link " aria-current="page" href="../dashboard.php">Dashboard</a>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="../kasir/manage_product.php">Manage</a>
                </li>
                <li class="nav-item <?php echo ($role !== 'kasir') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="../superadmin/data-karyawan.php">Data Karyawan</a>
                </li>
                <li class="nav-item <?php echo ($role !== 'owner') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="../kasir/transaksi.php">Transaksi</a>
                </li>
                <li class="nav-item <?php echo ($role !== 'kasir') ? '' : 'd-none'; ?>">
                    <a class="nav-link  active" href="../activity/log_activity.php">Activity</a>
                </li>
            </div>
        </div>
        <div>
            <form action="../../db/DB_logout.php" method="post">
                <button type="submit" class=" btn btn-danger">Log Out</button>
            </form>
        </div>
    </div>
</nav>

<div class="container">
    <!-- Konten dari halaman log activity -->
    <div class="content">
        <h2 class="mt-5">Log Aktivitas Transaksi</h2>
        <div class="text-end mb-3">
            <a href="?order=<?php echo ($sort_order == 'DESC') ? 'ASC' : 'DESC'; ?>" class="btn btn-secondary">
                Urutkan Tanggal <?php echo $sort_icon; ?>
            </a>
            <a href="cetak_log_activity.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-primary">
                Cetak Log Aktivitas
            </a>
        </div>
        <form method="GET">
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Tanggal Transaksi</th>
                    <th scope="col">Nama Barang</th>
                    <th scope="col">Total Harga</th>
                    <th scope="col">Uang Pelanggan</th>
                    <th scope="col">Kembalian</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['tanggal_pembuatan']; ?></td>
                        <td><?php echo $row['nama_barang']; ?></td>
                        <td><?php echo $row['total_harga']; ?></td>
                        <td><?php echo $row['uang_pelanggan']; ?></td>
                        <td><?php echo $row['kembalian']; ?></td>
                        <td>
                            <form action="hapus_log.php" method="post">
                                <input type="hidden" name="log_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Hapus Log</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


</body>
</html>


<?php
// Tutup koneksi database
mysqli_close($conn);
?>
