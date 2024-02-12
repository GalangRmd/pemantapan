<?php
session_start();
require_once("../db/DB_connection.php");

if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
    header('Location: ../index.php');
    exit();
}

$realName = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='../assets/style/dashboard.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Dashboard</title>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link active" aria-current="page" href="">Dashboard</a>
                <a class="nav-link" href="kasir/manage_product.php">Manage</a>
                <a class="nav-link" href="superadmin/Data-karyawan.php">Data Karyawan</a>
            </div>
        </div>
        <div>
            <form action="../db/DB_logout.php" method="post">
                <button type="submit" class="btn-logout">Log Out</button>
            </form>
        </div>
    </div>
</nav>
</body>
<h1>Hello <?php echo htmlspecialchars($realName); ?>! Welcome  to the dashboard</h1>

</html>