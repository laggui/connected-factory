<?php
include('db.php');

// GET request
if (isset($_GET['device_id'])){
	$device_id = $_GET['device_id'];
	if (isset($_POST['device_name'])){
		$device_name = $_POST['device_name'];
		//Update to database
		$update_device_sql = "UPDATE device_list SET name='$device_name' WHERE id IN ('$device_id');";
		$update_device_query = mysqli_query($conn, $update_device_sql);	
		echo "Modification compl&eacutet&eacutee";
		header('Location: add_edit.php?auth=ok');
	}
	else if (isset($_POST['units_min'])) {
		$units_min = $_POST['units_min'];
		if (isset($_POST['units_max'])) {
			$units_max = $_POST['units_max'];
			//Update to database
			$update_units_sql = "UPDATE device_list SET min='$units_min', max='$units_max' WHERE id IN ('$device_id');";
			$update_units_query = mysqli_query($conn, $update_units_sql);	
			echo "Modification compl&eacutet&eacutee";
			header('Location: add_edit.php?auth=ok');
		}
	}
	else if (isset($_POST['device_range'])) {
		$device_range = $_POST['device_range'];
		//Update to database
		$update_range_sql = "UPDATE device_list SET mA=$device_range WHERE id IN ('$device_id');";
		$update_range_query = mysqli_query($conn, $update_range_sql);	
		echo "Modification compl&eacutet&eacutee";
		header('Location: add_edit.php?auth=ok');
	}
}
else if (isset($_POST['newdevice_id'])){
		$newdevice_id = $_POST['newdevice_id'];
		//Check if new id already exists in database
		$check_id_sql = "SELECT * FROM device_list WHERE id IN ('$newdevice_id');";
		$check_id_query = mysqli_query($conn, $check_id_sql);
		if (mysqli_num_rows($check_id_query) > 0) {
			echo "Un appareil est d&eacutej&agrave attribu&eacute &agrave cet ID";
			header('Location: add_edit.php?auth=ok');
		}
		else {
			if (isset($_POST['newdevice_name'])){
				$newdevice_name = $_POST['newdevice_name'];
				//Insert into database
				$insert_device_sql = "INSERT INTO device_list (id, name) VALUES ('$newdevice_id', '$newdevice_name');";
				$insert_device_query = mysqli_query($conn, $insert_device_sql);	
				echo "Ajout compl&eacutet&eacute";
				header('Location: add_edit.php?auth=ok');
			}
		}
}
else{
	echo "Erreur lors de la modification ou de l'ajout";
	header('Location: add_edit.php?auth=ok');
}

?>