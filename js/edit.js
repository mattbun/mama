
function editItem(){
	if (active_tracks.length > 0){
		startSparkling("Applying changes to track");
		
		var data = {};
		data.track_id = active_tracks[0].track_id;
		data.artist = $("#artist_field").val();
		data.album = $("#album_field").val();
		data.title = $("#track_field").val();
		data.year = $("#year_field").val();
		data.track_no = $("#track_no_field").val();
		data.track_total = $("#track_total_field").val();
		data.disc_no = $("#disc_no_field").val();
		data.disc_total = $("#disc_total_field").val();
		data.genre = $("#genre_field").val();

		$.ajax({
			url: 'api/edit.php'
			,type: 'GET'
			,success: function (){
				stopSparkling("Applying changes to track");
				getArtists();
				}
			,data: data
		});
	}
	else if (active_albums.length > 0){
		startSparkling("Applying changes to album");
		
		var data = {};
		data.album_id = active_albums[0].album_id;
		data.artist = $("#artist_field").val();
		data.album = $("#album_field").val();
		data.year = $("#year_field").val();
		data.track_total = $("#track_total_field").val();
		data.disc_no = $("#disc_no_field").val();
		data.disc_total = $("#disc_total_field").val();
		data.genre = $("#genre_field").val();
		
		$.ajax({
			url: 'api/edit.php'
			,type: 'GET'
			,success: stopSparkling("Applying changes to album")
			,data: data
		});
	}
	else if (active_artists.length > 0){

		var data = {};
		data.artist_id = active_artists[0].id;
		data.artist = $("#artist_field").val();

		startSparkling("Applying changes to artist");
		$.ajax({
			url: 'api/edit.php'
			,type: 'GET'
			,success: stopSparkling("Applying changes to artist")
			,data: data
		});
	}

	return false;
}
/*
function populateEdit(){
	if (active_tracks.length > 0){
		$("#delete_description").html('the track "' + active_tracks[0].name + '" from "' + active_albums[0].name + '" by "' + active_artists[0].name + '"');
	}
	else if (active_albums.length > 0){
		$("#delete_description").html('the album "' + active_albums[0].name + '" by "' + active_artists[0].name + '"');
	}
	else if (active_artists.length > 0){
		$("#delete_description").html('the artist "' + active_artists[0].name + '"');
	}	
}*/
