<?php
$filterBy = "";
if(!empty($_POST["param"]) )
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$param = mysqli_real_escape_string($con,$_POST["param"]);
	if($param == "all")
	{
		$filterBy = " order by PropertyID asc";
	}
	elseif($param == "highest")
	{
		$filterBy = " order by Rent asc";
	}
	elseif($param == "lowest")
	{
		$filterBy = " order by Rent desc";
	}
	else
	{
		$filterBy = " and Province = '".$param."'";
	}
	$query = "select PropertyID,PropertyName,Vaccant,Description,Rent,Address from property where Vaccant = 'Yes' and Visible = 'Yes'" . $filterBy;
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$stmt = $con->prepare($query);
	//$stmt->bind_param("s",$email);
	$stmt->execute();
	$stmt->bind_result($propertyId,$propertyName,$vaccant,$description,$rent,$address);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
	$resultant = "";
	while($stmt->fetch())
	{
		$divContent = '<tr>
		<td><a href="viewProperty.php?pid='.$propertyId.'">'.$propertyName.'</a></td>
		<td>'.$vaccant.'</td>
		<td>'.$description.'</td>
		<td>'.$rent.'</td>
		<td>'.$address.'</td>
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