<?php
require_once('../db/DB_connection.php');
require_once('../db/DB_register.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛒Shopiria | Login</title>
</head>
<body>
    <div class="container">
    <img style="width:100px; margin-bottom: 2rem;" src="../assets/images/logo.png" alt="shop">
        <form method="POST">
            <?php if(isset($error_message)) : ?>
                <div class="error_message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <div>
                <label for="username">Username*</label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="****************" required>
            </div>
            <div>
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" placeholder="Your Full Name" required>
            </div>
            <div>
                <button type="submit">Register</button>
            </div>
            <p>Have an account? <a href="../index.php">Login!</a></p>
        </form>
    </div>
</body>
</html>