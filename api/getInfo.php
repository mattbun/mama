<?php

require "../php/db.php";

$track_id = intval($_GET["track_id"]) or $album_id = intval($_GET["album_id"]) or $album_id = 411 or die ("need an album_id");

$dbconn = dbConnect();

#pg_prepare($dbconn, "getTrackFields", "SELECT * FROM Track WHERE id = $1;") or die ("Unable to prepare statement");
#pg_prepare($dbconn, "getTracks", "SELECT track_id From AlbumTrackMap WHERE album_id = $1;") or die ("Unable to prepare statement");

if ($track_id){
	echo json_encode(getTrackInfo($dbconn, $track_id));
}
else {
	echo json_encode(getAlbumInfo($dbconn, $album_id));
}

dbDisconnect($dbconn);


function getTrackInfo($dbconn, $track_id){
	
	$result = pg_execute($dbconn, "getTrackFields", array($track_id)) or die ("Unable to execute statement");

	$arr = pg_fetch_all($result);
	
	return $arr;

}

function getAlbumInfo($dbconn, $album_id){
	
	$result = pg_execute($dbconn, "getTracks", array($album_id)) or die ("Unable to execute statement");

	$arr = pg_fetch_all($result);

	$firstRun = TRUE;

	echo json_encode($arr) . "<br><br>";

	/*$answer = array(
		"year" => "",
		"track_total" => "",
		"disc_no" => "",
		"disc_total" => "",
		"genre" => "");*/
	$answer = array();

	foreach ($arr as $track){
		$track_info = getTrackInfo($dbconn, $track["track_id"]);
		
		echo json_encode($track_info) . "<br><br>";

		if ($firstRun){
			$answer = array(
				'track_total' => $track_info["track_total"],
				'disc_no' => $track_info["disc_no"],
				'disc_total' => $track_info["disc_total"],
				'genre' => $track_info["genre"]
				);

			echo $track_info["track_total"];
			#$answer["artist"] = $track_info["artist"];
			#$answer["year"] = $track_info["year"];
			#$answer["track_total"] = $track_info["track_total"];
			#$answer["disc_no"] = $track_info["disc_no"];
			#$answer["disc_total"] = $track_info["disc_total"];
			#$answer["genre"] = $track_info["genre"];

			$firstRun = FALSE;
		}
	}

	return $answer;
	#return $arr;
	#return $track_info;
}

?>
