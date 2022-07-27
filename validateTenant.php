<?php
session_start();
if(!($_SESSION["signedInUser"] == "tenantSignedIn"))
{
	header('location: index.php');
}
?>