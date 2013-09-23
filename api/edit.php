<?php

require_once "../php/getid3/getid3.php";
require "../php/delete.php";
require "../php/db.php";
require "../php/file.php";
require_once "../php/sort.php";

$track_id = intval($_GET["track_id"]);
$album_id = intval($_GET["album_id"]);
$artist_id = intval($_GET["artist_id"]);

$dbconn = dbConnect();

$getID3 = new getID3;
$getID3->setOption(array('encoding'=>'UTF-8')); #Just to be safe
getid3_lib::IncludeDependency(GETID3_INCLUDEPATH . 'write.php', __FILE__, true);


#$result = pg_prepare($dbconn, "getTrack", "SELECT * FROM Track WHERE id=$1;") or die ("Unable to prepare statement");
#$result = pg_prepare($dbconn, "getTrackIDsByAlbum", "SELECT track_id FROM AlbumTrackMap WHERE album_id = $1");
#$result = pg_prepare($dbconn, "getAlbumIDsByArtist", "SELECT album_id FROM ArtistAlbumMap WHERE artist_id = $1") or die ("Unable to prepare statement");


if ($track_id != null){	
	editTrack($dbconn, $getID3, $_GET);
}
else if ($album_id != null){
	editAlbum($dbconn, $getID3, $_GET);
}
else if ($artist_id != null){
	editArtist($dbconn, $getID3, $_GET);
}

dbDisconnect($dbconn);

function editArtist($dbconn, $getID3, $changes){
	$result = pg_execute($dbconn, "getAlbumIDsByArtist", array($changes["artist_id"])) or die ("Unable to execute statement");

	$albums = pg_fetch_all($result) or die("no albums by this artist");

	$tracks = array();

	#$result = pg_execute($dbconn, "updateArtist", array($changes["artist_id"], $changes["artist"]));
	//Update artist info.

	foreach ($albums as $album){
		echo json_encode($album);
		$changes["album_id"] = $album["album_id"];
		//array_push($tracks, editAlbum($dbconn, $getID3, $changes));
		editAlbum($dbconn, $getID3, $changes);
	}

	#$tracks = deleteArtist($dbconn, $changes["artist_id"]);
	#rescanFiles($tracks);
	#echo json_encode($tracks);
	return $tracks;
}


function editAlbum($dbconn, $getID3, $changes){
	$result = pg_execute($dbconn, "getTrackIDsByAlbum", array($changes["album_id"])) or die ("Unable to execute statement");
	
	$tracks = pg_fetch_all($result) or die("no songs in this album");
	$track_paths = array();

	//update album info in DB
	#$result = pg_execute($dbconn, "updateAlbum", array($changes["album_id"], $changes["album"], $changes["year"]));

	foreach ($tracks as $track){
		echo json_encode($track);
		$changes["track_id"] = $track["track_id"];
		//array_push($track_paths, editTrack($dbconn, $getID3, $changes));
		editTrack($dbconn, $getID3, $changes);
	}

	#$tracks = deleteAlbum($dbconn, $changes["album_id"]);
	#rescanFiles($tracks);
	#echo json_encode($tracks);

}

function editTrack($dbconn, $getID3, $changes){
	$result = pg_execute($dbconn, "getTrack", array($changes["track_id"])) or die ("Unable to execute statement");

	$track = pg_fetch_all($result) or die("no song with that id");

	$tag = $getID3->analyze($track[0]["path"]);
	getid3_lib::CopyTagsToComments($tag);
	$tagData = createTagData($changes, $track, $tag);
	
	$tagwriter = new getid3_writetags;
	$tagwriter->filename = $track[0]["path"];

	$tagwriter->tagformats = array('id3v1', 'id3v2.3', 'id3v2.4');
	//$tagwriter->tagformats = getTagTypes($tag);
	echo $tagwrite->tagformats;
	$tagwriter->overwrite_tags = true;
	$tagwriter->remove_other_tags = true;

	
	echo json_encode($track) . "<br>";
	echo json_encode($tagData);

	$tagwriter->tag_data = $tagData;

	if ($tagwriter->WriteTags()){
		echo 'Successfully wrote tags<br>';
		if (!empty($tagwriter->warnings)) {
        		echo 'There were some warnings:<br>'.implode('<br><br>', $tagwriter->warnings);
	    	}
	} else {
		echo 'Failed to write tags!<br>'.implode('<br><br>', $tagwriter->errors);
	}
	
	$newLocation = sortFile($track[0]["path"], getSetting($dbconn, "MUSIC_FORMAT"), getSetting($dbconn, "MUSIC_PATH"));
	deleteTrack($dbconn, $changes["track_id"]);
	#scanFile($track[0]["path"], $dbconn);
	scanFile($newLocation, $dbconn);
	#echo json_encode($tracks);

}


function rescanFiles($tracks){
	foreach($tracks as $track){
		scanFile($track);
	}
}

function createTagData($changes, $track, $tag){

	$title = $changes["title"] or $title = $track[0]['name'];
	$artist = $changes["artist"] or $artist = $tag['comments_html']['artist'][0];
	$album = $changes["album"] or $album = $tag['comments_html']['album'][0];//$track[0]["album"];
	$year = $changes["year"] or $year = $tag['comments_html']['year'][0];//$track[0]["year"];
	$genre = $changes["genre"] or $genre = $track[0]["genre"];
	$disc_no = $changes["disc_no"] or $disc_no = $track[0]["disc_no"];
	$disc_total = $changes["disc_total"] or $disc_total = $track[0]["disc_total"];
	$track_no = $changes["track_no"] or $track_no = $track[0]["track_no"];
	$track_total = $changes["track_total"] or $track_total = $track[0]["track_total"];
	
	$tagData["title"] = array($title);
	$tagData["artist"] = array($artist);
	$tagData["album"] = array($album);
	$tagData["year"] = array($year);
	$tagData["genre"] = array($genre);
	
	if ($disc_no != null && $disc_no != "" && $disc_total != null && $disc_total != ""){
		$tagData["part_of_a_set"] = array($disc_no . "/" . $disc_total);
	}
	else if ($disc_no != null && $disc_no != ""){
		$tagData["part_of_a_set"] = array($disc_no);
	}
	else if ($disc_total != null && disc_total != ""){
		$tagData["part_of_a_set"] = array("0/" . $disc_total);
	}
	
	if ($track_no != null && $track_total != "" && $track_total != null && $track_total != ""){
		$tagData["track"] = array($track_no . "/" . $track_total);
	}
	else if ($track_no != null && $track_no != ""){
		$tagData["track"] = array($track_no);
	}
	else if ($track_total != null && $track_total != ""){
		$tagData["track"] = array("0/" . $track_total);
	}

	return $tagData;
}

function createTagDataOld($changes, $track){
	
	$tagData = array();


	if ($changes["title"] != null && $changes["title"] != "" && $changes["title"] != $track[0]["name"]){
		$tagData["title"] = array($changes["title"]);
	}

	if ($changes["artist"] != null && $changes["artist"] != "" && $changes["artist"] != $track[0]["artist"]){
		$tagData["artist"] = array($changes["artist"]);
	}

	if ($changes["album"] != null && $changes["album"] != "" && $changes["album"] != $track[0]["album"]){
		$tagData["album"] = array($changes["album"]);
	}

	if ($changes["year"] != null && $changes["year"] != "" && $changes["year"] != $track[0]["year"]){
		$tagData["year"] = array($changes["year"]);
	}

	if ($changes["genre"] != null && $changes["genre"] != "" && $changes["genre"] != $track[0]["genre"]){
		$tagData["genre"] = array($changes["genre"]);
	}
	
	//Disc numbers are a bit more complicated because we have to format it like "1/2"
	if (($changes["disc_no"] != null && $changes["disc_no"] != "" && $changes["disc_no"] != $track[0]["disc_no"]) && ($changes["disc_total"] != null && $changes["disc_total"] != $track[0]["disc_total"])){
		$tagData["part_of_a_set"] = array($changes["disc_no"] . "/" . $changes["disc_total"]);
	}
	else if ($changes["disc_no"] != null && $changes["disc_no"] != "" && $changes["disc_no"] != $track[0]["disc_no"]){
		$tagData["part_of_a_set"] = array($changes["disc_no"] . "/" . $track[0]["disc_total"]);
	}
	else if ($changes["disc_total"] != null && $changes["disc_total"] != "" && $changes["disc_total"] != $track[0]["disc_total"]){
		$tagData["part_of_a_set"] = array($track[0]["disc_no"] . "/" . $changes["disc_total"]);
	}
	
	//Track numbers also need the "1/11" format
	if (($changes["track_no"] != null && $changes["track_no"] != "" && $changes["track_no"] != $track[0]["track_no"]) && ($changes["track_total"] != null && $changes["track_total"] != $track[0]["track_total"])){
		$tagData["track"] = array($changes["track_no"] . "/" . $changes["track_total"]);
	}
	else if ($changes["track_no"] != null && $changes["track_no"] != "" && $changes["track_no"] != $track[0]["track_no"]){
		$tagData["track"] = array($changes["track_no"] . "/" . $track[0]["track_total"]);
	}
	else if ($changes["track_total"] != null && $changes["track_total"] != "" && $changes["track_total"] != $track[0]["track_total"]){
		$tagData["track"] = array($track[0]["track_no"] . "/" . $changes["track_total"]);
	}

	return $tagData;
}


function checkInt($val){
	if (is_int($val)){

	}
}
?>
