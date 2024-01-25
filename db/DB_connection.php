<?php 
$host = "localhost";
$username = "root";
$pass = "";
$dbname= "db_cashier";

$conn=new mysqli($host, $username, $pass, $dbname);

if($conn->connect_error){
    die("Conection Failed".$conn->connect_error);
}

// echo "Conncection Succesfull";

?>