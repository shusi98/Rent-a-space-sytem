<?php
include_once 'validateTenant.php';
include_once 'Constants.php';

$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
if(!$con->connect_error)
{
	$stmt = $con->prepare("select TransactionID,PropertyName,TenantID,TenantName,TenantEmail,AmountDue,AmountPaid,DateDue,DatePaid ,Created,LastUpdate from transaction where TenantID = ?");
	$stmt->bind_param("s",$_SESSION["UserID"]);
	$stmt->execute();
	$stmt->bind_result($transactionId,$propertyName,$tenantId,$tenantName,$tenantEmail,$amountDue,$amountPaid,$dateDue,$datePaid,$created,$lastUpdate);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | My Reports</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>You Transaction History</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<?php
			while($stmt->fetch())
			{
				echo '<div style="margin-top:15px;">Trace Number: '. $transactionId .'<br>Property name: '. $propertyName.'<br>Total Amount Due: '.$amountDue.'<br>Total Amount Paid: R '.$amountPaid.'<br>Date Due: '.$dateDue.'<br>Date Paid: '.$datePaid.'<br>Transaction Created: '.$created.'<br>Tranction Finalized: '.$lastUpdate.'</div>';
			}
			$con->close();
			if($numReturnedRows < 1)
			{
				echo '<div class="d-flex" style="margin-top:15px;">No transaction history found</div>';
			}
		?>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
</body>
</html>