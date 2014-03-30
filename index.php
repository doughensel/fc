<!DOCTYPE html>
<html>
<head>

<title>FC MetroStar Schedule</title>

<style>

	.playerName{ 
		display: inline-block;
		width: 200px; 
	}
	.playerStatus a{ text-decoration: none; }
		.playerStatus a:hover,
		.playerStatus a:focus,
		.playerStatus a.active{ text-decoration: underline; }

</style>

</head>
<body>

<?php 

include_once "config.php"; 

// CONNECT TO THE DATABASE
$con=mysqli_connect($database, $username, $password, $db_table);
if (mysqli_connect_errno()){ echo "Failed to connect to MySQL: " . mysqli_connect_error(); }

// ACCESS THE TABLES (or create them)
// http://stackoverflow.com/questions/6432178/how-can-i-check-if-a-mysql-table-exists-with-php
$schedule = mysqli_query($con,"SELECT * FROM Schedule ORDER BY DayOf");

if($schedule === FALSE){
	// Create table
	$sql = "CREATE TABLE Schedule 
			(
			ID        INT NOT NULL AUTO_INCREMENT, 
			PRIMARY KEY(ID),
			DayOf     DATETIME DEFAULT NULL,
			Home      INT,
			Away      INT,
			Field     INT,
			PlayerYes LONGTEXT,
			PlayerNo  LONGTEXT,
			PlayerMay LONGTEXT
			)";
    if (mysqli_query($con,$sql)){
	  echo "Table 'Schedule' created successfully. <br />";
	}else{
	  echo "Error creating table: " . mysqli_error($con);
	}
}

$teams = mysqli_query($con,"SELECT * FROM Teams ORDER BY ID");

if($teams === FALSE){
	// Create table
	$sql = "CREATE TABLE Teams 
			(
			ID        INT NOT NULL AUTO_INCREMENT, 
			PRIMARY KEY(ID),
			Name      VARCHAR(255)
			)";
    if (mysqli_query($con,$sql)){
	  echo "Table 'Teams' created successfully. <br />";
	}else{
	  echo "Error creating table: " . mysqli_error($con);
	}
}

$players = mysqli_query($con,"SELECT * FROM Players");

if($players === FALSE){
	// Create table
	$sql = "CREATE TABLE Players 
			(
			ID        INT NOT NULL AUTO_INCREMENT, 
			PRIMARY KEY(ID),
			Name      VARCHAR(255),
			Email     VARCHAR(255)
			)";
    if (mysqli_query($con,$sql)){
	  echo "Table 'Players' created successfully. <br />";
	}else{
	  echo "Error creating table: " . mysqli_error($con);
	}
}

$score = mysqli_query($con,"SELECT * FROM Score");

if($score === FALSE){
	// Create table
	$sql = "CREATE TABLE Score 
			(
			ID        INT NOT NULL AUTO_INCREMENT, 
			PRIMARY KEY(ID),
			Schedule  INT,
			Home      INT,
			Away      INT
			)";
    if (mysqli_query($con,$sql)){
	  echo "Table 'Score' created successfully. <br />";
	}else{
	  echo "Error creating table: " . mysqli_error($con);
	}
}

// USE THE DB CAPTURED DATA
foreach( $schedule as $sch ){


	if( time() > $sch['DayOf'] ){
		$gameID = $sch['ID'];
		echo '<div class="gameDay">';
			echo '<h2>';

		echo date( "F j, Y, g:i a", strtotime( $sch['DayOf'] ) ) . ': Field #' . $sch['Field'];

			echo '</h2>';
			echo '<p class="matchup">';
		// Is there a cleaner way to do this?
		foreach( $teams as $team ){
			if( $team['ID'] == $sch['Home'] ){
				echo $team['Name'];
				break;
			}
		}
			echo ' vs. ';
		// Is there a cleaner way to do this?
		foreach( $teams as $team ){
			if( $team['ID'] == $sch['Away'] ){
				echo $team['Name'];
				break;
			}
		}

			echo '</p>'; // END p.matchup

			echo '<p class="roster">';
				echo '<ul>';

		foreach( $players as $player ){
			echo '<li>';
				echo '<span class="playerName">';
					echo $player['Name'];
				echo '</span>'; // END span.playerName
				$playerID = $player['ID'];
				echo '<span class="playerStatus">';
					echo '<a href="javascript:updateStatus(' . $playerID . ', ' . $gameID . ', 0);">Yes</a> ';
					echo '<a href="javascript:updateStatus(' . $playerID . ', ' . $gameID . ', 1);">No</a> ';
					echo '<a href="javascript:updateStatus(' . $playerID . ', ' . $gameID . ', 2);">Maybe</a>';
				echo '</span>'; // END span.playerStatus
			echo '</li>';
		}

				echo '</ul>';
			echo '</p>'; // END p.roster

		echo '</div>'; // END .gameDay
	}// END if( time() > strtotime( $sch['DayOf'] ) )

}


mysqli_close($con); 

?>

<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript">
	
function updateStatus( playerID, gameID, status ){
	alert( 'test' );
}

</script>

</body>
</html>