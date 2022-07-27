<?php
include_once 'validateAdmin.php';
include_once 'Constants.php';
$divContent = "";
$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
if(!$con->connect_error)
{
	$stmt = $con->prepare("select a.ApplicationID,a.DateApplied,b.UserID,b.FirstName,b.LastName,b.AccountActive,b.DateAdded,c.PropertyName,c.Rent,c.Address from application a, user b, property c where a.ApplicationActive = '1' and a.UserID = b.UserID and a.PropertyID = c.PropertyID");
	$stmt->execute();
	$stmt->bind_result($applicationId,$dateApplied,$userId,$firstName,$lastName,$accountActive,$dateJoined,$propertyName,$rentAmount,$address);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
	while($stmt->fetch())
	{
		$divContent .= '<div style="margin-top:15px;">'.$firstName.' '.$lastName.', with account currently active: '.$accountActive.', wishes to occupy the space '.$propertyName.' situated at '.$address.' at R '.$rentAmount.' per month. '.$firstName.' has been a member of Rent-A-Space since'.substr($dateJoined,0,10).'. You may <a href="takeThisAction.php?aid='.$applicationId.'&uid='.$userId.'&des=1">approve</a> or <a href="takeThisAction.php?aid='.$applicationId.'&uid='.$userId.'&des=2">dismiss</a> this application</div>';
	}
	$con->close();
}
if($numReturnedRows < 1)
{
	$divContent = '<div class="d-flex"	style="margin-top:15px;">No applications for now...</div>';
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Applications</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Applications to occupy space</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
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
<script src="js/jquery-3.3.1.min.js"></script>

</body>
</html>