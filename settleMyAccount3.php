<?php
include_once 'validateTenant.php';
include_once 'Constants.php';
$divContent = "";
if(isset($_GET['pid']) && !empty($_GET['pid']))
{
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$payId = mysqli_real_escape_string($con,$_GET["pid"]);

	if(!$con->connect_error)
	{
		$stmt = $con->prepare("select a.PropertyID,a.PropertyName,a.Address,b.RentFrom,b.RentTo,c.AmountDue,c.DateDue,d.Email from property a, rental b, payment c,user d where b.UserID = ? and  a.PropertyID = b.PropertyID and c.PropertyID = b.PropertyID and c.PaymentID = ? and d.UserID = ?");
		$stmt->bind_param("sis",$_SESSION["UserID"],$payId,$_SESSION["UserID"]);
		if($stmt->execute())
		{
			$stmt->bind_result($propertyId,$propertyName,$address,$rentFrom,$rentTo,$amountDue,$dateDue,$userEmail);
			mysqli_stmt_store_result($stmt);
			$numReturnedRows = mysqli_stmt_num_rows($stmt);
			$stmt->fetch();
			$con->close();
			if($numReturnedRows > 0)
			{
				$amountPaid = $amountDue;
				$amountDue = 0;
				$dateDue = NULL;
				$datePaid = date("Y-m-d");
				$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
				$stmt = $con->prepare("update payment set AmountDue = ? , AmountPaid = ?, DateDue = ?, DatePaid = ? where PaymentID = ? and PropertyID = ?");
				$stmt->bind_param("iissss",$amountDue,$amountPaid,$dateDue,$datePaid,$payId,$propertyId);
				if($stmt->execute())
				{
					$con->close();
					$message = ' Good day. This a payment confirmation for settlement of rent on property '.$propertyName.' at www.Rent-A-Space.co.za . An amount of R '.$amountPaid.' was successfully received on '.$datePaid.'.    Thank you for settling you rent.';
					if(mail($userEmail, "Rent Payment", $message, $NO_REPLY_EMAIL_ADDRESS))
					{
						$divContent = '<div>Rent payment of R '.$amountPaid.' successful. A notification email was also sent to you.</div>';
					}
					else
					{
						$divContent = '<div>Rent payment of R '.$amountPaid.' successful. A notification email could NOT be sent to you</div>';
					}
				}
			}
			else
			{
				$con->close();
				$divContent = '<div class="d-flex"	style="margin-top:15px;">Oops, something went wrong. Please go back to the previous page and try again.</div>';
				
			}
		}
		else
		{
			$con->close();
			$divContent = '<div class="d-flex"	style="margin-top:15px;">Oops, something went wrong. Please go back to the previous page and try again.</div>';
			
		}
	}
}
else
{
	$divContent = '<div class="d-flex"	style="margin-top:15px;">Oops, something went wrong. Please go back to the previous page and try again.</div>';
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Rent Settlement</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Settle your dues</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="myRentals.php">My Rentals</a></li>
				<li><a href="javascript:void()">Pay Rent</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<?php echo $divContent;?>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
</body>
</html>