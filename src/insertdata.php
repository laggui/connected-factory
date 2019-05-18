<!DOCTYPE html>
<html>

<?php
include('db.php');

// GET request
if (isset($_GET['device'])){
	if (isset($_GET['data'])){
		$device_id = $_GET['device'];
		$data = $_GET['data'];
		//Insert into database
		$data_sql = "INSERT INTO test (id, device_id, value, time) VALUES ('NULL', $device_id, $data, NOW());";
		$data_query = mysqli_query($conn, $data_sql);
	}
}
else{
	echo "Data not received";
}
?> 
</html>