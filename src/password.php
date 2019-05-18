<!DOCTYPE html>
<html>
<head>
<title>Authentification</title>
</head>
<style>
body {
	background-color: #f37437;	
	font-family:Arial;
	color: #333333;	
}
</style>
<body>
<?php 
if (isset($_GET['func'])){
	$func = $_GET['func'];
	if ($func == "delete") {
echo <<<FORM
		<form action="delete_data.php" method="post">
		Mot de passe: <input type="password" name="password"><br>
		<input type="submit" value="Confirmer">
		</form>
FORM;
	}
	else if ($func == "addedit") {
echo <<<FORM
		<form action="add_edit.php" method="post">
		Mot de passe: <input type="password" name="password"><br>
		<input type="submit" value="Confirmer">
		</form>
FORM;
	}
}
?>
</body>
</html> 