<?php
session_start();
$filterBy = "";
if(!empty($_POST["param"]) )
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$param = mysqli_real_escape_string($con,$_POST["param"]);
	$con->close();
	if($param == "edit")
	{	
		$resultant = "editProperty.php?pid=".$_SESSION["findThisProperty"];
		echo json_encode( ["status" => 1, "divContent" => $resultant] );
		exit;
	}
	if($param == "tenant")
	{
		$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
		$stmt = $con->prepare("select UserID from rental where PropertyID = ?");
		$stmt->bind_param("s",$_SESSION["findThisProperty"]);
		$stmt->execute();
		$stmt->bind_result($userId);
		mysqli_stmt_store_result($stmt);
		$numReturnedRows = mysqli_stmt_num_rows($stmt);
		$resultant = "";
		$query=$multiQuery = "";
		while($stmt->fetch())
		{
			$query .= "select UserID,FirstName,LastName,Email,AccountActive,DateAdded from user where UserID = $userId";
		}
		$con->close();
		if($numReturnedRows < 1)
		{
			$resultant = '<div>This property is currently not occupied by any tenant.</div>';
		}
		else
		{
			$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
			$stmt = $con->prepare($query);
			$stmt->execute();
			$stmt->bind_result($userId,$firstName,$lastName,$userEmail,$accountActive,$dateJoined);
			mysqli_stmt_store_result($stmt);
			$numReturnedRows = mysqli_stmt_num_rows($stmt);
			$stmt->fetch();
			$con->close();
			$divContent = '<div>'.'Full name: '. $firstName.' '.$lastName.'<br>'.'Email Adress: '.$userEmail.'<br>'.'Account Active: '.$accountActive.'<br>'.'Tenant since: '.$dateJoined.'/<div><br>';
			$resultant .= $divContent;
		}
		echo json_encode( ["status" => 0, "divContent" => $resultant] );
		exit;
	}
	if($param == "chargeRent")
	{
		$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
		$stmt = $con->prepare("select a.Rent,a.PropertyName,b.RentalID,c.AmountDue from property a,rental b, payment c where a.PropertyID = ? and b.PropertyID = ? and c.PropertyID = ?");
		$stmt->bind_param("iss",$_SESSION["findThisProperty"],$_SESSION["findThisProperty"],$_SESSION["findThisProperty"]);
		$stmt->execute();
		$stmt->bind_result($propertyRent,$propertyName,$rentalId,$amountDue);
		mysqli_stmt_store_result($stmt);
		$numReturnedRows = mysqli_stmt_num_rows($stmt);
		$stmt->fetch();
		$con->close();
		if($numReturnedRows < 1)
		{
			$resultant = '<div>No rent record was found for this property.</div>';
		}
		else
		{
			$totalDue = $propertyRent + $amountDue;
			$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
			$dueDate = date("Y-m-d", strtotime("+7 day"));
			$totalPaid = 0;
			$stmt = $con->prepare("update payment set AmountDue = ?, AmountPaid = ?, DateDue = ? where PropertyID = ?");
			$stmt->bind_param("iiss",$totalDue,$totalPaid,$dueDate,$_SESSION["findThisProperty"]);
			if($stmt->execute())
			{
				$con->close();
				$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
				$stmt = $con->prepare("select a.UserID,b.Email from rental a, user b where a.PropertyID = ? and a.UserID = b.UserID");
				$stmt->bind_param("i",$_SESSION["findThisProperty"]);
				if($stmt->execute())
				{
					$stmt->bind_result($userId,$userEmail);
					$stmt->fetch();
					$con->close();
					$message = ' Good day. This a reminder that your rental of property '.$propertyName.' at www.Rent-A-Space.co.za needs to be settled an amount of R '.$propertyRent.' by '.$dueDate.'. Please attend to the matter ASAP.   Thank you.';
					if(mail($userEmail, "Rent Due", $message, $NO_REPLY_EMAIL_ADDRESS))
					{
						$resultant = '<div>Monthly rent of R '.$propertyRent.' due on '.$dueDate.' charged successfully. A notification email was also sent to the relevent tenant</div>';
					}
					else
					{
						$resultant = '<div>Monthly rent of R '.$propertyRent.' due on '.$dueDate.' charged successfully. A notification email could NOT be sent to the relevent tenant</div>';
					}
				}
			}
			else
			{
				$con->close();
				$resultant = '<div>An error occured, please try again. </div>';
			}
		}
		
		echo json_encode( ["status" => 0, "divContent" => $resultant] );
		exit;
	}
}
?>