<?php

require_once("../php/db.php");

$response = array();
$dbconn = dbConnect();

foreach ($_GET as $setting_key){
	$response[$setting_key] = getSetting($dbconn, $setting_key);
}

dbDisconnect($dbconn);

echo json_encode($response);

?>
