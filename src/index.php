<!DOCTYPE html>
<html>
<?php
include('db.php');
include('retrieve_db.php');
?> 
<head>
<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	
	<title>Usine connect&eacute;e</title>
	
	<script type="text/javascript">
		var device_id_list = <?php echo json_encode($device_id_list);?>;
		$(document).ready(function() {
			$.ajaxSetup({ cache: false }); // This part addresses an IE bug.  without it, IE will only load the first number and will never refresh
			setInterval(function() {
			for (var i = 0; i < device_id_list.length; i++) {
				var device_id = device_id_list[i];
				$("#reload" + (device_id)).load("reload_data.php?device=" + (device_id));
			}
			}, 5000);
		});
	</script>
	<script type="text/javascript">
		function delete_popup() {
			window.location = "password.php?func=delete";
		}
		function addedit_popup() {
			window.location = "password.php?func=addedit";
		}
	</script>
</head>
<style>
body {
	background-color: #f37437;	
}
</style>
<body>
<img src = "http://i.imgur.com/DYp7ZST.png" alt="Logo Crée ta ville" class="top-left">
<img src = "http://sinformer.cgodin.qc.ca/images/LogoGGtransparent.png" alt="Logo Gérald-Godin" class="top-right">

<div class="container-fluid">
	<div class="row empty">		
		<div class="col-md-12"></div>
	</div>
<!-- title -->
	<div class="row">
		<div class="col-md-12">
			<h1>USINE CONNECT&EacuteE</h1>
		</div>
	</div>	
	<div class="row empty">		
		<div class="col-md-12"></div>
	</div>	
	<div class="row banner-style">	
		<div class="col-md-6">
			<button type="button" class="myButton pull-left" onclick="addedit_popup();">Ajouter/modifier des appareils</button>
		</div>
		<div class="col-md-6">					
			<button type="button" class="myButton pull-right" onclick="delete_popup()">Supprimer l'historique de données</button>		
		</div>
	</div>
	<div class="row empty">		
		<div class="col-md-12"></div>
	</div>
<!-- devices -->
	<?php		
		if ($device_found == 1) {
			$i = 0;
			foreach ($device_id_data_list as $list) {			
				$device_id = $list['device_id'];
				$device_data = $list['value'];	
				// Retrieve device name with id
				$device_list_sql = "SELECT name FROM device_list WHERE id IN ('$device_id');";
				$device_list_query = mysqli_query($conn, $device_list_sql);			
				if (mysqli_num_rows($device_list_query) > 0) {
					while($row = mysqli_fetch_assoc($device_list_query)){
						$device_name[] = $row['name'];
					}
				} else {
					$device_name[] = "Inconnu"; 
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
echo <<<END
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-5">
				<h2> Appareil: $device_name[$i]</h2>
					<div class="panel-group" id="accordion">
						<div class="panel panel-default">
							<div class="panel-heading" >
								<h4 class="panel-title" id="reload$device_id">
END;
				if (($device_data >= ($x2 + 80)) || ($device_data < ($x1 - 40))) {
					echo "<a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse$device_id\">Lecture: ERREUR.</a>";
				}
				else {
					echo "<a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse$device_id\">Lecture: $data</a>";
				}
echo <<<END
								</h4>
							</div>
							<div id="collapse$device_id" class="panel-collapse collapse out">
								<div class="panel-body"><a href="csv.php?device_id=$device_id">T&eacutel&eacutecharger le fichier d'enregistrement de donn&eacutees.</a></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6"></div>
			</div>
			<div class="row empty">		
				<div class="col-md-12"></div>
			</div>	
END;
				$i++;
			}
		}
		else {
echo <<<END
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-11">
				<h2> Aucune lecture disponible.</h2>
				</div>
			</div>
END;
		}
	?>
</div>
</body>
</html>

