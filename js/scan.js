// Makes an API call to start a scan, sparkles while the scan happens.
function startScan(){
	startSparkling("Saving settings");

	var data = {};
	data.MUSIC_PATH = $("#music_location").val();
	data.MUSIC_FORMAT = $("#music_format").val();

	$.ajax({
		url: 'api/changeSettings.php'
		,type: 'GET'
		,data: data
		,success: function (){
			stopSparkling("Saving settings");
			startSparkling('Scanning Music Folder');
			$.ajax({
				type: "GET"
				,url: "api/scan.php"
				,success: function (){
					stopSparkling('Scanning Music Folder');
					getArtists();
				}
			});
			getSettings();
		}
	});
}
