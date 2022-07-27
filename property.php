<?php
include_once 'validateAdmin.php';
include_once 'Constants.php';

if(isset($_GET['pid']) && !empty($_GET['pid']))
{
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	
	$propId = mysqli_real_escape_string($con,$_GET["pid"]);

	$image = "";
	
	if(!$con->connect_error)
	{
		$stmt = $con->prepare("select property.PropertyID,PropertyName,Vaccant,Description,Rent,Address,Province,City,Image from property,image where property.PropertyID = ? and property.PropertyID = image.PropertyID");
		$stmt->bind_param("i",$propId);
		$stmt->execute();
		$stmt->bind_result($propertyId,$propertyName,$vaccant,$description,$rent,$address,$province,$city,$image);
		mysqli_stmt_store_result($stmt);
		$numReturnedRows = mysqli_stmt_num_rows($stmt);
		$stmt->fetch();
		$con->close();
		if($numReturnedRows > 0)
		{
			$_SESSION["findThisProperty"] = $propertyId;
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
    <title>Rent-A-Space | Property</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>A little more about this property...</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="listedProperties.php">Properties</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div style="margin-top:15px;">
			<?php
			if($numReturnedRows > 0)
			{
				echo '<div class="d-flex" style="margin-top:15px;">
					<select id="propertyActions" onchange="sortThisMess()">
						<option disabled selected>Select options</option>
						<option value="edit">Edit property info</option>
						<option value="tenant">Tenant occupying <?php echo $propertyName;?></option>
						<option value="chargeRent">Charge rent on '.$propertyName.'</option>
					</select>
				</div>
				
				<div id="requestedDivContent" class="d-flex table-data"  style="margin-top:15px;">
					
				</div>';
				echo '<img src="data:image/jpeg;base64,'.base64_encode($image ).'" height="200" width="300"/>';
				echo '<div class="d-flex" style="margin-top:15px;">Property name: '. $propertyName.'<br>'.'Vaccant: '.$vaccant.'<br>'.'Description: '.$description.'<br>'.'Rent: R '.$rent.'<br>'.'Address: '.$address.'<br>'.'City: '.$city.'<br>'.'Province: '.$province.'</div>';
			}
			else
			{
				echo 'Property not found.';
			}
			?>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script>
	function sortThisMess()
	{
		var doThis = document.getElementById("propertyActions").value;
		$.ajax({
			url: "getMeThisProperty.php",
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