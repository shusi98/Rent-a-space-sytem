<?php
$filterBy = "";
if(!empty($_POST["sortBy"]) && !empty($_POST["findBy"]) && !empty($_POST["region"]) )
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	
	$sortBy = mysqli_real_escape_string($con,$_POST["sortBy"]);
	$findBy = mysqli_real_escape_string($con,$_POST["findBy"]);
	$region = mysqli_real_escape_string($con,$_POST["region"]);
	
	if($sortBy == "all")
	{
		$filterBySort = " order by PropertyID asc";
	}
	elseif($sortBy == "highest")
	{
		$filterBySort = " order by Rent asc";
	}
	elseif($sortBy == "lowest")
	{
		$filterBySort = " order by Rent desc";
	}
	elseif($sortBy == "nameAsc")
	{
		$filterBySort = " order by PropertyName asc";
	}
	elseif($sortBy == "nameDesc")
	{
		$filterBySort = " order by PropertyName desc";
	}
	elseif($sortBy == "dateAsc")
	{
		$filterBySort = " order by DateAdded asc";
	}
	elseif($sortBy == "dateDesc")
	{
		$filterBySort = " order by DateAdded desc";
	}
	
	
	if($findBy == "all")
	{
		$filterByFind = "";
	}
	elseif($findBy == "vaccant")
	{
		$filterByFind = " where Vaccant = 'Yes'";
	}
	elseif($findBy == "occupied")
	{
		$filterByFind = " where Vaccant = 'No'";
	}
	elseif($findBy == "search")
	{
		$searchString = mysqli_real_escape_string($con,$_POST["searchString"]);
		$filterByFind = " where PropertyName like '%$searchString%' or Address like '%$searchString%' or Description like '%$searchString%'";
	}
	
	if($region == "all")
	{
		$filterByRegion = "";
	}
	else
	{
		if($findBy == "all")
		{
			$filterByRegion = " where Province = '".$region."'";
		}
		else
		{
			$filterByRegion = " and Province = '".$region."'";
		}
	}
	
	
	if($findBy == "rentDue")
	{
		$filterByFind = " where AmountDue > 1";
		$query = "select distinct property.PropertyID,PropertyName,Vaccant,Description,Rent,Address,DateAdded from property,payment" . $filterByFind.$filterByRegion.$filterBySort;
	}
	else
	{
		$query = "select PropertyID,PropertyName,Vaccant,Description,Rent,Address,DateAdded from property" . $filterByFind.$filterByRegion.$filterBySort;
	}
	
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$stmt = $con->prepare($query);
	//$stmt->bind_param("s",$email);
	/*echo json_encode( ["status" => 0, "divContent" => $query] );
	exit;*/
	$stmt->execute();
	$stmt->bind_result($propertyId,$propertyName,$vaccant,$description,$rent,$address,$dateAdded);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
	$resultant = "";
	while($stmt->fetch())
	{
	    $divContent = '<div style="margin-top:15px;">Property Name: <a href="property.php?pid='.$propertyId.'">'.$propertyName.'</a><br>Description: '.$description.'<br>Vaccant: '.$vaccant.'<br>Rent: R '.$rent.'<br>Address: '.$address.'<br>Date Added: '.$dateAdded.'</div>';
		/*$divContent = '<tr>
		<td><a href="property.php?pid='.$propertyId.'">'.$propertyName.'</a></td>
		<td>'.$vaccant.'</td>
		<td>'.$description.'</td>
		<td>'.$rent.'</td>
		<td>'.$address.'</td>
		<td>'.substr($dateAdded,0,10).'</td>dateAdded
		</tr>';*/
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