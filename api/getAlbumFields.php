<?php

# get agreed-upon album artist, year, disc number, genre

require "../php/db.php";

$album_id = intval($_GET["album_id"]) or die ("need an album_id");

$dbconn = dbConnect();

#$result = pg_prepare($dbconn, "getTracksByAlbum", "SELECT track_id, name, track_no, track_total, disc_no, disc_total, genre FROM AlbumTrackMap INNER JOIN Track ON AlbumTrackMap.track_id = Track.id WHERE album_id=$1 ORDER BY track_no;") or die ("Unable to prepare statement");
$result = pg_execute($dbconn, "getTracksByAlbum", array($album_id)) or die ("Unable to execute statement");

$arr = pg_fetch_all($result);

$consensus = array();


$firstRun = TRUE;
foreach ($arr as $track){
	if ($firstRun){
		$consensus["track_total"] = $track["track_total"];
		$consensus["year"] = $track["year"];
		$consensus["disc_no"] = $track["disc_no"];
		$consensus["disc_total"] = $track["disc_total"];
		$consensus["genre"] = $track["genre"];
		$firstRun = FALSE;
	}
	else {
		if ($consensus["track_total"] != $track["track_total"]){
			$consensus["track_total"] = null;
		}

		if ($consensus["year"] != $track["year"]){
			$consensus["year"] = null;
		}
		
		if ($consensus["disc_no"] != $track["disc_no"]){
			$consensus["disc_no"] = null;
		}
		
		if ($consensus["disc_total"] != $track["disc_total"]){
			$consensus["disc_total"] = null;
		}
		
		if ($consensus["genre"] != $track["genre"]){
			$consensus["genre"] = null;
		}
	}
}

echo json_encode($consensus);

dbDisconnect($dbconn);

?>
