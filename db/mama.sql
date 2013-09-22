-- Create Table: Track
--------------------------------------------------------------------------------
CREATE TABLE Track
(
	id SERIAL
	,name VARCHAR(250)  NULL 
	,path TEXT  NULL 
	,other_artist VARCHAR(250) NULL
	,year INTEGER NULL
	,track_no INTEGER  NULL 
	,track_total INTEGER  NULL 
	,disc_no INTEGER  NULL 
	,disc_total INTEGER  NULL 
	,genre VARCHAR(250)  NULL 
	,date_added TIMESTAMP  NULL 
	,CONSTRAINT PK_Track_id PRIMARY KEY (id)
);



-- Create Table: AlbumTrackMap
--------------------------------------------------------------------------------
CREATE TABLE AlbumTrackMap
(
	id SERIAL
	,album_id INTEGER  NULL 
	,track_id INTEGER  NULL 
	,CONSTRAINT PK_AlbumTrackMap_id PRIMARY KEY (id)
);



-- Create Table: Artist
--------------------------------------------------------------------------------
CREATE TABLE Artist
(
	id SERIAL
	,name VARCHAR(250)  NULL 
	,sort_name VARCHAR(250) NULL
	,date_added TIMESTAMP  NULL 
	,CONSTRAINT PK_Artist_id PRIMARY KEY (id)
);



-- Create Table: ArtistAlbumMap
--------------------------------------------------------------------------------
CREATE TABLE ArtistAlbumMap
(
	id SERIAL
	,artist_id INTEGER  NULL 
	,album_id INTEGER  NULL 
	,CONSTRAINT PK_ArtistAlbumMap_id PRIMARY KEY (id)
);



-- Create Table: Album
--------------------------------------------------------------------------------
CREATE TABLE Album
(
	id SERIAL
	,name VARCHAR(250)  NULL 
	,year INTEGER  NULL 
	,date_added TIMESTAMP  NULL 
	,CONSTRAINT PK_Album_id PRIMARY KEY (id)
);



-- Create Table: Settings
--------------------------------------------------------------------------------
CREATE TABLE Settings
(
	id SERIAL
	,key VARCHAR(250)  NULL 
	,value VARCHAR(250)  NULL 
	,CONSTRAINT PK_Settings_id PRIMARY KEY (id)
);



-- Create Foreign Key: ArtistAlbumMap.album_id -> Album.id
ALTER TABLE ArtistAlbumMap ADD CONSTRAINT FK_ArtistAlbumMap_album_id_Album_id FOREIGN KEY (album_id) REFERENCES Album(id);


-- Create Foreign Key: AlbumTrackMap.track_id -> Track.id
ALTER TABLE AlbumTrackMap ADD CONSTRAINT FK_AlbumTrackMap_track_id_Track_id FOREIGN KEY (track_id) REFERENCES Track(id);


-- Create Foreign Key: AlbumTrackMap.album_id -> Album.id
ALTER TABLE AlbumTrackMap ADD CONSTRAINT FK_AlbumTrackMap_album_id_Album_id FOREIGN KEY (album_id) REFERENCES Album(id);


-- Create Foreign Key: ArtistAlbumMap.artist_id -> Artist.id
ALTER TABLE ArtistAlbumMap ADD CONSTRAINT FK_ArtistAlbumMap_artist_id_Artist_id FOREIGN KEY (artist_id) REFERENCES Artist(id);




