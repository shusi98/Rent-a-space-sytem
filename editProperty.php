<?php
include_once 'validateAdmin.php';

$errorMessage = "";
$propertyName=$propertyDescription=$propertyRent=$propertyAddress="";
$numReturnedRows = 0;

$error = array();
$fieldErrors = false;


if(isset($_GET['pid']) && !empty($_GET['pid']))
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$propertyId = mysqli_real_escape_string($con,$_GET["pid"]);
	$_SESSION["findThisProperty"] = $propertyId;
	$stmt = $con->prepare("select PropertyName,Description,Rent,Visible from property where PropertyID = ?");
	$stmt->bind_param("i",$propertyId);
	$stmt->execute();
	$stmt->bind_result($propertyName,$propertyDescription,$propertyRent,$propertyHidden);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
	$stmt->fetch();
	$con->close();
	
	
	if($propertyHidden == "Yes")
	{
		$radio1 = 'checked="checked"';
		$radio2 = '';
	}
	else
	{
		$radio1 = '';
		$radio2 = 'checked="checked"';
	}
}

if(isset($_POST["updateProperty"]))
{
	include_once 'Constants.php';
	$propertyName = test_input($_POST["propertyName"]);
	if (!preg_match("/^[a-zA-Z ]*$/",$propertyName) || empty($propertyName))
	{
		$error["propertyName"] = true;
		$errorMessage = "Only letters and white space allowed for field PROPERTY NAME";
	}
	else
	{
		$error["propertyName"] = false;
		$propertyName = test_input($_POST["propertyName"]);
	}

	$propertyDescription = test_input($_POST["propertyDescription"]);
	if (!preg_match("/^[a-zA-Z ]*$/",$propertyDescription) || empty($propertyDescription))
	{
		$error["propertyDescription"] = true;
		$errorMessage = "Only letters and white space allowed for field PROPERTY DESCRIPTION";
	}
	else
	{
		$error["propertyDescription"] = false;
		$propertyDescription = test_input($_POST["propertyDescription"]);
	}

	$propertyRent = test_input($_POST["propertyRent"]);
	if (is_int($propertyRent) || empty($propertyRent))
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

	$propertyHidden = $_POST["accountActive"];
	
	foreach($error as $err => $value){
		if( $error[$err] == true ){
			$fieldErrors = true;
		}
	}
		
	if(!$fieldErrors == true){
		$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
		if(!$con->connect_error)
		{
			$stmt = $con->prepare("update property set PropertyName = ?, Description = ?, Rent = ?, Visible = ? where PropertyID = ?");
			$stmt->bind_param("sssss",$propertyName,$propertyDescription,$propertyRent,$propertyHidden,$_SESSION["findThisProperty"]);
			if($stmt->execute())
			{
				$propertyId = $_SESSION["findThisProperty"];
				$con->close();
				header("location: property.php?pid=$propertyId");
			}
			else
			{
				$con->close();
				$errorMessage = "Please try again ";
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
    <title>Rent-A-Space | Edit Property</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Update Properties Info</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="listedProperties.php">Properties</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div class="d-flex"	style="margin-top:15px;">
			<span class="error"><h6><?php echo $errorMessage;?></h6></span>
		</div>
		<div class="d-flex">
		<?php
			if($numReturnedRows > 0)
			{
		?>
				
			<div class="py-2" >
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="w-50">
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
					<span>Property Hidden</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="radio" name="accountActive" value="Yes" <?php echo $radio1;?>>
						<label for="Yes">Active</label><br>
						<input type="radio" name="accountActive" value="No" <?php echo $radio2;?>>
						<label for="No">Blocked</label>
					</div>
					<div class="d-flex justify-content-left">
						<button name="updateProperty" class="btn btn-success" id="">UPDATE PROPERTY</button>
					</div>
				</form>
				<div class="d-flex justify-content-left">
					<a href="deleteProperty.php?pid=<?php echo $propertyId;?>"><button name="deleteProperty"  class="btn btn-success" id="">DELETE THIS PROPERTY</button></a>
				</div>
			</div>
			<?php
			}
			else
			{
				echo 'Sorry, there seems to be a problem with that link...';
			}
			?>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
</body>
</html>