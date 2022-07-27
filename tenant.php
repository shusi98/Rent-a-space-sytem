<?php
include_once 'validateAdmin.php';
include_once 'Constants.php';

if(isset($_GET['tid']) && !empty($_GET['tid']))
{
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	
	$tenantId = mysqli_real_escape_string($con,$_GET["tid"]);

	if(!$con->connect_error)
	{
		$stmt = $con->prepare("select UserID,FirstName,LastName,Email,AccountActive,DateAdded from user where UserID = ?");
		$stmt->bind_param("i",$tenantId);
		$stmt->execute();
		$stmt->bind_result($userId,$firstName,$lastName,$userEmail,$accountActive,$dateJoined);
		mysqli_stmt_store_result($stmt);
		$numReturnedRows = mysqli_stmt_num_rows($stmt);
		$stmt->fetch();
		$con->close();
		if($numReturnedRows > 0)
		{
			$_SESSION["findThisUser"] = $userId;
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
    <title>Rent-A-Space | Tenant</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>A little more about this tenant...</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="tenants.php">Tenants</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<?php
			if($numReturnedRows > 0)
			{
				echo 'Full name: '. $firstName.' '.$lastName.'<br>'.'Email Adress: '.$userEmail.'<br>'.'Account Active: '.$accountActive.'<br>'.'Tenant since: '.substr($dateJoined,0,10);
			}
			else
			{
				echo 'User not found.';
			}
			?>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<select id="userActions" onchange="sortThisMess()">
				<option disabled selected>Select option</option>
				<option value="edit">Edit user info</option>
				<option value="properties">Properties occupied by <?php echo $firstName;?></option>
			</select>
		</div>
		<div id="requestedDivContent" style="margin-top:15px;">
			
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script>
	function sortThisMess()
	{
		var doThis = document.getElementById("userActions").value;
		$.ajax({
			url: "getMeThisUser.php",
			data: "param="+doThis,
			method: "post"
		}).done(function(response) {
			var data = JSON.parse(response);
			if(data.status == 0)
			{
				document.getElementById("requestedDivContent").innerHTML = data.divContent;
			}
			if(data.status == 1)
			{
				window.location = data.divContent;
			}
		})
	}
</script>

</body>
</html>