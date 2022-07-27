<?php
session_start();

$errorMessage = "";
$userEmail="";

if(isset($_POST["signMeIn"]))
{
	if(!empty($_POST["userEmail"]) && !empty($_POST["userPassword"]))
	{
		include_once 'Constants.php';
		$userEmail = test_input($_POST["userEmail"]);
		if (filter_var($userEmail, FILTER_VALIDATE_EMAIL))
		{
			$userPassword = test_input($_POST["userPassword"]);
			$con = new mysqli($ras_db.sql);
			if(!$con->connect_error)
			{
				$stmt = $con->prepare("select UserID,FirstName,LastName,Email,Password,AccountActive,UserType from user where Email = ?");
				$stmt->bind_param("s",$userEmail);
				$stmt->execute();
				$stmt->bind_result($userId,$name,$surname,$email,$password,$active,$userType);
				$stmt->fetch();
				$con->close();
				
				if($active == "0")
				{
					$errorMessage = "Failed, please contact Systes Administrator for more info";
				}
				else
				{
					if( password_verify($userPassword,$password) )
					{
						if($userType == '0')
							$_SESSION["signedInUser"] = "adminSignedIn";
						if($userType == '1'){
							$_SESSION["signedInUser"] = "tenantSignedIn";
							$_SESSION["UserID"] = $userId;
						}
						header('location: index.php');
					}
					else
					{
						$errorMessage = "Invalid username/password combination";
					}
				}
			}
		}
		else
		{
			$errorMessage = "Invalid e-mail - " .$userEmail;
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
    <title>Rent-A-Space | Sign In</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded "  style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Welcome to Rent-A-Space. Please sign-in to continue...</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div class="d-flex"	style="margin-top:15px;">
			<span class="error"><h6><?php echo $errorMessage;?></h6></span>
		</div>
		<div class="d-flex">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="w-50">
				<div class="py-2">
					<span>Email address</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="email" name="userEmail" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="UserID" value="<?php echo $userEmail;?>" required>
					</div>
					
					<span>Password</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="password" name="userPassword" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="Password" required>
					</div> 
                    
                    <span>Forgot your password?</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
						</div>
						<a href="lostPassword.php">Reset your password here</a>
					</div>
                    
					<div class="d-flex justify-content-left">
						<button name="signMeIn"  class="btn btn-success" id="">LOGIN</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>   



</body>
</html>