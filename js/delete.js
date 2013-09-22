
function deleteItem(){
	if (active_tracks.length > 0){
		startSparkling("Deleting track");
		$.ajax({
			url: 'api/delete.php'
			,type: 'GET'
			,success: stopSparkling("Deleting track")
			,data: {track_id:active_tracks[0].track_id}
		});
	}
	else if (active_albums.length > 0){
		startSparkling("Deleting album");
		$.ajax({
			url: 'api/delete.php'
			,type: 'GET'
			,success: stopSparkling("Deleting album")
			,data: {album_id:active_albums[0].album_id}
		});
	}
	else if (active_artists.length > 0){
		startSparkling("Deleting artist");
		$.ajax({
			url: 'api/delete.php'
			,type: 'GET'
			,success: stopSparkling("Deleting artist")
			,data: {artist_id:active_artists[0].id}
		});
	}

	return false;
}

function populateDelete(){
	if (active_tracks.length > 0){
		$("#delete_description").html('the track "' + active_tracks[0].name + '" from "' + active_albums[0].name + '" by "' + active_artists[0].name + '"');
	}
	else if (active_albums.length > 0){
		$("#delete_description").html('the album "' + active_albums[0].name + '" by "' + active_artists[0].name + '"');
	}
	else if (active_artists.length > 0){
		$("#delete_description").html('the artist "' + active_artists[0].name + '"');
	}	
}
