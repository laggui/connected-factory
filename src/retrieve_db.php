<?php
include('db.php');
// Retrieve most recent data and different device id's
$device_id_data_list_sql = "(SELECT device_id, value FROM test WHERE id = (SELECT id FROM test AS lookup WHERE lookup.device_id = test.device_id ORDER BY id DESC LIMIT 1))ORDER BY device_id ASC;";
$device_id_data_list_query = mysqli_query($conn, $device_id_data_list_sql);
$device_id_data_list = array();

if (mysqli_num_rows($device_id_data_list_query) > 0) {
	while($row_fetch = mysqli_fetch_assoc($device_id_data_list_query)){
		$device_id_data_list[] = $row_fetch;
	}
	$device_found = 1;
} else {
	//echo "No devices found!"; 
	$device_found = 0; 
}

// Retrieve different device id's
$device_id_list_sql = "SELECT device_id FROM test WHERE id = (SELECT id FROM test AS lookup WHERE lookup.device_id = test.device_id ORDER BY id DESC LIMIT 1);";
$device_id_list_query = mysqli_query($conn, $device_id_list_sql);
$device_id_list = array();

if (mysqli_num_rows($device_id_list_query) > 0) {
	$index = 0;
	while($rows_fetch = mysqli_fetch_assoc($device_id_list_query)){
		$device_id_list[$index] = $rows_fetch['device_id'];
		$index++;
	}
} else {
	//echo "No devices found!"; 
}
?>