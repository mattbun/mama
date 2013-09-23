<?php
require_once("../php/db.php");

while ($setting = current($_GET)){
	$key = key($_GET);
	$newvalue = $setting;

	changeSetting($key, $newvalue);

	next($_GET);
}

?>
