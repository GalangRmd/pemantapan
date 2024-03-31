<?php
session_start();
require_once('../../db/DB_connection.php');
include "../../confi/app.php";
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /index.php');
    exit;
}



if(isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

}

$query = "SELECT * FROM products";
$result = $conn->query($query);
$no = 1;

// Edit barang
if (isset($_POST['update_product'])){
    $id = $_POST['id'];
    $nama_produk = $_POST['nama_produk'];
    $harga_produk = $_POST['harga_produk'];
    $jumlah = $_POST['jumlah'];

    $stmt = $conn->prepare("UPDATE products SET nama_produk = ?, harga_produk = ?, jumlah = ? WHERE id = ?");
    $stmt->bind_param("siii", $nama_produk, $harga_produk, $jumlah, $id);
    
    if ($stmt->execute()){
        if($stmt->affected_rows > 0) {
            echo "Product updated successfully";
        } else {
            echo "No changes made to the product";
        }
    } else {
        echo "Failed to update product.";
    }
    $stmt->close();
    $conn->close();
    header('location: manage_product.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='../../Assets/style/manage_product.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>🛒Tamias | Manage</title>
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
                    <a class="nav-link active" href="">Manage</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="../superadmin/data-karyawan.php">Data Karyawan</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="transaksi.php">Transaksi</a>
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
    
    <h1 align="center" class="sss mt-4"><b>Manage Product</b></h1>
    
    <center>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add" style="width: 120px; margin-top:25px; box-shadow: 1px 1px 1px grey; ">
            Add Product
        </button>
    </center>
    
    <table>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Quantity</th>
            <th>Terakhir Di Update</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $no++?></td>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                <td>Rp. <?php echo number_format($row["harga_produk"]); ?></td>
                <td><?php echo number_format($row['jumlah']); ?> pcs</td>
                <td><?php echo date('d F Y H:i:s', strtotime($row['updated_at'])); ?></td>
                <td class="action-buttons">
                    <form action="" method="get">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $row['id']; ?>" style="width: 120px; margin-top:25px; box-shadow: 1px 1px 1px grey; ">
                            Edit
                        </button>
                    </form>
                    <form action="../../db/DB_delete_product.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-danger" name="delete_product" style="width: 120px; margin-top:25px; box-shadow: 1px 1px 1px grey; " onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                    </form>
                    <form action="../../db/DB_process_checkout.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-primary" name="checkout_product" style="width: 120px; margin-top:25px; box-shadow: 1px 1px 1px grey; " onclick="return confirm('Are you sure you want to Buy this product?')">Checkout</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

<!-- Modal Add -->
<div class="modal fade" id="add" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Product</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                        <button type="submit" class="btn btn-primary" align="center" name="add_product" style="margin-top:15px;">Add Product</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update -->
<?php
$result = $conn->query($query);
while ($row = $result->fetch_assoc()):
?>
<div class="modal fade" id="updateModal<?php echo $row['id']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Product</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-container">
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <label for="nama_produk">Product Name:</label>
                        <input type="text" name="nama_produk" value="<?php echo htmlspecialchars($row['nama_produk']); ?>" required>
                        <br>
                        <label for="harga_produk">Product Price:</label>
                        <input type="number" name="harga_produk" value="<?php echo htmlspecialchars($row['harga_produk']); ?>" required>
                        <br>
                        <label for="jumlah">Quantity:</label>
                        <input type="number" name="jumlah" value="<?php echo htmlspecialchars($row['jumlah']); ?>" required>
                        <br>
                        <button type="submit" class="btn btn-primary" name="update_product">Update Product</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>

