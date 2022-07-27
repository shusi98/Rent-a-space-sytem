<?php
include_once 'validateAdmin.php';

$errorMessage = "";
$propertyName=$propertyDescription=$propertyRent=$propertyAddress="";

$error = array();
$fieldErrors = false;

include_once 'Constants.php';

if(isset($_POST["addProperty"]))
{
	$propertyName = test_input($_POST["propertyName"]);
	if (empty($propertyName))
	{
		$error["propertyName"] = true;
		$errorMessage = "Enter a valid Property Name";
	}
	else
	{
		$error["propertyName"] = false;
		$propertyName = test_input($_POST["propertyName"]);
	}

	$propertyDescription = test_input($_POST["propertyDescription"]);
	if (empty($propertyDescription))
	{
		$error["propertyDescription"] = true;
		$errorMessage = "Enter a valid Property Description";
	}
	else
	{
		$error["propertyDescription"] = false;
		$propertyDescription = test_input($_POST["propertyDescription"]);
	}

	$propertyRent = test_input($_POST["propertyRent"]);
	if (/*!preg_match("/^[0-9]*$/",$propertyRent)*/is_int($propertyRent) || empty($propertyRent))
	{
		$error["propertyRent"] = true;
		$errorMessage = "Only intergers allowed for field RENT AMOUNT";
	}
	else
	{
		$propertyRent = (int)$propertyRent;
		if($propertyRent < 5000)
		{
			$error["propertyRent"] = true;
			$errorMessage = "Minimum rent is R5000 (Five Thousand Rands)";
		}
		else
		{
			$propertyRent = (string)$propertyRent;
			$error["propertyRent"] = false;
			$propertyRent = test_input($_POST["propertyRent"]);
		}
	}
	
	if($_POST["state"] != '')
	{
		if($_POST["state"] == 1)
			$_POST["state"] = "Eastern Cape" ;
		if($_POST["state"] == 2)
			$_POST["state"] = "Free State" ;
		if($_POST["state"] == 3)
			$_POST["state"] = "Gauteng" ;
		if($_POST["state"] == 4)
			$_POST["state"] = "kwaZulu-Natal" ;
		if($_POST["state"] == 5)
			$_POST["state"] = "Limpopo" ;
		if($_POST["state"] == 6)
			$_POST["state"] = "Mpumalanga" ;
		if($_POST["state"] == 7)
			$_POST["state"] = "Northern Cape" ;
		if($_POST["state"] == 8)
			$_POST["state"] = "North-West" ;
		if($_POST["state"] == 9)
			$_POST["state"] = "Western Cape" ;
		
		$province = test_input($_POST["state"]);
		$error["province"] = false;
	} 
	else {
		$errorMessage = "Please select a province";
		$error["province"] = true;
	}
	
	if($_POST["city"] != '') {
		$city = test_input($_POST["city"]);
	} 
	else {
		$cityErr = "Please select a city";
		$error["city"] = true;
	}

	$propertyAddress = test_input($_POST["propertyAddress"]);
	if(empty($propertyAddress) || strlen($propertyAddress) < 14)
	{
		$error["propertyAddress"] = true;
		$errorMessage = "Enter a valid address";
	}
	else
	{
		$error["propertyAddress"] = false;
		$propertyAddress = test_input($_POST["propertyAddress"]);
	}
	
	foreach($error as $err => $value){
		if( $error[$err] == true ){
			$fieldErrors = true;
		}
	}
		
	if(!$fieldErrors == true){
		$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
		if(!$con->connect_error)
		{
			$stmt = $con->prepare("insert into property(PropertyName,Description,Rent,Address,Province,City) values(?,?,?,?,?,?)");
			$stmt->bind_param("ssssss",$propertyName,$propertyDescription,$propertyRent,$propertyAddress,$province,$city);
			if($stmt->execute())
			{
				/*$lastInsertId = $con->insert_id;
				$con->close();
				header('location: listedProperties.php');*/
				$lastInsertId = $con->insert_id;
				$con->close();
				$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
				$stmt = $con->prepare("insert into payment(PropertyID) values(?)");
				$stmt->bind_param("s",$lastInsertId);
				if($stmt->execute())
				{
					$con->close();
					$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
					$stmt = $con->prepare("insert into image(PropertyID) values(?)");
					$stmt->bind_param("s",$lastInsertId);
					if($stmt->execute())
					{
						$con->close();
						header("location: uploadImage.php?lipid=$lastInsertId");
					}
				}
				else
					header('location: listedPropertieaas.php');
			}
			else
			{
				$con->close();
				$errorMessage = "Please try again ".$stmt->error;
				if((string)$stmt->error == "Data too long for column 'Description' at row 1" )
				{
					$errorMessage = "Property Description field limited to 150 characters";
				}
			}
		}
	}
	
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Add Property</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Add Properties To The System</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div class="d-flex"	style="margin-top:15px;">
			<span class="error"><h6><?php echo $errorMessage;?></h6></span>
		</div>
		<div class="d-flex">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="w-50">
				<div class="py-2" >
					<span>Property Name</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="text" name="propertyName" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="John Flats" required value="<?php echo $propertyName;?>">
					</div>
					<span>Description</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="text" name="propertyDescription" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="John Flats is..........." value="<?php echo $propertyDescription;?>" required>
					</div>
					<span>Rent Amount - ONLY RAND (ZAR) INTEGER VALUES- e.g 1500 or 20000</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="text" name="propertyRent" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="9999" value="<?php echo $propertyRent;?>" required>
					</div>
					<span>Country</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<select id="country" name="country" class="state">
							<option value="South Africa">South Africa</option>
						</select>
					</div>
					<span>Province</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<select id="state" name = "state" class="state" required>
							<option disabled selected>Select A Province</option>
							<?php
								$connection = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
								$sql = "SELECT * FROM province";
								$result = mysqli_query($connection, $sql);
								while($row = mysqli_fetch_assoc($result)){
							?>
							<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ;?></option>
							<?php 
								//$connection->close();
							} ?>
						</select>
					</div>
					<span>City</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<select id="city" name = "city" class="city" required>
							<option disabled selected>Select A Province</option>
						</select>
					</div>
					<span>Property Address</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="text" name="propertyAddress" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="123 Fake Street, Fake Suburb, 0000" value="<?php echo $propertyAddress;?>" required>
					</div>

					<div class="d-flex justify-content-left">
						<button name="addProperty"  class="btn btn-success" id="">ADD PROPERTY</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script type="text/javascript">
	function getCitySelectList(){
		var state_select = document.getElementById("state");

		var state_id = state_select.options[state_select.selectedIndex].value;
		console.log('StateId : ' + state_id);

		var xhr = new XMLHttpRequest();
		var url = 'cities.php?state_id=' + state_id;
		xhr.open('GET', url, true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function(){
			if(xhr.readyState == 4 && xhr.status == 200){
				var text = xhr.responseText;
				var city_select = document.getElementById("city");
				city_select.innerHTML = text;
				city_select.style.display='inline';
			}
		}
		xhr.send();
	}
	var state_select = document.getElementById("state");
	state_select.addEventListener("change", getCitySelectList);
</script>

</body>
</html>