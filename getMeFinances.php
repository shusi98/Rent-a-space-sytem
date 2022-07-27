<?php
$filterBy = "";
if(!empty($_POST["sortBy"]) && !empty($_POST["findBy"]) )
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);

	$sortBy = mysqli_real_escape_string($con,$_POST["sortBy"]);
	$findBy = mysqli_real_escape_string($con,$_POST["findBy"]);

	if(!empty($_POST["dateFrom"]) && !empty($_POST["dateTo"]) )
	{
		$dateFrom = mysqli_real_escape_string($con,$_POST["dateFrom"]);
		$dateTo = mysqli_real_escape_string($con,$_POST["dateTo"]);
		$filterByDate = " and DateDue between '$dateFrom' and '$dateTo' || DatePaid between '$dateFrom' and '$dateTo' || Created between '$dateFrom' and '$dateTo' || LastUpdate between '$dateFrom' and '$dateTo'";
	}
	else
	{
		$filterByDate = "";
	}
	
	if($sortBy == "all")
	{
		$filterBySort = " order by TransactionID asc";
	}
	elseif($sortBy == "pNameAsc")
	{
		$filterBySort = " order by PropertyName asc";
	}
	elseif($sortBy == "pNameDesc")
	{
		$filterBySort = " order by PropertyName desc";
	}
	elseif($sortBy == "tNameAsc")
	{
		$filterBySort = " order by TenantName asc";
	}
	elseif($sortBy == "tNnameDesc")
	{
		$filterBySort = " order by TenantName desc";
	}
	elseif($sortBy == "emailAsc")
	{
		$filterBySort = " order by TenantEmail asc";
	}
	elseif($sortBy == "emailDesc")
	{
		$filterBySort = " order by TenantEmail desc";
	}
	elseif($sortBy == "amountDueAsc")
	{
		$filterBySort = " order by AmountDue asc";
	}
	elseif($sortBy == "amountDueDesc")
	{
		$filterBySort = " order by AmountDue desc";
	}
	elseif($sortBy == "amountPaidAsc")
	{
		$filterBySort = " order by AmountPaid asc";
	}
	elseif($sortBy == "amountPaidDesc")
	{
		$filterBySort = " order by AmountPaid desc";
	}
	elseif($sortBy == "dateDueAsc")
	{
		$filterBySort = " order by DateDue asc";
	}
	elseif($sortBy == "dateDueDesc")
	{
		$filterBySort = " order by DateDue desc";
	}
	elseif($sortBy == "datePaidAsc")
	{
		$filterBySort = " order by DatePaid asc";
	}
	elseif($sortBy == "datePaidDesc")
	{
		$filterBySort = " order by DatePaid desc";
	}
	elseif($sortBy == "createdAsc")
	{
		$filterBySort = " order by Created asc";
	}
	elseif($sortBy == "createdDesc")
	{
		$filterBySort = " order by Created desc";
	}
	elseif($sortBy == "lastUpdateAsc")
	{
		$filterBySort = " order by LastUpdate asc";
	}
	elseif($sortBy == "lastUpdateDesc")
	{
		$filterBySort = " order by LastUpdate desc";
	}
	
	if($findBy == "all")
	{
		$filterByFind = "";
	}
	elseif($findBy == "search")
	{
		$searchString = mysqli_real_escape_string($con,$_POST["searchString"]);
		$filterByFind = " and PropertyName like '%$searchString%' or TenantName like '%$searchString%' or TenantEmail like '%$searchString%'";
	}
	
	$query = "select distinct TransactionID,PropertyID,PropertyName,TenantID,TenantName,TenantEmail,AmountDue,AmountPaid,DateDue,DatePaid,Created,LastUpdate,AccountActive from transaction, user where TenantID = UserID" . $filterByFind.$filterByDate.$filterBySort;
	
	
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$stmt = $con->prepare($query);
	$stmt->execute();
	$stmt->bind_result($traceId,$propertyId,$propertyName,$tenantId,$tenantName,$tenantEmail,$amountDue,$amountPaid,$dateDue,$datePaid,$dateCreated,$lastUpdate,$accountActive);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
	$resultant = "";
	while($stmt->fetch())
	{
		$divContent = '<tr>
		<td>'.$traceId.'</td>
		<td><a href="property.php?pid='.$propertyId.'">'.$propertyName.'</a></td>
		<td><a href="tenant.php?tid='.$tenantId.'">'.$tenantName.'</a></td>
		<td>'.$tenantEmail.'</td>
		<td>'.$accountActive.'</td>
		<td>'.$amountDue.'</td>
		<td>'.$amountPaid.'</td>
		<td>'.$dateDue.'</td>
		<td>'.substr($datePaid,0,16).'</td>
		<td>'.substr($dateCreated,0,16).'</td>
		<td>'.substr($lastUpdate,0,16).'</td>
		</tr>';
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