<?php
include 'config.php';
include 'steamlogin.php';
include 'steamdata.php';
include 'sql.php';

function dollars($cents) {
	$cents = str_pad($cents, 3, "0", STR_PAD_LEFT);
	return "$".substr($cents,0,-2).".".substr($cents,-2);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
		<meta name="viewport" content="width=device-width">
