<?php

include 'db2-sqlite.php'; //This should be changed to use a different kind of database

$db = new DBConnection();
$db->connectAndPrepare();

print $db->addArtist(array("yeah luke"));

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
