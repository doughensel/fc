<!DOCTYPE html>
<html>
<head>

<title>FC MetroStar Schedule</title>

</head>
<body>

<?php 

include_once "config.php"; 

$con=mysqli_connect($database, $username, $password, $db_table);

if (mysqli_connect_errno()){ echo "Failed to connect to MySQL: " . mysqli_connect_error(); }



mysqli_close($con); 

?>

</body>
</html>