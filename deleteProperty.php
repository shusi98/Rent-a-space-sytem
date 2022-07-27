<?php
include_once 'validateAdmin.php';
include_once 'Constants.php';

if(isset($_GET['pid']) && !empty($_GET['pid']))
{
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);

	$propId = mysqli_real_escape_string($con,$_GET["pid"]);

	if(!$con->connect_error)
	{
		$stmt = $con->prepare("select PropertyID,PropertyName,Vaccant,Description,Rent,Address,Province,City from property where PropertyID = ?");
		$stmt->bind_param("i",$propId);
		$stmt->execute();
		$stmt->bind_result($propertyId,$propertyName,$vaccant,$description,$rent,$address,$province,$city);
		mysqli_stmt_store_result($stmt);
		$numReturnedRowsQ1 = mysqli_stmt_num_rows($stmt);
		$stmt->fetch();
		$con->close();
		if($numReturnedRowsQ1 > 0)
		{
			$divContent = 	'<div class="d-flex" style="margin-top:15px;">
								Are you sure you want to delete this property from your system?<br><br>
								Property name: '. $propertyName.'<br>Vaccant: '.$vaccant.'<br>Description: '.$description.'<br>Rent: R '.$rent.'<br>Address: '.$address.'<br>City: '.$city.'<br>Province: '.$province.'
							</div>';
			if($vaccant == "No")
			{
				$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
				$stmt = $con->prepare("select a.RentalID,a.RentFrom,a.RentTo,b.PaymentID,b.AmountDue,b.DateDue,c.FirstName,c.LastName from rental a, payment b, user c where a.PropertyID = ? and a.PropertyID = b.PropertyID and a.UserID = c.UserID");
				$stmt->bind_param("s",$propertyId);
				$stmt->execute();
				$stmt->bind_result($renatlId,$rentFrom,$rentTo,$paymentId,$amountDue,$dueDate,$firstName,$lastName);
				mysqli_stmt_store_result($stmt);
				$numReturnedRowsQ2 = mysqli_stmt_num_rows($stmt);
				$stmt->fetch();
				$con->close();
				if($numReturnedRowsQ2 > 0)
				{
					$divContent .= 	'<div style="margin-top:15px;">
										<span class="error">This property is currently being rented out by '.$firstName.' '.$lastName.' until '.$rentTo.' for R '.$rent.' monthly.</span>
									</div>';
					if($amountDue > 0)
					{
						$divContent .= 	'<div>
											<span class="error">And has rent worth R '.$amountDue.' due by '.$dueDate.'</span>
										</div>';
					}
				}
			}
			else
			{
				$divContent .= 	'<div style="margin-top:15px;">
									This property is currently VACCANT.
								</div>';
			}
			$divContent .= 	'<div style="margin-top:15px;">
									<a href="takeThisAction.php?act=del&pid='.$propertyId.'"><button class="btn btn-success">DELETE PROPERTY</button></a>
							</div>';
		}
		else
		{
			$divContent = 	'<div class="d-flex" style="margin-top:15px;">
								Property not found.
							</div>';
		}
	}
}
else
{
	header('location: listedProperties.php');
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Delete Property</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Delete this property...</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="listedProperties.php">Properties</a></li>
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