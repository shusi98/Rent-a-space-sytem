<?php
include_once 'validateAdmin.php';

$errorMessage = "";
$userEmail=$firstName=$lastName="";

$error = array();
$fieldErrors = false;

if(isset($_GET['tid']) && !empty($_GET['tid']))
{
	include_once 'Constants.php';
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$userId = mysqli_real_escape_string($con,$_GET["tid"]);
	$_SESSION["editThisUser"] = (int)$userId;
	$stmt = $con->prepare("select FirstName,LastName,Email,AccountActive,UserType from user where UserID = ?");
	$stmt->bind_param("i",$userId);
	$stmt->execute();
	$stmt->bind_result($firstName,$lastName,$userEmail,$accountActive,$userType);
	mysqli_stmt_store_result($stmt);
	$numReturnedRows = mysqli_stmt_num_rows($stmt);
	$stmt->fetch();
	$con->close();
	if($accountActive == "Yes")
	{
		$radio1 = 'checked="checked"';
		$radio2 = '';
	}
	else
	{
		$radio1 = '';
		$radio2 = 'checked="checked"';
	}
	if($userType == "1")
	{
		$radioT1 = 'checked="checked"';
		$radioT2 = '';
	}
	else
	{
		$radioT1 = '';
		$radioT2 = 'checked="checked"';
	}
	
}
if(isset($_POST["updateTenantInfo"]))
{
	if(!empty($_POST["userEmail"]))
	{
		include_once 'Constants.php';
		$userEmail = (string)test_input($_POST["userEmail"]);
		if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
		{
			$error["userEmail"] = true;
			$errorMessage = "Invalid e-mail - " .$userEmail;
		}
		else
		{
			$error["userEmail"] = false;

			$firstName = test_input($_POST["firstName"]);
			if (!preg_match("/^[a-zA-Z ]*$/",$firstName) || empty($firstName))
			{
				$error["firstName"] = true;
				$errorMessage = "Only letters and white space allowed for field FIRST NAME";
			}
			else
			{
				$error["firstName"] = false;
				$firstName = test_input($_POST["firstName"]);
			}
			
			$lastName = test_input($_POST["lastName"]);
			if(!preg_match("/^[a-zA-Z ]*$/",$_POST["lastName"]) || empty($lastName))
			{
				$error["lastName"] = true;
				$errorMessage = "Only letters and white space allowed for field LAST NAME";
			}
			else
			{
				$error["lastName"] = false;
				$lastName = test_input($_POST["lastName"]);
			}
			
			$accountActive = $_POST["accountActive"];
			$userType = $_POST["userType"];
			
			foreach($error as $err => $value){
				if( $error[$err] == true ){
					$fieldErrors = true;
				}
			}
			
			
			if(!$fieldErrors == true){
				$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
				if(!$con->connect_error)
				{
					//echo $firstName.$lastName.$userEmail.$accountActive.$userType.$userId;exit;
					$stmt = $con->prepare("update user set FirstName = ?, LastName = ?, Email = ?, AccountActive = ?, UserType = ? where UserID = ?");
					$stmt->bind_param("sssssi",$firstName,$lastName,$userEmail,$accountActive,$userType,$_SESSION["editThisUser"]);
					if($stmt->execute())
					{
						$con->close();
						header('location: dashboard.php');
					}
					else
					{
						echo $stmt->error;
						$con->close();
						$errorMessage = "Please try again";
					}
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
    <title>Rent-A-Space | Edit Tenant</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Edit tenant profile...</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="tenants.php">Tenants</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div class="d-flex"	style="margin-top:15px;">
			<span class="error"><h6><?php echo $errorMessage;?></h6></span>
		</div>
		<div class="d-flex">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="w-50">
				<div class="py-2" >
					<span>First Name</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="text" name="firstName" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="John" required value="<?php echo $firstName;?>">
					</div>
					<span>Last Name</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="text" name="lastName" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="Doe" value="<?php echo $lastName;?>" required>
					</div>
					<span>Email Address</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="email" name="userEmail" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="john@doe.com" value="<?php echo $userEmail;?>" required>
					</div>
					<span>Account Activity</span>
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
					<span>User Type</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="radio" name="userType" value="1" <?php echo $radioT1;?>>
						<label for="1">Tenant</label><br>
						<input type="radio" name="userType" value="0" <?php echo $radioT2;?>>
						<label for="0">Administrator</label>
					</div>
					<div class="d-flex justify-content-left">
						<button name="updateTenantInfo"  class="btn btn-success" id="">UPDATE</button>
					</div>
				</div>
			</form>
		</div>
		<div class="d-flex">
			<div class="d-flex justify-content-left">
				<a href="deleteTenant.php?uid=<?php echo $userId;?>"><button name="deleteTenant"  class="btn btn-success" id="">DELETE THIS TENANT</button></a>
			</div>
		</div>

	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>   

</body>
</html>