<?php
session_start();
require_one('DB_connection.php');


if(isset($_POST['add_product'])){
    $nama_produk = $_POST['nama_produk'];
    $harga_produl = $_POST['harga_produk'];
    $jumlah = $post['jumlah'];

    $kode_unik = bin2hex(random_bytes(5));
    $stmt = $conn->prepare("INSERT INTO products (nama_produk, harga_produk, jumlah, kode_unik) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siis', $nama_produk, $harga_produk, $jumlah, $kode_unik);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        echo "Product added succesfully";
    }else{
        echo "Failed to add product. error".$stmt->error;

    }
    $stmt->close();
    $conn->close();
    header('Location: ../pages/kasir/manage_product.php');
    exit;
}
?>