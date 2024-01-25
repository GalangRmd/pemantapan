<?php
require_once('./db/DB_connection.php');
require_once('./db/DB_login.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <title>🛒Shopiria | Login</title>
    <link rel='stylesheet' href='./assets/style/login.css'>
    
</head>
<body>
    <div class="container" >
        <img style="width: 100px; margin-bottom: 2rem;" src="./assets/images/logo.png" alt="">
        <form method="POST">
            <?php if(isset($error_massage)) : ?>
                <div class="error-massage"><?php echo $error_massage; ?></div>
                <?php endif; ?>
            <div>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="***********">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="***********">
            </div>
            <div>
                <button type="submit">Sign In</button>
            </div>
            <div class="text-center mt-4">
                <p>Don't Have Account?<a href="./pages/register.php">Register Here</a></p>
            </div>
        </form>
    </div>
    
</body>
</html>