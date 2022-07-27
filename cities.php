<?php
include_once 'Constants.php';
$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
$state_id = (int) $_GET['state_id'];
$sql = "SELECT * FROM city WHERE state_id=$state_id";
$result = mysqli_query($con, $sql);
	echo "<option disabled selected>Please Select City</option>";
while($row = mysqli_fetch_assoc($result)){
	echo "<option value='" . $row['name'] . "'>" . $row['name'] ."</option>";
}

?>