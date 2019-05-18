<?php
// Initialize connection to MySQL database
$servername = "127.0.0.1";
$username = "myuser";
$password = "1234";
$dbname = "stored_data";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";
?> 