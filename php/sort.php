<?php

require_once("getid3/getid3.php");

function sortFile($original_path, $format_string, $music_path){

	//create getid3 instance
	$getID3 = new getID3;
	$tag = $getID3->analyze($original_path);
	getid3_lib::CopyTagsToComments($tag);

	//create path
	$destination = assemblePath($tag, $format_string, $original_path, $music_path);
	
	$ext = pathinfo($original_path, PATHINFO_EXTENSION);
	
	//check destination path
	while (file_exists($destination . "." . $ext)){
		$destination = $destination . "(duplicate)";
	}

	$destination = "$destination.$ext";

	//move file to destination path
	print $destination . "\n";
	moveFile($original_path, $destination);
	cleanUpFolders($original_path, $music_path);
	
	//return path
	return $destination;

}

function assemblePath($tag, $format_string, $path, $music_path){
	
	$artist = stripIllegalChars($tag['comments_html']['artist'][0]);
	$album = stripIllegalChars($tag['comments_html']['album'][0]);
	$year = stripIllegalChars($tag['comments_html']['year'][0]);
	$title = stripIllegalChars($tag['comments_html']['title'][0]);
	$track = $tag['comments_html']['track_number'][0];
	$disc = $tag['comments_html']['part_of_a_set'][0];
	$genre = stripIllegalChars($tag['comments_html']['genre'][0]);
	
	$trackparts = explode('/',$track);
	$track_no = intval($trackparts[0]);
	$track_total = intval($trackparts[1]);
	 
	$discparts = explode('/',$disc);
	$disc_no = intval($discparts[0]);
	$disc_total = intval($discparts[1]);

	
	#This could definitely be done in a better way... don't judge
	$result = $format_string;

	$result = str_replace("%artist%", $artist, $result);	
	$result = str_replace("%album%", $album, $result);
	$result = str_replace("%year%", $year, $result);
	$result = str_replace("%title%", $title, $result);
	$result = str_replace("%disc_no%", $disc_no, $result);
	$result = str_replace("%disc_total%", $disc_total, $result);
	$result = str_replace("%track_no%", sprintf("%02d", $track_no), $result);
	$result = str_replace("%track_total%", $track_total, $result);
	$result = str_replace("%genre%", $genre, $result);


	if ($music_path[strlen($music_path)-1] == "/"){
		$music_path = substr($music_path, 0, strlen($music_path) - 1);
	}
	
	if ($result[0] == "/"){
		$result = substr($result, 1);
	}

	return $music_path . "/" . $result; //And this is where I stop caring about windows
}

function moveFile($source_path, $destination_path){
	print "Moving $source_path to $destination_path... ";

	@mkdir (dirname($destination_path), 0777, true);

	if (rename($source_path, $destination_path)){
		print "SUCCESS\n";
	}
	else {
		print "FAILED\n";
	}
}

//Remove \0 and / from strings
function stripIllegalChars($string){
	$string = str_replace("/", "_", $string);
	$string = str_replace("\0", "_", $string);
	return $string;
}

?>
