<?php
$filterBy = "";
if(!empty($_POST["sortBy"]) && !empty($_POST["findBy"]) )
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	
	$sortBy = mysqli_real_escape_string($con,$_POST["sortBy"]);
	$findBy = mysqli_real_escape_string($con,$_POST["findBy"]);
	
	
	if($sortBy == "all")
	{
		$filterBySort = " order by UserID asc";
	}
	elseif($sortBy == "nameAsc")
	{
		$filterBySort = " order by LastName asc";
	}
	elseif($sortBy == "nameDesc")
	{
		$filterBySort = " order by LastName desc";
	}
	elseif($sortBy == "emailAsc")
	{
		$filterBySort = " order by Email asc";
	}
	elseif($sortBy == "emailDesc")
	{
		$filterBySort = " order by Email desc";
	}
	elseif($sortBy == "dateAsc")
	{
		$filterBySort = " order by DateAdded asc";
	}
	elseif($sortBy == "dateDesc")
	{
		$filterBySort = " order by DateAdded desc";
	}
	
	$filterByFind = "";
	if($findBy == "all")
	{
		$filterByFind = "";
	}
	elseif($findBy == "active")
	{
		$filterByFind = " and AccountActive = 'Yes'";
	}
	elseif($findBy == "inactive")
	{
		$filterByFind = " and AccountActive = 'No'";
	}
	elseif($findBy == "search")
	{
		$searchString = mysqli_real_escape_string($con,$_POST["searchString"]);
		$filterByFind = " and FirstName like '%$searchString%' or LastName like '%$searchString%' or Email like '%$searchString%'";
	}
	
	$query = "select UserID,FirstName,LastName,Email,DateAdded,AccountActive from user where UserType = '1'" . $filterByFind.$filterBySort;
	
	if($findBy == "rentDue" || $findBy == "occupying" || $findBy == "notOccupying")
	{
		if($findBy == "rentDue")
		{
			$filterByFind = " and AccountActive = 'Yes' and user.UserID = rental.UserID and rental.PropertyID = payment.PropertyID and AmountDue > 1";
			$query = "select distinct user.UserID,FirstName,LastName,Email,a.DateAdded,AccountActive from user, payment, rental where UserType = '1'" . $filterByFind.$filterBySort;
		}
		elseif($findBy == "occupying")
		{
			$filterByFind = " and AccountActive = 'Yes' and a.UserID = b.UserID";
			$query = "select distinct a.UserID,a.FirstName,a.LastName,a.Email,a.DateAdded,a.AccountActive from user a, rental b where UserType = '1'" . $filterByFind.$filterBySort;
		}
		elseif($findByFind == "notOccupying")
		{
			$filterBy = " and AccountActive = 'Yes' and not a.UserID = b.UserID";
			$query = "select distinct a.UserID,a.FirstName,a.LastName,a.Email,a.DateAdded,a.AccountActive from user a, rental b where UserType = '1'" . $filterByFind.$filterBySort;
		}
	}
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$stmt = $con->prepare($query);
	$stmt->execute();
	$stmt->bind_result($userId,$firstName,$lastName,$userEmail,$dateAdded,$accountActive);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
	$resultant = "";
	while($stmt->fetch())
	{
		$divContent = '<div style="margin-top:15px;">Tenant Name: <a href="tenant.php?tid='.$userId.'">'.$firstName.' '.$lastName.'</a><br>Email: '.$userEmail.'<br>Date Added: '.substr($dateAdded,0,10).'<br>Account Active: '.$accountActive.'</div>';
		$resultant .= $divContent;
	}
	$con->close();
	if($numReturnedRows < 1)
	{
		$resultant = '<tr><td>Sorry, your search brought back zero (0) results.</td><tr>';
	}
	echo json_encode( ["status" => 0, "divContent" => $resultant] );
	exit;
}
?>