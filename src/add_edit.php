<!DOCTYPE html>
<html>
<?php
include('db.php');
// Retrieve the list of devices
$device_list_sql = "SELECT id, name, mA FROM device_list ORDER BY id ASC;";
$device_list_query = mysqli_query($conn, $device_list_sql);		
// Retrieve the list of devices and their units
$device_units_list_sql = "SELECT id, min, max FROM device_list ORDER BY id ASC;";
$device_units_list_query = mysqli_query($conn, $device_units_list_sql);		

$device_id = 0;	
$password = 0;
$auth = 0;
?>
<head>
<link rel="stylesheet" type="text/css" href="style.css" />
	<title>Ajouter/modifier des appareils</title>
	
	<script type="text/javascript">
		function go_back() {			
			window.location = "index.php";
		}
	</script>
</head>
<style>
body {
	background-color: #f37437;	
	font-family:Arial;
	color: #333333;
}
</style>
<body>
<button type="button" class="myButton" onclick="go_back()">Retour</button>
<p></p>
<?php
if ((isset($_POST['password'])) || (isset($_GET['auth']))) {
	if (isset($_POST['password'])) {
		$password = $_POST['password'];
		$auth = "ok";
	}
	else if (isset($_GET['auth'])) {
		$auth = $_GET['auth'];
		$password = "123";
	}
	if($password == "123" && $auth == "ok") {
echo <<<DEVICES
		<h3>Propri&eacutet&eacutes et liste des appareils</h3>
		<table>
		<tr>
			<th>ID</th>
			<th>Nom</th>
			<th>Supprimer</th>
			<th>Nouveau nom</th>
			<th>Gamme</th>
			<th>Changer la gamme</th>
		</tr>
DEVICES;
		if (mysqli_num_rows($device_list_query) > 0) {
			foreach ($device_list_query as $device_list) {	
				$device_id = $device_list['id'];
				$device_name = $device_list['name'];
				$device_range = $device_list['mA'];
echo <<<TABLE
				<tr>
					<td>$device_id</td>
					<td>$device_name</td>
					<td><a href="delete_data.php?device_id=$device_id">Supprimer</a></td>	
				<form action="data_to_sql.php?device_id=$device_id" method="post">
					<td><input type="text" name="device_name" maxlength="18"><input type="submit" value="Modifier"></td>
				</form>
					<td>$device_range*</td>
				<form action="data_to_sql.php?device_id=$device_id" method="post">
					<td><input type="text" name="device_range" maxlength="1"><input type="submit" value="Modifier"></td>
				</tr>
				</form>
TABLE;
			}
			echo "</table>";
		} else {
		echo "</table><h2>Aucun appareil trouv&eacute</h2>"; 
		}
echo <<<FORM
		*1: Appareil 4-20mA [d&eacutefaut]. 0: Sortie 0-5V.
		<p></p>
		<form action="data_to_sql.php" method="post">
		<table>
		<tr>
			<th>Ajouter un appareil</th>
		</tr>
		<tr>
			<td>
				ID: <input type="text" name="newdevice_id" maxlength="1">
				Nom: <input type="text" name="newdevice_name" maxlength="18">
				<input type="submit" value="Ajouter">
			</td>
		</tr>
		</table>
		</form>
		<p></p>
FORM;
echo <<<UNITS
		<h3>Unit&eacutes des appareils</h3>
		<table>
		<tr>
			<th>ID</th>
			<th>Valeur minimum</th>
			<th>Valeur maximum</th>
			<th>Nouvelle valeur min.</th>
			<th>Nouvelle valeur max.</th>
			<th>Confirmer modification</th>
		</tr>
UNITS;
		if (mysqli_num_rows($device_units_list_query) > 0) {
			foreach ($device_units_list_query as $device_units_list) {	
				$device_id = $device_units_list['id'];
				$units_min = $device_units_list['min'];
				$units_max = $device_units_list['max'];
echo <<<TABLE
				<tr>
					<td>$device_id</td>
					<td>$units_min</td>
					<td>$units_max</td>
				<form action="data_to_sql.php?device_id=$device_id" method="post">
					<td><input type="text" name="units_min" maxlength="6"></td>
					<td><input type="text" name="units_max" maxlength="6"></td>
					<td><input type="submit" value="Modifier"></td>
				</tr>
				</form>
TABLE;
			}
		}
	}
	else {header('Location: index.php');}
}
else {header('Location: index.php');}
?>
</body>
</html>