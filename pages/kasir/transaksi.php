<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

if (!$role) {
    header("Location: ../");
    exit();
}
include_once '../../db/DB_connection.php';

// Fungsi untuk mencari produk berdasarkan kata kunci
function cariProduk($keyword) {
    global $conn;
    $query = "SELECT * FROM products WHERE nama_produk LIKE '%$keyword%' OR harga_produk LIKE '%$keyword%' OR kode_unik LIKE '%$keyword%'";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Fungsi untuk menambahkan produk ke dalam transaksi
function tambahkanProduk($id, $jumlah) {
    global $conn;
    $query = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $produk = mysqli_fetch_assoc($result);
    if ($jumlah <= 0) {
        return false;
    } elseif ($jumlah > $produk['jumlah']) {
        return false; 
    } else {
        $produk['jumlah'] = $jumlah;
        return $produk;
    }
}

// Fungsi untuk mengurangi jumlah produk dalam transaksi
function kurangiProduk($index) {
    $struk = isset($_SESSION['struk']) ? $_SESSION['struk'] : [];
    unset($struk[$index]);
    $_SESSION['struk'] = array_values($struk);
}

// Fungsi untuk mengecek apakah produk sudah ada dalam transaksi
function cekProduk($produk, $struk) {
    foreach ($struk as $index => $item) {
        if ($item['id'] == $produk['id']) {
            return $index;
        }
    }
    return -1;
}

// Mendapatkan data transaksi dari session
$struk = isset($_SESSION['struk']) ? $_SESSION['struk'] : [];
$totalHarga = isset($_SESSION['totalHarga']) ? $_SESSION['totalHarga'] : 0;
$error = '';

// Mendapatkan data produk berdasarkan pencarian atau menampilkan semua produk
$rows = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['keyword'])) {
    $keyword = $_POST['keyword'];
    $rows = cariProduk($keyword);
} else {
    $query = "SELECT * FROM products";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}

// Menambahkan produk ke dalam transaksi jika permintaan POST diterima
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && isset($_GET['tambah'])) {
    $id_produk = $_GET['id'];
    $jumlah = isset($_GET['jumlah']) ? $_GET['jumlah'] : 1;
    $produk = tambahkanProduk($id_produk, $jumlah);
    if ($produk === false) {
        $error = 'Jumlah produk tidak valid!';
    } else {
        $index = cekProduk($produk, $struk);
        if ($index != -1) {
            $struk[$index]['jumlah'] += $jumlah;
        } else {
            array_push($struk, $produk);
        }
        // Menghitung total harga setelah menambahkan produk
        $totalHarga = 0;
        foreach ($struk as $item) {
            $totalHarga += $item['harga_produk'] * $item['jumlah'];
        }
        $_SESSION['struk'] = $struk;
        $_SESSION['totalHarga'] = $totalHarga;
    }
}

// Melakukan tindakan ketika tombol "Kurang" diklik untuk mengurangi jumlah produk dalam transaksi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['kurang'])) {
    $index = $_POST['index'];
    kurangiProduk($index);
    header("Location: " . $_SERVER['PHP_SELF']); 
}

// Melakukan tindakan ketika tombol "Cetak" diklik untuk menyimpan transaksi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cetak'])) {
    if (isset($_POST['uang']) && isset($_POST['kembalian'])) {
        $uang = $_POST['uang'];
        $kembalian_transaksi = $_POST['kembalian'];

        // Memasukkan informasi transaksi ke dalam tabel 'transaksi'
        $query_transaksi = "INSERT INTO transaksi (uang_pelanggan, kembalian, total_harga) VALUES ($uang, $kembalian_transaksi, $totalHarga)";
        $result_transaksi = mysqli_query($conn, $query_transaksi);
        if (!$result_transaksi) {
            $error = 'Gagal menyimpan transaksi!';
        } else {
            // Mendapatkan ID transaksi yang baru saja dimasukkan
            $id_transaksi = mysqli_insert_id($conn);

            // Memasukkan informasi produk dalam transaksi ke dalam tabel 'transaksi_produk' dan mengurangi jumlah produk dalam tabel 'products'
            foreach ($struk as $produk) {
                $nama_produk = $produk['nama_produk'];
                $harga_produk = $produk['harga_produk'];
                $jumlah = $produk['jumlah'];
                $kode_unik = $produk['kode_unik'];
                $totalHargaProduk = $harga_produk * $jumlah;
                
                $query_produk = "INSERT INTO transaksi_produk (id_transaksi, nama_produk, harga_produk, jumlah, kode_unik, total_harga) VALUES ($id_transaksi, '$nama_produk', '$harga_produk', $jumlah, '$kode_unik', $totalHargaProduk)";
                $result_produk = mysqli_query($conn, $query_produk);
                if (!$result_produk) {
                    $error = 'Gagal menyimpan informasi produk dalam transaksi!';
                    break; 
                }

                $query_update_produk = "UPDATE products SET jumlah = jumlah - $jumlah WHERE nama_produk = '$nama_produk'";
                $result_update_produk = mysqli_query($conn, $query_update_produk);
                if (!$result_update_produk) {
                    $error = 'Gagal mengurangi jumlah produk!';
                    break; 
                }
            }

            // Mengosongkan session transaksi dan total harga setelah transaksi selesai
            $_SESSION['struk'] = [];
            $_SESSION['totalHarga'] = 0;
            $struk = [];
            $totalHarga = 0;

            // Mengarahkan ke halaman cetak struk dengan ID transaksi
            header("Location: cetak_struk.php?id_transaksi=$id_transaksi");
            exit(); 
        }
    } else {
        $error = "Mohon lengkapi informasi uang dan kembalian.";
    }
}

// Melakukan tindakan ketika tombol "Perbarui Transaksi" diklik untuk mengosongkan transaksi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['refresh'])) {
    $_SESSION['struk'] = [];
    $_SESSION['totalHarga'] = 0;
    $struk = [];
    $totalHarga = 0;
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Kasir</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link " aria-current="page" href="../dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="manage_product.php">Manage</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="../superadmin/data-karyawan.php">Data Karyawan</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link active" href="">Transaksi</a>
                </li>
            </div>
        </div>
        <div>
            <form action="../db/DB_logout.php" method="post">
                <button type="submit" class=" btn btn-danger">Log Out</button>
            </form>
        </div>
    </div>
</nav>
        <div class="content">
        <div class="container">
        <h2 class="mt-5">Transaksi Kasir</h2>
        <h4>Total Harga: Rp. <?php echo number_format($totalHarga, 0, ',', '.'); ?></h4>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Cari produk berdasarkan nama, harga, atau kode unik" name="keyword">
                <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Cari</button>
            </div>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nama Produk</th>
                    <th scope="col">Harga Produk</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Kode Unik</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
    <?php if (!empty($rows)) : ?>
        <?php foreach ($rows as $row) : ?>
            <tr>
                <td><?php echo $row['nama_produk']; ?></td>
                <td>Rp <?php echo number_format($row['harga_produk'], 0, ',', '.'); ?></td>
                <td><?php echo $row['jumlah']; ?></td>
                <td><?php echo $row['kode_unik']; ?></td>
                <td>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="number" name="jumlah" value="1" min="1" class="form-control" style="width: 70px; display: inline-block;">
                        <button type="submit" class="btn btn-sm btn-primary" name="tambah">Tambah</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="5">Tidak ada produk yang ditemukan.</td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
        <?php if (!empty($struk)) : ?>
    <h4>Struk Harga</h4>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Nama Produk</th>
                <th scope="col">Harga Produk</th>
                <th scope="col">Jumlah</th>
                <th scope="col">Kode Unik</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($struk as $index => $produk) : ?>
                <tr>
                    <td><?php echo $produk['nama_produk']; ?></td>
                    <td><?php echo $produk['harga_produk']; ?></td>
                    <td><?php echo $produk['jumlah']; ?></td>
                    <td><?php echo $produk['kode_unik']; ?></td>
                    <td>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" name="kurang" class="btn btn-sm btn-danger">Kurang</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h5>Total Harga: Rp. <?php echo number_format($totalHarga, 0, ',', '.'); ?></h5> <!-- Menampilkan total harga di dalam struk -->
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="mb-3">
            <label for="uang" class="form-label">Uang Diberikan:</label>
            <input type="text" name="uang" id="uang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="kembalian" class="form-label">Kembalian:</label>
            <input type="text" name="kembalian" id="kembalian" class="form-control" readonly>
        </div>
        <button type="submit" name="cetak" class="btn btn-success">Cetak dan Simpan</button>
        <button type="submit" name="refresh" class="btn btn-primary">Perbarui Transaksi</button>
    </form>
<?php endif; ?>

    </div>
    </div>
    </div>
    

    <!-- JavaScript untuk cetak struk -->
    <script>
        function printStruk() {
            window.print();
        }

        // Hitung kembalian saat input uang pelanggan
        document.getElementById('uang').addEventListener('input', function() {
            var uang = parseFloat(this.value);
            var totalHarga = <?php echo $totalHarga; ?>;
            var kembalian = uang - totalHarga;
            document.getElementById('kembalian').value = kembalian >= 0 ? kembalian : 0;
        });
    </script>
</body>
</html>
