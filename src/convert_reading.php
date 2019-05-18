<?php
include('db.php');
// Retrieve device range
				$device_range_sql = "SELECT mA FROM device_list WHERE id IN ('1');";
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
					echo "Error.";
					$x2 = 800;
					$x1 = 162;
				}
echo "x2 = ".$x2." x1 = ".$x1."";
?>