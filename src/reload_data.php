<?php
include('db.php');

// GET request
if (isset($_GET['device'])){
		$device_id = $_GET['device'];
}
else{
	echo "Data not received";
}

// Retrieve most recent data
$device_data_list_sql = "SELECT value FROM test WHERE id = (SELECT id FROM test AS lookup WHERE lookup.device_id = '$device_id' ORDER BY id DESC LIMIT 1);";
$device_data_list_query = mysqli_query($conn, $device_data_list_sql);

if (mysqli_num_rows($device_data_list_query) > 0) {
	while($row_fetch = mysqli_fetch_assoc($device_data_list_query)){
		$device_data = $row_fetch['value'];
	}
	// Retrieve device units min & max
	$device_data_points_sql = "SELECT min, max FROM device_list WHERE id IN ('$device_id');";
	$device_data_points_query = mysqli_query($conn, $device_data_points_sql);
	if (mysqli_num_rows($device_data_points_query) > 0) {
		while ($points = mysqli_fetch_assoc($device_data_points_query)) {	
			$y2 = $points['max'];
			$y1 = $points['min'];
		}
	}
	else {
		$y2 = 800;
		$y1 = 162;
	}
	// Retrieve device range
	$device_range_sql = "SELECT mA FROM device_list WHERE id IN ('$device_id');";
	$device_range_query = mysqli_query($conn, $device_range_sql);
	if (mysqli_num_rows($device_range_query) > 0) {
		while ($range = mysqli_fetch_assoc($device_range_query)) {	
			$device_range = $range['mA'];
		}
		if ($device_range == 0) {
			$x2 = 1023;
			$x1 = 0;
		}
		else {
			$x2 = 800;
			$x1 = 162;
		}
	}
	else {
		$x2 = 800;
		$x1 = 162;
	}
	//$x1 = 162; //4mA
	//$x2 = 800; //20mA
	// Convert data to proper reading
	$y = ($y2 - $y1);
	$x = ($x2 - $x1);
	$m = bcdiv((string)$y, (string)$x, '100');
	$b = bcsub((string)$y2, bcmul((string)$m,(string)$x2, '50'), '50');
	$data = bcadd((bcmul((string)$m, (string)$device_data, '50')), (string)$b, '2');
	if (($device_data >= ($x2 + 80)) || ($device_data < ($x1 - 40))) {
		echo "<a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse$device_id\">Lecture: ERREUR.</a>";
	}
	else {
		echo "<a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse$device_id\">Lecture: $data</a>";
	}
} else {
	echo "No devices found!"; 
}
?>