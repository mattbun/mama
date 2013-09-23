<?php

require "../php/db.php";
require "../php/delete.php";
require "../php/file.php";

$track_id = intval($_GET["track_id"]);
$album_id = intval($_GET["album_id"]);
$artist_id = intval($_GET["artist_id"]);

$dbconn = dbConnect();

if ($track_id != null){	
	$track_paths = deleteTrack($dbconn, $track_id);
	deleteFiles($track_paths, getSetting($dbconn, "MUSIC_PATH"));
}
else if ($album_id != null){
	$track_paths = deleteAlbum($dbconn, $album_id);
	deleteFiles($track_paths, getSetting($dbconn, "MUSIC_PATH"));
}
else if ($artist_id != null){
	$track_paths = deleteArtist($dbconn, $artist_id);
	deleteFiles($track_paths, getSetting($dbconn, "MUSIC_PATH"));
}

dbDisconnect($dbconn);

?>
