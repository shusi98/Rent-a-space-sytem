<?php
session_start();
if(!($_SESSION["signedInUser"] == "adminSignedIn"))
{
	header('location: index.php');
}
?>