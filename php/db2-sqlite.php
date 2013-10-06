<?php

include 'db-utils.php';

/*
 * All SQL drivers (if more are made) should follow this class structure. If a 
 * user wishes to use a different type of database they should just change the 
 * include statement in db.php
 */
class DBConnection {

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

	// Connect to database and prepare statements that will be used.
	// TODO Be able to specify a subset of statements to prepare
	function connectAndPrepare(){
		$this->connect();
		$this->prepare();
	}

	// Connect to the sqlite database
	function connect(){
		$this->db = new SQLite3($this->db_location) or die ("Failed to connect to database.\n");
	}

	// Disconnect from sqlite database
	function disconnect(){
		$db->close() or die ("failed to close database connection.\n");
	}

	// Prepare statements
	// TODO Be able to specify a subset of statements to prepare
	function prepare(){
		$this->stmt_addArtist = $this->db->prepare('INSERT INTO Artist (name, sort_name, date_added) SELECT :name, :sort_name, CURRENT_TIMESTAMP WHERE NOT EXISTS (SELECT 1 FROM Artist WHERE name = :name); SELECT id FROM Artist WHERE name = :name') 
			or die ("Failed to prepare 'addArtist' statement.");
		$this->stmt_addTrack = $this->db->prepare('INSERT INTO TRACK (name, path, track_no, track_total, disc_no, disc_total, genre, date_added) SELECT :name, :path, :track_no, :track_total, :disc_no, :disc_total, :genre, CURRENT_TIMESTAMP WHERE NOT EXISTS (SELECT 1 FROM TRACK WHERE path = :path); SELECT last_insert_rowid();') 
			or die ("Failed to prepare 'addTrack' statement.\n");


	}

	// Get the settings value for a specific key
	function getSetting($key){
		$this->stmt_getSetting = $this->db->prepare('SELECT value FROM Settings WHERE key = ?')
			or die("Failed to prepare 'getSetting' statement\n");
		$this->stmt_getSetting->bindValue(1, $key, SQLITE3_TEXT);

		$result = $this->stmt_getSetting->execute()
			or die ("Failed to execute statement to get setting\n");

		return $result->fetchArray()[0];
	}

	// Modify an existing setting or add a new one
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


	// Adds an artist to the database. Returns the artist id of the newly added or
	// existing artist
	// $input - An array with the name of the artist at $input[0]
	function addArtist($input){
		$input[] = createSortName($input[0]);
		
		$this->stmt_addArtist->bindValue(':name', $input[0], SQLITE3_TEXT); //Name of the artist
		$this->stmt_addArtist->bindValue(':sort_name', $input[1], SQLITE3_TEXT); //Sort name of the artist (lower case and no 'the's)

		$result = $this->stmt_addArtist->execute();

		echo $this->db->querySingle('SELECT id FROM Artist WHERE name = "' . $input[0] . '";');

		$artist_id = $result->fetchArray()[0];;

		return $artist_id;
	}

}

	
