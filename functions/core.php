<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
// Check connection
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>
