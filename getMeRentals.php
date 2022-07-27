<?php
$filterBy = "";
if(!empty($_POST["param"]) )
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$param = mysqli_real_escape_string($con,$_POST["param"]);
	if($param == "all")
	{
		$filterBy = " where a.PropertyID = b.PropertyID and a.UserID = c.UserID order by a.PropertyID asc";
	}
	elseif($param == "highest")
	{
		$filterBy = " where a.PropertyID = b.PropertyID and a.UserID = c.UserID order by b.Rent asc";
	}
	elseif($param == "lowest")
	{
		$filterBy = " where a.PropertyID = b.PropertyID and a.UserID = c.UserID order by b.Rent desc";
	}
	else
	{
		$filterBy = " where a.PropertyID = b.PropertyID and a.UserID = c.UserID  and b.Province = '".$param."'";
	}
	$query = "select a.RentalID,a.PropertyID,a.RentFrom,a.RentTo,b.PropertyName,b.Description,b.Rent,b.Address,c.FirstName,c.LastName from rental a, property b, user c" . $filterBy;
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$stmt = $con->prepare($query);
	//$stmt->bind_param("s",$email);
	$stmt->execute();
	$stmt->bind_result($rentalId,$propertyId,$rentFrom,$rentTo,$propertyName,$description,$rentAmount,$address,$firstName,$lastName);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
	$resultant = "";
	while($stmt->fetch())
	{
		$divContent = '<div style="margin-top:15px;">Property Name: <a href="property.php?pid='.$propertyId.'">'.$propertyName.'</a><br>Tenant Name: '.$firstName.' '.$lastName.'<br>Description: '.$description.'<br>Rent: R '.$rentAmount.'<br>Renting From: '.$rentFrom.'<br>Renting Until: '.$rentTo.'<br>Address: '.$address.' <a href="takeThisAction.php?rid='.$rentalId.'&des=1">terminate rental</a></div>';
		$resultant .= $divContent;
	}
	$con->close();
	if($numReturnedRows < 1)
	{
		$resultant = '<div style="margin-top:15px;">No active rentals for now...</div>';
	}
	echo json_encode( ["status" => 0, "divContent" => $resultant] );
	exit;
}
?>