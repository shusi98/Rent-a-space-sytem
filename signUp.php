<?php
session_start();

$errorMessage = "";
$userEmail=$firstName=$lastName="";

$error = array();
$fieldErrors = false;

if(isset($_POST["signMeUp"]))
{
	if(!empty($_POST["userEmailSignUp"]))
	{
		include_once 'Constants.php';
		$userEmail = (string)test_input($_POST["userEmailSignUp"]);
		if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
		{
			$error["userEmail"] = true;
			$errorMessage = "Invalid e-mail - " .$userEmail;
		}
		else
		{
			$error["userEmail"] = false;
			$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
			$stmt = $con->prepare("select count(Email) from user where Email = ?");
			$stmt->bind_param("s",$userEmail);
			$stmt->execute();
			$stmt->bind_result($count);
			$stmt->fetch();
			$con->close();
			if($count>0)
			{
				$error["userCount"] = true;
				$errorMessage = "Account already exists";
			}
			else
			{
				$error["userCount"] = false;
				$password = test_input($_POST["userPasswordOne"]);
				$password2 = test_input($_POST["userPasswordTwo"]);
				if( !empty($password) && !empty($password2) )
				{
					if( (strlen($password)>5 && strlen($password)<15) && (strlen($password2)>5 && strlen($password2)<15) )
					{
						if( !($password == $password2) )
						{
							$error["password"] = true;
							$errorMessage = "Passwords do not match";
						}
						else
						{
							$error["password"] = false;
							$userPassword = password_hash($password,PASSWORD_DEFAULT);;
						}
					}
					else
					{
						$error["password"] = true;
						$errorMessage = "Password must be between 6 and 14 charcters long";
					}
				}
				else
				{
					$error["password"] = true;
					$errorMessage = "Passwords do not match";
				}
				
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
				
				if(!isset($_POST["termsAndConditions"]))
				{
					$error["termsAndConditions"] = true;
					$errorMessage = "You did not agree to our terms and conditions";
				}
				else
				{
					$error["termsAndConditions"] = false;
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
						$OTP = rand(1000,99999);
						$stmt = $con->prepare("insert into user(FirstName,LastName,Email,Password,OTP) values(?,?,?,?,?)");
						$stmt->bind_param("sssss",$firstName,$lastName,$userEmail,$userPassword,$OTP);
						if($stmt->execute())
						{
							//$lastInsertId = $con->insert_id;
							$con->close();
							header('location: signIn.php');
						}
						else
						{
							$con->close();
							$errorMessage = "Please try again";
							if( (string)$stmt->error == "Data too long for column 'LastName' at row 1" )
							{
								$errorMessage = "LastName field limited to 15 characters";
							}
							if( (string)$stmt->error == "Data too long for column 'FirstName' at row 1" )
							{
								$errorMessage = "FirstName field limited to 15 characters";
							}
						}
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
    <title>Rent-A-Space | Sign Up</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Welcome to Rent-A-Space. Please sign-up to continue...</h3>
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
						<input type="email" name="userEmailSignUp" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="john@doe.com" value="<?php echo $userEmail;?>" required>
					</div>
					<span>Password</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="password" name="userPasswordOne" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="********" required>
					</div>
					<span>Re-enter Password</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="password" name="userPasswordTwo" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="********" required>
					</div> 
					<span>Agree To Terms & Conditions</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
							<div class="input-group-text bg-warning">
								<i class="fas fa-id-badge"></i>
							</div>
						</div>
						<input type="checkbox" name="termsAndConditions" value="Agree">
						<label for="vehicle1">Agree</label>
					</div>
					<div class="input-group mb-2">
						<span>Read our Terms And Conditions <a href="termsAndConditions.php" target="_blank">HERE</a></span>
					</div>

					<div class="d-flex justify-content-left">
						<button name="signMeUp"  class="btn btn-success" id="">REGISTER</button>
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