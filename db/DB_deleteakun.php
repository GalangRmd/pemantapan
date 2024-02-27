<?php
session_start();
require_once('DB_connection.php');

if(isset($_POST['delete_product']) && isset($_POST['id'])){
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt-> execute();

    if($stmt->affected_row > 0){
        echo "Account delete succesfully";
    }else {
        echo "Failed to delete Account";
    }

    $stmt ->close();
    $conn -> close();

    header ('location: ../pages/superadmin/data-karyawan.php');
    exit();
}
?>