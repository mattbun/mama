<?php

class sqliteDBConnection {

	private $db_location = 'db/mama.db';

	private $db;

	private $stmt_getSetting;
	private $stmt_addArtist;
	private $stmt_addTrack;
	private $stmt_connectAlbumTrack;
	private $stmt_connectArtistAlbum;
	private $stmt_countAlbumIDsByArtist;
	private $stmt_countTrackIDsByAlbum;

	public $getSetting = 1;
	public $setSetting = 2;

	//Connect to database and prepare statements that will be used.
	function connectAndPrepare(){
		$this->connect();
		$this->prepare();
	}

	function connect(){
		$this->db = new SQLite3($this->db_location) or die ("Failed to connect to database.\n");
	}

	function disconnect(){
		$db->close() or die ("failed to close database connection.\n");
	}

	function prepare(){
		$this->stmt_addArtist = $this->db->prepare('INSERT INTO Artist (name, sort_name, date_added) SELECT :name, :sort_name, CURRENT_TIMESTAMP WHERE NOT EXISTS (SELECT 1 FROM Artist WHERE name = :name);') 
			or die ("Failed to prepare 'addArtist' statement.");
		$this->stmt_addTrack = $this->db->prepare('INSERT INTO TRACK (name, path, track_no, track_total, disc_no, disc_total, genre, date_added) SELECT :name, :path, :track_no, :track_total, :disc_no, :disc_total, :genre, CURRENT_TIMESTAMP WHERE NOT EXISTS (SELECT 1 FROM TRACK WHERE path = :path); SELECT last_insert_rowid();') 
			or die ("Failed to prepare 'addTrack' statement.\n");
	}


	function getSetting($key){
		$this->stmt_getSetting = $this->db->prepare('SELECT value FROM Settings WHERE key = ?')
			or die("Failed to prepare 'getSetting' statement\n");
		$this->stmt_getSetting->bindValue(1, $key, SQLITE3_TEXT);

		$result = $this->stmt_getSetting->execute()
			or die ("Failed to execute statement to get setting\n");

		return $result->fetchArray()[0];
	}


	function setSetting($key, $newvalue){
		 
		$stmt = $this->db->prepare('SELECT count(*) FROM Settings WHERE key = ?')
			or die("Failed to prepare statement\n");
		$stmt->bindValue(1, $key, SQLITE3_TEXT);

		$result = $stmt->execute()
			or die("Failed to execute statement\n");
		$arr = $result->fetchArray();
		
		if ($arr[0] > 0){
			$stmt = $this->db->prepare('UPDATE Settings SET value = ? WHERE key = ?') 
				or die("Failed to prepare statement");
			
			$stmt->bindValue(1, $newvalue, SQLITE3_TEXT);
			$stmt->bindValue(2, $key, SQLITE3_TEXT);
			
			$result = $stmt->execute()
				or die("Failed to execute statement");
		}
		else {
			$stmt = $db->prepare('INSERT INTO Settings (key, value) VALUES (?, ?)') 
				or die("Failed to prepare statement");
			
			$stmt->bindValue(1, $key, SQLITE3_TEXT);
			$stmt->bindValue(2, $newvalue, SQLITE3_TEXT);
			
			$result = $stmt->execute()
				or die("Failed to execute statement");
		}
	}


}

	
