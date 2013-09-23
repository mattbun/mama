<?php

require "../php/db.php";

$album_id = intval($_GET["album_id"]) or die ("need an album_id");
//$artist_id = "7";

$dbconn = dbConnect();

#$result = pg_prepare($dbconn, "getTracksByAlbum", "SELECT track_id, name, track_no, track_total, disc_no, disc_total, genre FROM AlbumTrackMap INNER JOIN Track ON AlbumTrackMap.track_id = Track.id WHERE album_id=$1 ORDER BY track_no;") or die ("Unable to prepare statement");
$result = pg_execute($dbconn, "getTracksByAlbum", array($album_id)) or die ("Unable to execute statement");

$arr = pg_fetch_all($result);
echo json_encode($arr);

dbDisconnect($dbconn);

?>
