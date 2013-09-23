<?php

require "../php/db.php";

$dbconn = dbConnect();
#$result = pg_prepare($dbconn, "getArtists", "SELECT name FROM Artist ORDER BY name;") or die ("could not prepare statement to get Artists");

if ($_GET["epoch"] != null){
	$result = pg_prepare($dbconn, "getArtistByDate", "SELECT id, name FROM Artist WHERE extract(EPOCH FROM date_added) > $1 ORDER BY date_added;");
	$result = pg_execute($dbconn, "getArtistByDate", array($_GET["epoch"]));
	$arr = pg_fetch_all($result);
	echo json_encode($arr);
}
else if ($_GET["interval"] != null){
	$result = pg_prepare($dbconn, "getArtistByDate", "SELECT id, name FROM Artist WHERE date_added >= NOW() - $1::interval ORDER BY date_added DESC;");
	$result = pg_execute($dbconn, "getArtistByDate", array($_GET["interval"]));
	$arr = pg_fetch_all($result);
	echo json_encode($arr);
}
else {
	$result = pg_query($dbconn, "SELECT id, name, sort_name FROM Artist ORDER BY sort_name;") or die ("could not execute statement to get Artists");
	$arr = pg_fetch_all($result);
	echo json_encode($arr);
}


dbDisconnect($dbconn);

?>
