<?php

function initDelete($dbconn){
	#$result = pg_prepare($dbconn, "getTrackIDsByAlbum", "SELECT track_id FROM AlbumTrackMap WHERE album_id = $1");
	#$result = pg_prepare($dbconn, "countTrackIDsByAlbum", "SELECT count(track_id) FROM AlbumTrackMap WHERE album_id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "countAlbumIDsByArtist", "SELECT count(album_id) FROM ArtistAlbumMap WHERE artist_id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "getAlbumIDsByArtist", "SELECT album_id FROM ArtistAlbumMap WHERE artist_id = $1");
	#$result = pg_prepare($dbconn, "deleteTrack", "DELETE FROM Track WHERE id = $1 RETURNING path") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteAlbum", "DELETE FROM Album WHERE id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteArtist", "DELETE FROM Artist WHERE id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteAlbumTrackMapEntry", "DELETE FROM AlbumTrackMap WHERE track_id = $1 RETURNING album_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteAlbumTrackMapEntries", "DELETE FROM AlbumTrackMap WHERE album_id = $1 RETURNING track_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteArtistAlbumMapEntry", "DELETE FROM ArtistAlbumMap WHERE album_id = $1 RETURNING artist_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteArtistAlbumMapEntries", "DELETE FROM ArtistAlbumMap WHERE artist_id = $1 RETURNING album_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "getAlbumIDFromTrack", "SELECT album_id FROM AlbumTrackMap WHERE track_id = $1") or die("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "getArtistIDFromAlbum", "SELECT artist_id FROM ArtistAlbumMap WHERE album_id = $1") or die("Unable to prepare statement");
}

function deleteTrack($dbconn, $track_id){
	
	#$result = pg_prepare($dbconn, "deleteAlbumTrackMapEntry", "DELETE FROM AlbumTrackMap WHERE track_id = $1 RETURNING album_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteTrack", "DELETE FROM Track WHERE id = $1 RETURNING path") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "countTrackIDsByAlbum", "SELECT count(track_id) FROM AlbumTrackMap WHERE album_id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteArtistAlbumMapEntry", "DELETE FROM ArtistAlbumMap WHERE album_id = $1 RETURNING artist_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteAlbum", "DELETE FROM Album WHERE id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "countAlbumIDsByArtist", "SELECT count(album_id) FROM ArtistAlbumMap WHERE artist_id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteArtist", "DELETE FROM Artist WHERE id = $1") or die ("Unable to prepare statement");

	//Delete track
	$result = pg_execute($dbconn, "deleteAlbumTrackMapEntry", array($track_id)) or die ("Unable to execute statement");
	$album_id = pg_fetch_all($result)[0]["album_id"];
	$result = pg_execute($dbconn, "deleteTrack", array($track_id)) or die ("Unable to execute statement");
	$tracks = pg_fetch_all($result);
	
	echo json_encode($album_id);

	$result = pg_execute($dbconn, "countTrackIDsByAlbum", array($album_id)) or die ("Unable to execute statement");
	$track_count = pg_fetch_all($result)[0]["count"];

	echo json_encode($track_count);

	if (intval($track_count) < 1){
		//Delete the album
		$result = pg_execute($dbconn, "deleteArtistAlbumMapEntry", array($album_id)) or die ("Unable to execute statement");
		$artist_id = pg_fetch_all($result)[0]["artist_id"];
		$result = pg_execute($dbconn, "deleteAlbum", array($album_id)) or die ("Unable to execute statement");

		echo json_encode($artist_id);

		$result = pg_execute($dbconn, "countAlbumIDsByArtist", array($artist_id)) or die ("Unable to execute statement");
		$album_count = pg_fetch_all($result)[0]["count"];

		echo json_encode($album_count);

		if (intval($album_count) < 1){
			$result = pg_execute($dbconn, "deleteArtist", array($artist_id)) or die ("Unable to execute statement");
		}
	}

	return array($tracks[0]["track_path"]);
}

function deleteAlbum($dbconn, $album_id){
	
	#$result = pg_prepare($dbconn, "deleteAlbumTrackMapEntries", "DELETE FROM AlbumTrackMap WHERE album_id = $1 RETURNING track_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteTrack", "DELETE FROM Track WHERE id = $1 RETURNING path") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteArtistAlbumMapEntry", "DELETE FROM ArtistAlbumMap WHERE album_id = $1 RETURNING artist_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteAlbum", "DELETE FROM Album WHERE id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "countAlbumIDsByArtist", "SELECT count(album_id) FROM ArtistAlbumMap WHERE artist_id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteArtist", "DELETE FROM Artist WHERE id = $1") or die ("Unable to prepare statement");
	
	//Delete tracks
	$result = pg_execute($dbconn, "deleteAlbumTrackMapEntries", array($album_id)) or die ("Unable to execute statement");
	$tracks = pg_fetch_all($result);
	
	$track_paths = array();

	foreach ($tracks as $track){
		$result = pg_execute($dbconn, "deleteTrack", array($track["track_id"]));
		$arr = pg_fetch_all($result);
		array_push($track_paths, $arr[0]["path"]);
	}

	//Delete the album
	$result = pg_execute($dbconn, "deleteArtistAlbumMapEntry", array($album_id)) or die ("Unable to execute statement");
	$artist_id = pg_fetch_all($result)[0]["artist_id"];
	$result = pg_execute($dbconn, "deleteAlbum", array($album_id)) or die ("Unable to execute statement");

	echo json_encode($artist_id);

	$result = pg_execute($dbconn, "countAlbumIDsByArtist", array($artist_id)) or die ("Unable to execute statement");
	$album_count = pg_fetch_all($result)[0]["count"];

	echo json_encode($album_count);

	if (intval($album_count) < 1){
		$result = pg_execute($dbconn, "deleteArtist", array($artist_id)) or die ("Unable to execute statement");
	}

	return $track_paths;
}

function deleteArtist($dbconn, $artist_id){
	#$result = pg_prepare($dbconn, "deleteArtistAlbumMapEntries", "DELETE FROM ArtistAlbumMap WHERE artist_id = $1 RETURNING album_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteAlbumTrackMapEntries", "DELETE FROM AlbumTrackMap WHERE album_id = $1 RETURNING track_id") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteTrack", "DELETE FROM Track WHERE id = $1 RETURNING path") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteAlbum", "DELETE FROM Album WHERE id = $1") or die ("Unable to prepare statement");
	#$result = pg_prepare($dbconn, "deleteArtist", "DELETE FROM Artist WHERE id = $1") or die ("Unable to prepare statement");
	
	$result = pg_execute($dbconn, "deleteArtistAlbumMapEntries", array($artist_id)) or die ("Unable to execute statement");
	$albums = pg_fetch_all($result);
	
	$track_paths = array();

	foreach ($albums as $album){
		
		$album_id = $album["album_id"];

		$result = pg_execute($dbconn, "deleteAlbumTrackMapEntries", array($album_id)) or die ("Unable to execute statement");
		$tracks = pg_fetch_all($result);
	
		foreach ($tracks as $track){
			$result = pg_execute($dbconn, "deleteTrack", array($track["track_id"])) or die ("Unable to execute statement");
			$arr = pg_fetch_all($result);
			array_push($track_paths, $arr[0]["path"]);
			echo json_decode($arr);
		}

		$result = pg_execute($dbconn, "deleteAlbum", array($album_id)) or die ("Unable to execute statement");
	}
	
	$result = pg_execute($dbconn, "deleteArtist", array($artist_id)) or die ("Unable to execute statement");

	return $track_paths;
}

?>
