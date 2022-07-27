<?php
include_once 'validateAdmin.php';

$errorMessage="";

$pageDescription=$pageUpperNiche=$pageTitle="";

if( isset($_GET['aid']) && !empty($_GET['aid']) && isset($_GET['uid']) && !empty($_GET['uid']) && isset($_GET['des']) && !empty($_GET['des']) )
{
	$pageTitle = "Application";
	$pageDescription = "Approve tenant application";
	$pageUpperNiche = '<li><a href="viewApplications.php">Tenant Applications</a></li>';
	
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$applicationId = mysqli_real_escape_string($con,$_GET["aid"]);
	$userId = mysqli_real_escape_string($con,$_GET["uid"]);
	$decision = mysqli_real_escape_string($con,$_GET["des"]);
	
	$stmt = $con->prepare("select a.PropertyID,a.PropertyName,a.Address,a.Rent,b.DateApplied,c.Email from property a, application b, user c where b.ApplicationID = ? and c.UserID = ? and b.PropertyID = a.PropertyID");
	$stmt->bind_param("ii",$applicationId,$userId);
	$stmt->execute();
	$stmt->bind_result($propertyId,$propertyName,$address,$rentAmount,$dateApplied,$userEmail);
	$stmt->fetch();
	$con->close();
	
	$multiQuery = "";
	$multiQuery .= "update application set ApplicationActive = '0' where ApplicationID = '$applicationId';";
	if($decision == '1')
	{
		$rentFrom = date("Y-m-d");
		$rentTo = date("Y-m-d", strtotime("+6 month"));
		$status = "No";
		$multiQuery .= "insert into rental(PropertyID,UserID,RentFrom,RentTo,Address) values('$propertyId','$userId','$rentFrom','$rentTo','$address');";
		$multiQuery .= "update property set Vaccant = '$status' where PropertyID = '$propertyId';";
		$message = 'Good day. Congratulations, your application made on '.substr($dateApplied,0,10).' to occupy the space '.$propertyName.' at '.$address.' is successful. Rent for this space is R '.$rentAmount.'. Rent-A-Space looks forward to a long partnership with you.     Thank you.';
	}
	if($decision == '2')
	{
		$message = 'Good day. Rent-A-Space regrets to inform you that your application made on '.substr($dateApplied,0,10).' to occupy the space '.$propertyName.' at '.$address.' is unsuccessful. Keep checking our website for more space.     Thank you.';
	}
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	if ($con->multi_query($multiQuery) === TRUE)
	{
		$con->close();
		if(mail($userEmail, "Application to rent space", $message, $NO_REPLY_EMAIL_ADDRESS))
		{
			$errorMessage = "Application resolved successfully, tenant will receive relevant email";
			header( "Refresh:3.5; url=viewApplications.php", true, 303);
		}
		else
		{
			$errorMessage = "Application resolved successfully, email could not be sent";
			header( "Refresh:3.5; url=viewApplications.php", true, 303);
		}
	}
	else
	{
		$con->close();
		$errorMessage = "Failed. Please go to the previous page and try again";
		header( "Refresh:3.5; url=viewApplications.php", true, 303);
		//echo $multiQuery."<br>Error: " . $con->error;
	}
}
else if( isset($_GET['rid']) && !empty($_GET['rid']) && isset($_GET['des']) && !empty($_GET['des']) )
{
	$pageTitle = "Terminate Rental";
	$pageDescription = "Terminate this rental";
	$pageUpperNiche = '<li><a href="rentals.php">Rentals</a></li>';
	
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$rentalId = mysqli_real_escape_string($con,$_GET["rid"]);
	$decision = mysqli_real_escape_string($con,$_GET["des"]);
	if($decision =='1')
	{
		$stmt = $con->prepare("select PropertyID from rental where RentalID = $rentalId");
		if($stmt->execute())
		{
			$stmt->bind_result($propertyId);
			$stmt->fetch();
			$con->close();
			$multiQuery = "update payment set AmountDue = '0', DateDue = 'NULL' where PropertyID = '$propertyId';"
			$multiQuery .= "delete from rental where RentalID = '$rentalId';";
			$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
			if ($con->multi_query($multiQuery) === TRUE)
			{
				$errorMessage = "Property deleted";
				header( "Refresh:3.5; url=rentals.php", true, 303);
			}
		}
	}
}
else if( isset($_GET['act']) && !empty($_GET['act']) && isset($_GET['pid']) && !empty($_GET['pid']) )
{
	$pageTitle = "Delete Property";
	$pageDescription = "Delete this property";
	$pageUpperNiche = '<li><a href="listedProperties.php">Properties</a></li>';
	
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$propertyId = mysqli_real_escape_string($con,$_GET["pid"]);
	$action = mysqli_real_escape_string($con,$_GET["act"]);
	if($action == "del")
	{
		$multiQuery = "delete from property where PropertyID = '$propertyId';delete from rental where PropertyID = '$propertyId';delete from payment where PropertyID = '$propertyId';";
		if (mysqli_multi_query($con, $multiQuery))
		{
			mysqli_close($con);
			$errorMessage = "Property successfully deleted";
			header( "Refresh:3.5; url=listedProperties.php", true, 303);
		}
		else
		{
			mysqli_close($con);
			$errorMessage = "Error in deleting the mentioned property. Please navigate to the previous page and try again.";
			header( "Refresh:3.5; url=deleteProperty.php?pid=$propertyId", true, 303);
		}
		echo '';

	}
}
else if( isset($_GET['act']) && !empty($_GET['act']) && isset($_GET['tid']) && !empty($_GET['tid']) )
{
	$pageTitle = "Delete Tenant";
	$pageDescription = "Delete this tenant";
	$pageUpperNiche = '<li><a href="tenants.php">Tenants</a></li>';
	
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$tenantId = mysqli_real_escape_string($con,$_GET["tid"]);
	$action = mysqli_real_escape_string($con,$_GET["act"]);
	if($action == "del")
	{
		if($_SESSION["UserCurrentlyRenting"] == true)
		{
			$rentalId = $_SESSION["RentingRentalID"];
			$propertyId = $_SESSION["RentingPropertyID"];
			$multiQuery = "update payment set AmountDue = '0', DateDue = 'NULL' where PropertyID = '$propertyId';delete from rental where PropertyID = '$propertyId';delete from user where UserID = '$tenantId';"
		}
		else
		{
			$multiQuery = "delete from user where UserID = '$tenantId';"
		}
		
		if (mysqli_multi_query($con, $multiQuery))
		{
			mysqli_close($con);
			$errorMessage = "Tenant successfully deleted";
			header( "Refresh:3.5; url=tenants.php", true, 303);
		}
		else
		{
			mysqli_close($con);
			$errorMessage = "Error in deleting the mentioned tenant. Please navigate to the previous page and try again.";
			header( "Refresh:3.5; url=deleteTenant.php?uid=$tenantyId", true, 303);
		}

	}
}
else
{
	header('location: dashboard.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rent-A-Space | <?php echo $pageTitle;?></title>

<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

  <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
<div class="container">
	<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
	<div class="d-flex" style="margin-top:15px;">
		<h3><?php echo $pageDescription;?>A little more about this property...</h3>
	</div>
	<div class="d-flex" style="margin-top:15px;">
		<ul class="breadcrumb">
			<li><a href="index.php">Home</a></li>
			<li><a href="dashboard.php">Dashboard</a></li>
			<?php echo $pageUpperNiche;?>
			<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
		</ul>
	</div>
	<div style="margin-top:15px;">
		<span class="error"><?php echo $errorMessage;?></span>
	</div>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
</body>
</html>