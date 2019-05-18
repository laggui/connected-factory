<?php
include('db.php');

if (isset($_GET['device_id'])) { 
	$device_id = $_GET['device_id'];
}
else {
	$device_id = 0;
}

//header to give the order to the browser
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment;filename=data_device-' . $device_id . '.csv');
//header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('#', 'Lecture', 'Date'));

//select table to export the data
$select_sql = "SELECT id, value, time FROM test WHERE device_id IN ('$device_id');";
$select_query = mysqli_query($conn, $select_sql);
$list = array();

// Retrieve device units min & max
$device_data_points_sql = "SELECT min, max FROM device_list WHERE id IN ('$device_id');";
$device_data_points_query = mysqli_query($conn, $device_data_points_sql);
if (mysqli_num_rows($device_data_points_query) > 0) {
	foreach ($device_data_points_query as $points) {	
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

// loop over the rows, outputting them
/*while ($rows = mysqli_fetch_assoc($select_query)) {
      fputcsv($output, $rows);
    }*/
while ($rows = mysqli_fetch_assoc($select_query)) {
	$device_data = $rows['value'];
	$data = bcadd((bcmul((string)$m, (string)$device_data, '50')), (string)$b, '2');
	$list = array($rows['id'], $data, $rows['time']); 
	fputcsv($output, $list);
}	
fclose($output);
?>