<?php
session_start();
$filterBy = "";
if(!empty($_POST["param"]) )
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$param = mysqli_real_escape_string($con,$_POST["param"]);
	if($param == "edit")
	{
		$con->close();
		$resultant = "editTenant.php?tid=".$_SESSION["findThisUser"];
		echo json_encode( ["status" => 1, "divContent" => $resultant] );
		exit;
	}
	if($param == "properties")
	{
		$stmt = $con->prepare("select distinct property.PropertyID,PropertyName,RentFrom,Description,Rent,property.Address,Province,City from property, rental where property.PropertyID = rental.PropertyID and rental.UserID = ?");
		//$stmt = $con->prepare("select PropertyID from rental where UserID = ?");
		//$userId = (string)$_SESSION["findThisUser"];
		$stmt->bind_param("s",$_SESSION["findThisUser"]);
		$stmt->execute();
		$stmt->bind_result($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7]);
		mysqli_stmt_store_result($stmt);
		$numReturnedRows = mysqli_stmt_num_rows($stmt);
		$resultant = "";
		$multiQuery = "";
		while($stmt->fetch())
		{
			$divContent = '<div>Property name: <a href="property.php?pid='.$row[0].'">'.$row[1].'</a><br>'.'Renting Since: '.$row[2].'<br>'.'Description: '.$row[3].'<br>'.'Rent: R '.$row[4].'<br>'.'Address: '.$row[5].'<br>'.'City: '.$row[7].'<br>'.'Province: '.$row[6].'</div><br><br>';
			$resultant .= $divContent;
			//$multiQuery .= "select PropertyID,PropertyName,Vaccant,Description,Rent,Address,Province,City from property where PropertyID = '$propertyId';";
		}
		$con->close();
		if($numReturnedRows < 1)
		{
			$resultant = '<div>This user is currently not occupying any property.</div>';
		}
		/*else
		{
			$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
			if (mysqli_multi_query($con, $multiQuery))
			{
				do
				{
					// Store first result set
					if ($result = mysqli_store_result($con))
					{
						while ($row = mysqli_fetch_row($result))
						{
							$divContent = '<div>Property name: <a href="property.php?pid='.$row[0].'">'.$row[1].'</a><br>'.'Vaccant: '.$row[2].'<br>'.'Description: '.$row[3].'<br>'.'Rent: R '.$row[4].'<br>'.'Address: '.$row[5].'<br>'.'City: '.$row[7].'<br>'.'Province: '.$row[6].'</div><br><br>';
							$resultant .= $divContent;
						}
						mysqli_free_result($result);
					}
					// if there are more result-sets, the print a divider
					/*if (mysqli_more_results($con))
					{
						$resultant .= "-----------------------------";
					}*/
					//Prepare next result set
				/*}
				while (mysqli_more_results($con));
			}
			mysqli_close($con);
		}*/
		echo json_encode( ["status" => 0, "divContent" => $resultant] );
		exit;
	}
}
?>