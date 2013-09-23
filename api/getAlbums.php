<?php

require "../php/db.php";

$artist_id = intval($_GET["artist_id"]) or die ("need an artist_id");
//$artist_id = "7";

$dbconn = dbConnect();

#$result = pg_prepare($dbconn, "getAlbumsByArtist", "SELECT album_id, name, year FROM ArtistAlbumMap INNER JOIN Album ON ArtistAlbumMap.album_id = Album.id WHERE artist_id=$1 ORDER BY year;") or die ("Unable to prepare statement");
$result = pg_execute($dbconn, "getAlbumsByArtist", array($artist_id)) or die ("Unable to execute statement");

$arr = pg_fetch_all($result);
echo json_encode($arr);

dbDisconnect($dbconn);

?>
