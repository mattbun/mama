<?php

require_once("getid3/getid3.php");
require_once("db.php");


function scanFile($file, $dbconn){
	$getID3 = new getID3;
	$tag = $getID3->analyze($file);
	getid3_lib::CopyTagsToComments($tag);
	
	addSongToDB($tag,$file,$dbconn);
}

# We're using $musicdir to know when to stop deleting empty folders
function deleteFiles($paths, $musicdir){
	foreach ($paths as $fullpath){
		unlink($fullpath);
		cleanUpFolders($fullpath, $musicdir);
	}
}

#Removes folders that have been made empty by file operations
function cleanUpFolders($fullpath, $musicdir){
	
	if ($musicdir[strlen($musicdir)-1] == "/"){
		$musicdir = substr($musicdir, 0, strlen($musicdir)-1);
	}

	$current = dirname($fullpath);

	while ($current != $musicdir && $current != "." && rmdir($current)){
		$current = dirname($current);
	}
}
		
	

function scan($dir, $dbconn){
	$iterator = new DirectoryIterator($dir);	
	$getID3 = new getID3;

	foreach ($iterator as $fileinfo) {
		
		$filename = $fileinfo->getFilename();
		
		if ($filename[0] != '.' && !$fileinfo->isDot()){
			
			if ($fileinfo->isDir()){
				scan($fileinfo->getPathname(), $dbconn);
			}
			else if ($fileinfo->isFile()){
				//echo $fileinfo->getPathname() . "  -  " . mime_content_type($fileinfo->getPathname()) . "  -  " . $fileinfo->getExtension() . "\n";
				checkFile($getID3, $fileinfo, $dbconn);
			}
		}
	}
}

//Checking to see if the file is actually a music file.
function checkFile($getID3, $fileinfo, $dbconn){	
	$accepted_filetypes = array('mp3', 'flac');
	
	$tag = $getID3->analyze($fileinfo->getPathname());
	getid3_lib::CopyTagsToComments($tag);

	if (in_array($fileinfo->getExtension(), $accepted_filetypes)){
		addSongToDB($tag,$fileinfo->getPathname(), $dbconn);
	}
}

//Add Artist, Album, Song to Database
function addSongToDB($tag, $path, $dbconn){

	print $path . "\n";

	$artist = $tag['comments_html']['artist'][0];
	$album = $tag['comments_html']['album'][0];
	$year = $tag['comments_html']['year'][0] or $year = $tag['comments_html']['date'][0];
	$title = $tag['comments_html']['title'][0];
	$track = $tag['comments_html']['track_number'][0];
	$disc = $tag['comments_html']['part_of_a_set'][0];
	$genre = $tag['comments_html']['genre'][0];

	//Database only allows strings up to 250 chars in length
	$artist = substr($artist, 0, 249);
	$album = substr($album, 0, 249);
	$title = substr($title, 0, 249);
	$genre = substr($genre, 0, 249);

	//Doing some quick error checking
	if ($artist == null || $artist == ""){
		$artist = "Unknown Artist";
	}

	if ($album == null || $album == ""){
		$album = "Unknown Album";
	}

	if ($track == null){
		$track = "";
	}
	
	//track and disc number is stored as "number/total" so we need to split them up!
	$trackparts = explode('/',$track);
	$trackno = intval($trackparts[0]);
	$tracktotal = intval($trackparts[1]);

	$discparts = explode('/',$disc);
	$discno = intval($discparts[0]);
	$disctotal = intval($discparts[1]);

	
	//Now add this song (and possibly artist and album) to the database
	$artist_id = addArtist($dbconn, array($artist));
	$album_id = addAlbum($dbconn, array($album,$year, $artist_id));
	$track_id = addTrack($dbconn, array($title, $path, $trackno, $tracktotal, $discno, $disctotal, $genre));
	connectArtistToAlbum($dbconn, array($artist_id, $album_id));
	connectAlbumToTrack($dbconn, array($album_id, $track_id));

	print "<br><br>" . json_encode($tag["tags"]) . "<br><br>";
	//print $tag['comments_html']['artist'][0] . " " . $tag['comments_html']['album'][0] . " " . $tag['comments_html']['title'][0] . "\n";
	//print $artist_id . " " . $album_id . " " . $track_id . "\n";
	//print $artist . " " . $album . " " . $title . "\n";
	
}

//Replaces empty strings like "" with 0
function verifyInteger($int){
	if (!is_int($int)){
		return 0;
	}
	else {
		return intval($int);
	}
}


//Returns an array of all of the tag types that this tag contains/that should be written (id3v2,vorbis,etc.)
function getTagTypes($tag){

	$types = array();
	$accepted_types = array('id3v1', 'id3v2.3', 'id3v2.4', 'vorbiscomment');

	foreach($tag["tags"] as $key => $val){
		if (in_array($key, $accepted_types)){
			$types[] = $key;
		}
	}

	//If the file doesn't already have tags, we should give it some based on the format.
	//TODO add more formats here.
	if (empty($types)){
		$format = $tag["audio"]["dataformat"];

		if ($format == "flac"){
			return array("vorbiscomment");
		}
		else {
			return array('id3v1', 'id3v2.3', 'id3v2.4');
		}
	}

	return $types;
}
?>
