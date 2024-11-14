<?php
$servername = "localhost";
$username = "callingsys";
$password = "Xann7ADX8nCytZyh";
$dbname = "callingsys";
 
// Create connection
$conn = new mysqli($servername, $username, $password, $database);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>