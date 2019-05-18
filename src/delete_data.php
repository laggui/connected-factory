<?php
include('db.php');

$delete_data_sql = "DELETE FROM test;";

if(isset($_POST['password'])) {
	$password = $_POST['password'];
	if($password == "123") {
		if (mysqli_query($conn, $delete_data_sql)) {
			echo "Donn&eacute supprim&eacutees";
			header('Location: index.php');
		} else {
			echo "Erreur lors de la suppression";
			header('Location: index.php');
		}
	}
	else {header('Location: index.php');}
}
else if (isset($_GET['device_id'])) {
	echo "Suppression en cours...";
	$device_id = $_GET['device_id'];
	$delete_device_data_sql = "DELETE FROM device_list WHERE id IN ('$device_id');";
	if (mysqli_query($conn, $delete_device_data_sql)) {
			echo "Donn&eacutees supprim&eacutees";
			header('Location: add_edit.php?auth=ok');
		} else {
			echo "Erreur lors de la suppression";
			header('Location: add_edit.php?auth=ok');
		}
}
?>