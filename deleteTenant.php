<?php
include_once 'validateAdmin.php';
include_once 'Constants.php';

if(isset($_GET['uid']) && !empty($_GET['uid']))
{
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);

	$usrId = mysqli_real_escape_string($con,$_GET["uid"]);

	if(!$con->connect_error)
	{
		$stmt = $con->prepare("select UserID,FirstName,LastName,Email,AccountActive,DateAdded from user where UserID = ?");
		$stmt->bind_param("i",$usrId);
		$stmt->execute();
		$stmt->bind_result($userId,$firstName,$lastName,$userEmail,$accountActive,$dateJoined);
		mysqli_stmt_store_result($stmt);
		$numReturnedRowsQ1 = mysqli_stmt_num_rows($stmt);
		$stmt->fetch();
		$con->close();
		if($numReturnedRowsQ1 > 0)
		{
			$divContent = 	'<div class="d-flex" style="margin-top:15px;">
								Are you sure you want to delete this tenant from your system?<br><br>
								Tenant name: '. $firstName.' '.$lastName.'<br>Email: '.$userEmail.'<br>Account Activity: '.$accountActive.'<br>Date Joined: '.substr($dateJoined,0,19).'
							</div>';
			if($accountActive == "Yes")
			{
				$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
				$stmt = $con->prepare("select a.RentalID,a.RentFrom,a.RentTo,b.PaymentID,b.AmountDue,b.DateDue,c.PropertyID,c.PropertyName,c.Rent from rental a, payment b, property c where a.UserID = ? and a.PropertyID = c.PropertyID and a.PropertyID = b.PropertyID");
				$stmt->bind_param("s",$userId);
				$stmt->execute();
				$stmt->bind_result($rentalId,$rentFrom,$rentTo,$paymentId,$amountDue,$dueDate,$propertyId,$propertyName,$rent);
				mysqli_stmt_store_result($stmt);
				$numReturnedRowsQ2 = mysqli_stmt_num_rows($stmt);
				$_SESSION["RentingPropertyID"] = array();
				while($stmt->fetch())
				{
					array_push($_SESSION["RentingPropertyID"],$propertyId);
					$divContent .= 	'<div style="margin-top:15px;">
										<span class="error">This tenant is currently renting out property '.$propertyName.' until '.$rentTo.' for R '.$rent.' monthly.</span>
									</div>';
					if($amountDue > 0)
					{
						$divContent .= 	'<div>
											<span class="error">And has rent worth R '.$amountDue.' due by '.$dueDate.'</span>
										</div>';
					}
				}
				$con->close();
				if($numReturnedRowsQ2 > 0)
				{
					$_SESSION["UserCurrentlyRenting"] = true;
				}
			}
			else
			{
				$divContent .= 	'<div style="margin-top:15px;">
									This tenant&#39;s account is currently DEACTIVATED.
								</div>';
			}
			$divContent .= 	'<div style="margin-top:15px;">
									<a href="takeThisAction.php?act=del&tid='.$userId.'"><button class="btn btn-success">DELETE TENANT</button></a>
							</div>';
		}
		else
		{
			$divContent = 	'<div class="d-flex" style="margin-top:15px;">
								Tenant not found.
							</div>';
		}
	}
}
else
{
	header('location: tenants.php');
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Delete Tenant</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Delete this Tenant...</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="tenants.php">Tenants</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<?php
			echo $divContent;
		?>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
</body>
</html>