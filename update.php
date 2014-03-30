<?php 

$playerID = $_POST['playerID'];
$gameID   = $_POST['gameID'];
$status   = $_POST['status'];
$allIDs   = $_POST['allIDs'];

include_once "config.php"; 

// CONNECT TO THE DATABASE
$con=mysqli_connect($database, $username, $password, $db_table);
if (mysqli_connect_errno()){ echo "Failed to connect to MySQL: " . mysqli_connect_error(); }

$schedule = mysqli_query($con,"SELECT * FROM Schedule WHERE ID=$gameID LIMIT 1");

$yesArray = [];
$noArray  = [];
$mayArray = [];

$yesString = '';
$noString  = '';
$mayString = '';

foreach( $schedule as $sch ){
	if( !is_null( $sch['PlayerYes'] ) ){ $yesArray = unserialize( $sch['PlayerYes'] ); }
	if( !is_null( $sch['PlayerNo' ] ) ){ $noArray  = unserialize( $sch['PlayerNo' ] ); }
	if( !is_null( $sch['PlayerMay'] ) ){ 
		$mayArray = unserialize( $sch['PlayerMay'] ); 
	}else{
		$mayArray = $allIDs;
	}

	// status => 0 is Yes | 1 is No | 2/Default is Maybe
	$state = ( $status == 0 )? 'add' : 'remove';
	$yesArray = updateArray( $yesArray, $playerID, $state );
	$state = ( $status == 1 )? 'add' : 'remove';
	$noArray  = updateArray( $noArray,  $playerID, $state );
	// even though there is a state for 'maybe' (2), I'm testing if neither
	// 'yes' (0) or 'no' (1) exists to catch any edge cases
	$state = ( $status != 0 && $status != 1 )? 'add' : 'remove';
	$mayArray = updateArray( $mayArray, $playerID, $state );

	// Serialize the arrays for the DB
	$yesString = serialize($yesArray);
	$noString  = serialize($noArray );
	$mayString = serialize($mayArray);
}

function updateArray( $arr, $player, $state ){
	$playerIndex = -1;
	$arrSize     = count($arr);
	for( $x=0; $x<$arrSize; $x++ ){
		if( $arr[$x] == $player ){ $playerIndex = $x; }
	}
	// Add to the array if the player does not already exist
	if( $state == 'add' && $playerIndex < 0 ){ $arr[] = $player; }
	// Remove the player from the array if his/her ID is in it
	if( $state != 'add' && $playerIndex >= 0 ){
		unset( $arr[$playerIndex] );
		$arr = array_values($arr);
	}
	return $arr;
}

mysqli_query($con,"UPDATE Schedule SET PlayerYes='$yesString', PlayerNo='$noString', PlayerMay='$mayString' WHERE ID=$gameID");

mysqli_close($con); 

?>