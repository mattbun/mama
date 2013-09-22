<?php

//Pulls a setting from the Settings table
/*function getSetting($key){
	$dbconn = dbConnect();
	
	$result = pg_execute($dbconn, "getSetting", array($key))
		or die("Failed to execute statement");

	$arr = pg_fetch_array($result, 0, PGSQL_NUM) 
		or die ("No setting with that key");
	
	dbDisconnect($dbconn);

	return $arr[0];
}*/

function getSetting($dbconn, $key){
	
	$result = pg_execute($dbconn, "getSetting", array($key))
		or die("Failed to execute statement");

	$arr = pg_fetch_array($result, 0, PGSQL_NUM) 
		or die ("No setting with that key");
	
	return $arr[0];
}

function changeSetting($key, $newvalue){
	$dbconn = dbConnect();

	$result = pg_execute($dbconn, "changeSetting", array($key, $newvalue)) or die("Failed to execute statement");

	dbDisconnect($dbconn);
}

//Connects to the database
function dbConnect(){	
	$dbname = "mama";
	$dbuser = "mama";
	$dbconn = pg_connect("dbname=" . $dbname . " user=". $dbuser) or die("could not connect to database");


	$result = pg_prepare($dbconn, "getSetting", 'SELECT value FROM Settings WHERE key = $1') or die("Failed to prepare statement");
	$result = pg_prepare($dbconn, "changeSetting", 'UPDATE Settings SET value = $2 WHERE key = $1') or die("Failed to prepare statement");

	#$result = pg_prepare($dbconn, "addAlbum", 'INSERT INTO Album (name, year, date_added) SELECT $1::text, $2::int, CURRENT_TIMESTAMP WHERE NOT EXISTS (SELECT 1 FROM Album WHERE name = $1 AND (year = $2 OR year is NULL)) RETURNING id;') or die("Failed to prepare statement");
	$result = pg_prepare($dbconn, "addArtist", 'INSERT INTO Artist (name, sort_name, date_added) SELECT $1::text, $2::text, CURRENT_TIMESTAMP WHERE NOT EXISTS (SELECT 1 FROM Artist WHERE name = $1);') or die("Failed to prepare statement");
	$result = pg_prepare($dbconn, "addTrack", 'INSERT INTO Track (name, path, track_no, track_total, disc_no, disc_total, genre, date_added) SELECT $1::text, $2::text, $3::int, $4::int, $5::int, $6::int, $7::text, CURRENT_TIMESTAMP WHERE NOT EXISTS (SELECT 1 FROM Track WHERE path = $2) RETURNING id;') or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "connectAlbumTrack", "INSERT INTO AlbumTrackMap (album_id, track_id) SELECT $1::int, $2::int WHERE NOT EXISTS (SELECT 1 FROM AlbumTrackMap WHERE album_id = $1 AND track_id = $2)") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "connectArtistAlbum", "INSERT INTO ArtistAlbumMap (artist_id, album_id) SELECT $1::int, $2::int WHERE NOT EXISTS (SELECT 1 FROM ArtistAlbumMap WHERE artist_id = $1 AND album_id = $2)") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "countAlbumIDsByArtist", "SELECT count(album_id) FROM ArtistAlbumMap WHERE artist_id = $1") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "countTrackIDsByAlbum", "SELECT count(track_id) FROM AlbumTrackMap WHERE album_id = $1") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "deleteAlbum", "DELETE FROM Album WHERE id = $1") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "deleteArtist", "DELETE FROM Artist WHERE id = $1") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "deleteTrack", "DELETE FROM Track WHERE id = $1 RETURNING path") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "deleteAlbumTrackMapEntry", "DELETE FROM AlbumTrackMap WHERE track_id = $1 RETURNING album_id") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "deleteAlbumTrackMapEntries", "DELETE FROM AlbumTrackMap WHERE album_id = $1 RETURNING track_id") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "deleteArtistAlbumMapEntry", "DELETE FROM ArtistAlbumMap WHERE album_id = $1 RETURNING artist_id") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "deleteArtistAlbumMapEntries", "DELETE FROM ArtistAlbumMap WHERE artist_id = $1 RETURNING album_id") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "findArtist", 'SELECT id FROM Artist WHERE name = $1;') or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "findAlbum", 'SELECT id FROM Album WHERE name = $1 AND (year = $2 OR year is NULL);') or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "findAlbum", "SELECT album_id FROM ArtistAlbumMap INNER JOIN Album ON ArtistAlbumMap.album_id = Album.id WHERE name = $1 AND artist_id = $3 AND (year = $2 OR year is NULL)") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "findTrack", 'SELECT id FROM Track WHERE path = $1::text;') or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getAlbumIDsByArtist", "SELECT album_id FROM ArtistAlbumMap WHERE artist_id = $1") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getAlbumIDFromTrack", "SELECT album_id FROM AlbumTrackMap WHERE track_id = $1") or die("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getAlbumsByArtist", "SELECT album_id, name, year FROM ArtistAlbumMap INNER JOIN Album ON ArtistAlbumMap.album_id = Album.id WHERE artist_id=$1 ORDER BY year;") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getArtistIDFromAlbum", "SELECT artist_id FROM ArtistAlbumMap WHERE album_id = $1") or die("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getTrack", "SELECT * FROM Track WHERE id=$1;") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getTrackIDsByAlbum", "SELECT track_id FROM AlbumTrackMap WHERE album_id = $1") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getTracksByAlbum", "SELECT track_id, name, track_no, track_total, disc_no, disc_total, genre FROM AlbumTrackMap INNER JOIN Track ON AlbumTrackMap.track_id = Track.id WHERE album_id=$1 ORDER BY track_no;") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getTrackFields", "SELECT * FROM Track WHERE id = $1;") or die ("Unable to prepare statement");
	$result = pg_prepare($dbconn, "getTracks", "SELECT track_id From AlbumTrackMap WHERE album_id = $1;") or die ("Unable to prepare statement");

	pg_prepare($dbconn, "updateArtist", "UPDATE Artist SET name=$2, sort_name=$3 WHERE id = $1") or die ("Unable to prepare statement");
	pg_prepare($dbconn, "updateAlbum", "UPDATE Album SET name=$2,year=$3 WHERE id = $1") or die ("Unable to prepare statement");

	$result = pg_prepare($dbconn, "addAlbum", "INSERT INTO Album (name, year, date_added) SELECT $1::text, $2::int, CURRENT_TIMESTAMP WHERE NOT EXISTS (SELECT artist_id, album_id, name, year FROM ArtistAlbumMap INNER JOIN Album ON ArtistAlbumMap.album_id = Album.id WHERE name = $1 AND artist_id = $3 AND (year = $2 OR year is NULL)) RETURNING id;") or die ("Unable to prepare statement");
	return $dbconn;
}

//Disconnects from the database
function dbDisconnect($dbconn){
	pg_close($dbconn);
}

//Adds an artist to the database if it doesn't already exist
function addArtist($dbconn, $input){	

	$input[] = createSortName($input[0]);

	$result = pg_execute($dbconn, "addArtist", $input)
		or die("Failed to execute statement");
	
	array_pop($input);

	$artist_id = pg_fetch_array($result, 0, PGSQL_NUM)[0] or $artist_id = -1;

	if ($artist_id < 0){
		$result = pg_execute($dbconn, "findArtist", $input)
			or die("Failed to execute statement");
		$artist_id = pg_fetch_array($result, 0, PGSQL_NUM)[0] or $artist_id = -1;
	}

	return $artist_id;
}

function createSortName($name){
	$name = strtolower($name);
	$words = array("the");

	foreach ($words as $word){
		if (strlen($name) > strlen($word) && substr($name, 0, strlen($word) + 1) == ($word . " ")){
			return substr($name, strlen($word) + 1);
		}
	}

	return $name;
}

//Adds an album to the database if it doesn't already exist
function addAlbum($dbconn, $input){
	$result = pg_execute($dbconn, "addAlbum", $input)
		or die("Failed to execute statement");
	$album_id = pg_fetch_array($result, 0, PGSQL_NUM)[0] or $album_id = -1;

	if ($album_id < 0){
		$result = pg_execute($dbconn, "findAlbum", $input)
			or die ("Failed to execute statement");
		$album_id = pg_fetch_array($result, 0, PGSQL_NUM)[0] or $album_id = -1;
	}

	return $album_id;
}

//Adds a track to the database if it doesn't already exist
function addTrack($dbconn, $input){
	
	$result = pg_execute($dbconn, "addTrack", $input)
		or die ("Failed to execute statement to add track");
	$track_id = pg_fetch_array($result, 0, PGSQL_NUM)[0] or $track_id = -1;
	
	if ($track_id < 0){
		$result = pg_execute($dbconn, "findTrack", array($input[1]))
			or die ("Failed to execute statement");
		$track_id = pg_fetch_array($result, 0, PGSQL_NUM)[0] or $track_id = -1;
	}

	return $track_id;
}

//Maps an album to an artist in the ArtistAlbumMap table
function connectArtistToAlbum($dbconn, $input){
	if ($input[0] > -1 && $input[1] > -1){
		$result = pg_execute($dbconn, "connectArtistAlbum", $input)
			or die ("Failed to execute statement to map album to artist");
	}
}

//Maps a track to an album in the AlbumTrackMap table
function connectAlbumToTrack($dbconn, $input){
	if ($input[0] > -1 && $input[1] > -1){
		$result = pg_execute($dbconn, "connectAlbumTrack", $input)
			or die ("Failed to execute statement to map track to album");
	}	
}


?>
