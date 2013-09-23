<?php

include 'db2-sqlite.php';

$db = new sqliteDBConnection();
$db->connectAndPrepare();
print $db->getSetting("MUSIC_PATH");
$db->setSetting("MUSIC_PATH","this/folder");
print $db->getSetting("MUSIC_PATH");

/*
abstract class dbConnection {

	abstract function connect();
	abstract function disconnect();

	//Prepare statements to be used
	abstract function prepare();
	//execute prepared statements
	abstract function getSetting($key);
	abstract function setSetting($key, $value);

}


function connectArtistToAlbum($db, $input){

}

function connectAlbumToTrack($db, $input){

}*/
