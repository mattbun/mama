<?php

require '../php/file.php';

#$path = $_GET["path"] or //What do we say about scanning a specific directory? Not today.
$dbconn = dbConnect();
$path = getSetting($dbconn, "MUSIC_PATH");

scan($path, $dbconn);
dbDisconnect($dbconn);

?>
