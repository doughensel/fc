<!DOCTYPE html>
<html>
<head>

<title>FC MetroStar Schedule</title>

<style>

	.playerName{ 
		display: inline-block;
		width: 200px; 
	}
	.gameDay ul li{ opacity: 0.5; }
		.gameDay ul li:hover{ opacity: 1; }

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
$mysqli = new mysqli($host, $username, $password, $database);
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

// ACCESS THE TABLES (or create them)
// http://stackoverflow.com/questions/6432178/how-can-i-check-if-a-mysql-table-exists-with-php
$schedule = $mysqli->query("SELECT * FROM schedule ORDER BY DayOf");
//printf("Select returned %d rows.\n", $schedule->num_rows);
$teams    = $mysqli->query("SELECT * FROM teams ORDER BY ID");
//printf("Select returned %d rows.\n", $teams->num_rows);
$players  = $mysqli->query("SELECT * FROM players");
//printf("Select returned %d rows.\n", $players->num_rows);
$score    = $mysqli->query("SELECT * FROM score");
//printf("Select returned %d rows.\n", $score->num_rows);

$team = array();
while( $teamObj = $teams->fetch_object() ){
	$team[$teamObj->ID] = $teamObj->Name;
}

$player = array();
while( $playerObj = $players->fetch_object() ){
	$player[$playerObj->ID] = $playerObj->Name;
}


// USE THE DB CAPTURED DATA
// foreach( $schedule->fetch_object() as $sch ){
while( $sch = $schedule->fetch_object() ){

	if( date( DATE_ATOM, time() ) < $sch->DayOf ){
		$gameID = $sch->ID;

		$playerYes = array();
		$playerNo  = array();
		$playerMay = array();

		if( !is_null($sch->PlayerYes )){ $playerYes = unserialize($sch->PlayerYes ); }
		if( !is_null($sch->PlayerNo  )){ $playerNo  = unserialize($sch->PlayerNo  ); }
		if( !is_null($sch->PlayerMay )){ $playerMay = unserialize($sch->PlayerMay ); }

		echo '<div class="gameDay">';

			echo '<h2>';
		
		echo date( "F j, Y, g:i a", strtotime( $sch->DayOf ) ) . ': Field #' . $sch->Field;

			echo '</h2>';

			echo '<p class="matchup">';

		$homeTeam = $team[intval($sch->Home)];
		$awayTeam = $team[intval($sch->Away)];
		
		echo $homeTeam . ' vs. ' . $awayTeam;

			echo '</p>'; // END P.matchup
			
			echo '<p class="roster">';
				echo '<ul>';
			
		foreach( $player as $pID => $pName ){

			$activeYes = ( checkPlayerStatus( $playerYes, $pID ) )? ' class="active" ' : '';
			$activeNo  = ( checkPlayerStatus( $playerNo,  $pID ) )? ' class="active" ' : '';
			$activeMay = ( $activeYes == '' && $activeNo == ''   )? ' class="active" ' : '';

			echo '<li>';
				
				echo '<span class="playerName">';
					echo $pName;
					echo '<input type="hidden" class="playerID" value="' . $pID . '" />';
				echo '</span>'; // END span.playerName
				echo '<span class="playerStatus">';
				
					echo '<a href="javascript:updateStatus(' . $pID . ', ' . $gameID . ', 0);" ' . $activeYes . '>Yes</a> ';
					echo '<a href="javascript:updateStatus(' . $pID . ', ' . $gameID . ', 1);" ' . $activeNo  . '>No</a> ';
					echo '<a href="javascript:updateStatus(' . $pID . ', ' . $gameID . ', 2);" ' . $activeMay . '>Maybe</a>';
				
				echo '</span>'; // END span.playerStatus
				
			echo '</li>';
		}
			
				echo '</ul>';
			echo '</p>'; // END P.roster

		echo '</div>';// END div.gameDay

	}// END if( time() > $sch->DayOf )

}// END while( $sch = $schedule->fetch_object() )

function checkPlayerStatus( $arr, $player ){
	$playerStatus = false;
	for( $x=0; $x<count($arr); $x++ ){
		if( $arr[$x] == $player ){ $playerStatus = true; }
	}
	return $playerStatus;
}

$mysqli->close(); 

?>

<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript">
	
$('.playerStatus a').click(function(){
	$(this).removeClass('active');
	$(this).siblings().removeClass('active');
	$(this).addClass('active');
});

function updateStatus( player, game, state ){
	var playerIDs = [];
	$('.gameDay:eq(0) span.playerName .playerID').each( function(){
		playerIDs.push( parseInt( $(this).val() ) );
	});

	$.ajax({
		url: 'update.php',
		type: "POST",
		data: {
			playerID: player,
			gameID  : game,
			status  : state,
			allIDs  : playerIDs
		}
	})
	.done(function( data ){
	//	console.log( data );
	})
	.complete(function(){
		console.log( 'process complete' );
	});
}

</script>

</body>
</html>