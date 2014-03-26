<?php
class DButil {
	const MYSQL_SERVER_FILESERVER		= "";
	const MYSQL_USER_NAME_FILESERVER 	= "";
	const MYSQL_PASSWORD_FILESERVER 	= "";
	const DB_NAME_FILESERVER 			= "";
	
	const MYSQL_SERVER_STREAMER		= "";
	const MYSQL_USER_NAME_STREAMER 	= "";
	const MYSQL_PASSWORD_STREAMER	= "";
	const DB_NAME_STREAMER			= "";
	
	
	static function DBconnection($connection, $database) {
		$result = false;
		//Refering to $con declared eralier
		//global $connection;
		//Check DB connection
		if ($connection->connect_error) { die('Connect Error: ' . $connection->connect_error); }
	
		else {
			//Refering to $DB_NAME declared earlier
			//Select DB
			//global $DB_NAME;
			$DB_selected = $connection->select_db($database/*DButil::DB_NAME*/);
			if (!$DB_selected) { die ('Can\'t use : ' . $connection->connect_error); }
			else { $result = true; }
		}
		return $result;
	}
	
	static function connectToFileserver() {
		return new mysqli(DButil::MYSQL_SERVER_FILESERVER, DButil::MYSQL_USER_NAME_FILESERVER, DButil::MYSQL_PASSWORD_FILESERVER, DButil::DB_NAME_FILESERVER);
	}
	
	static function connectToStreamer() {
		return new mysqli(DButil::MYSQL_SERVER_STREAMER, DButil::MYSQL_USER_NAME_STREAMER, DButil::MYSQL_PASSWORD_STREAMER, DButil::DB_NAME_STREAMER);
	}
}
?>
