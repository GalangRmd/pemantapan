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
    <title>Dashboard</title>
</head>
<body>
    <h1>Hello <?php echo htmlspecialchars($realName); ?>! Welcome  to the dashboard</h1>
    <form action="../db/DB_logout.php" method="post">
        <button type="submit" class="btn-logout">Log Out</button>
    </form>
    <div class="dashboard-content">
        <h2>Dashboard</h2>
        <p>Welcome to the shopiria cashier dashboard. Yaou can manage products and perform other tasks here.</p>
    </div>
    
    <div class="additional-content">
        <h2>Manage Products</h2>
        <p>Would you like to manage products?</p>
        <p><a href="./kasir/manage_product.php" class="text-blue-500 hover:underline">Click here</a></p>
    </div>
</body>
</html>