<?php
session_start();
require_once('../../db/DB_connection.php');

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /index.php');
    exit;
}

$query = "SELECT * FROM products";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='../../Assets/style/manage_product.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Shop</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link " aria-current="page" href="../dashboard.php">Dashboard</a>
                    <a class="nav-link active" href="">Manage</a>
                    <a class="nav-link " href="../superadmin/Data-karyawan.php">Data Karyawan</a>
                </div>
            </div>
            <div>
                <form action="../../db/DB_logout.php" method="post">
                    <button type="submit" class="btn-logout">Log Out</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="header">
        <h1>Hello, <?php echo htmlspecialchars($_SESSION['nama']); ?>! Welcome to product management!</h1>
    </div>
    <div class="form-container">
        <form class="body" action="../../db/DB_add_product.php" method="post">
            <label for="nama_produk">Product Name:</label>
            <input type="text" name="nama_produk" required>
            <br>
            <label for="harga_produk">Product Price:</label>
            <input type="number" name="harga_produk" required>
            <br>
            <label for="jumlah">Quantity:</label>
            <input type="number" name="jumlah" required>
            <br>
            <button type="submit" name="add_product" style="margin-top:15px;">Add Product</button>
        </form>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Quantity</th>
            <th>Terakhir Di Update</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                <td>Rp. <?php echo number_format($row["harga_produk"]); ?></td>
                <td><?php echo number_format($row['jumlah']); ?> pcs</td>
                <td><?php echo date('d F Y H:i:s', strtotime($row['updated_at'])); ?></td>
                <td class="action-buttons">
                    <form action="update_product.php" method="get">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button class="update-button" type="submit">Update</button>
                    </form>
                    <form action="../../db/DB_delete_product.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="delete-button" name="delete_product" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                    </form>
                    <form action="../../db/DB_process_checkout.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="checkout-button" name="checkout_product" onclick="return confirm('Are you sure you want to Buy this product?')">Checkout</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-XX1t1zVtizEJtrIzgkgT/iVXntHLD/j2Z9t8x8iqUpuTUQoPkqBpWrn3X2IU9yBn" crossorigin="anonymous"></script>
</html>