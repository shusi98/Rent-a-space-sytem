<?php

	session_start();
	$_SESSION["signedInUser"] = NULL;
	session_unset();
	session_destroy();
	header('location: index.php');
	exit;
?>