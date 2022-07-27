<?php
	$errorMessage = "";
	$otpValueErr = $passwordOneErr = $passwordTwoErr = "";
	$_SESSION["passwordProcessStarted"] = NULL;
	if( isset($_POST["changeMyPassword"]) )
	{
		$_SESSION["passwordProcessStarted"] = "true";
		if(isset($_GET['email']) && !empty($_GET['email']))
		{
			include_once 'Constants.php';

			$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
			
			$email = mysqli_real_escape_string($con,$_GET["email"]);
			
			$stmt = $con->prepare("select Email,OTP from user where Email = ?");
			$stmt->bind_param("s",$email);
			$stmt->execute();
			$stmt->bind_result($userEmail,$userOTP);
			$stmt->fetch();
			$con->close();
			
			$oneTimePin = test_input($_POST["otpValue"]);
			if(empty($oneTimePin))
			{
				$errorMessage = $otpValueErr = "OTP missing.";
				$error["oneTimePin"] = true;
			}
			else
			{
				if( !($userOTP == $oneTimePin) )
				{
					$errorMessage = $otpValueErr = "Incorrect OTP.";
					$error["oneTimePin"] = true;
				}
				else
				{
					$errorMessage = "";
					$error["oneTimePin"] = false;
				}
			}
			if( ($userEmail == $email) && ($userOTP == $oneTimePin) )
			{
				$error = array();
				$fieldErrors = false;
				
				$passwordOne = test_input($_POST["passwordOne"]);
				$passwordTwo = test_input($_POST["passwordTwo"]);
				if( !empty($passwordOne) && !empty($passwordTwo) )
				{
					if( (strlen($passwordOne)>5 && strlen($passwordOne)<15) && (strlen($passwordTwo)>5 && strlen($passwordTwo)<15) )
					{
						if( !($passwordOne == $passwordTwo) )
						{
							$passwordOneErr = $passwordTwoErr = "Passwords do not match";
							$error["matchingPasswords"] = true;
						}
						else
						{
							$customerPassword = password_hash($passwordTwo,PASSWORD_DEFAULT);
							$error["matchingPasswords"] = false;
						}
					}
					else
					{
						$passwordOneErr = $passwordTwoErr = "Password must be atleast 6 to 14 charcters long";
						$error["passwordLength"] = true;
					}
				}

				foreach($error as $err => $value)
				{
					if( $error[$err] == true ){
						$fieldErrors = true;
					}
				}

				if(!$fieldErrors)
				{
					include_once 'Constants.php';
					
					$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
					$stmt = $con->prepare("update user set Password = '$customerPassword' where Email = ?");
					$stmt->bind_param("s",$userEmail);
					if($stmt->execute())
					{
						$con->close();
						do
						{
							$newOneTimePin = rand(1000,99999);
						}
						while($newOneTimePin == $userOTP);
						$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
						$stmt2 = $conn->prepare("update user set  OTP = ? where Email = ?");
						$stmt2->bind_param("ss",$newOneTimePin,$userEmail);
						$stmt2->execute();
						$conn->close();
						$errorMessage = "Password changed succesfully";
						header( "Refresh:2.5; url=signIn.php", true, 303);
						//header();
					}
					else
					{
						$con->close();
						$errorMessage = 'Please try again.';
					}
				}
			}
			else
			{
				$errorMessage = 'The url is invalid or has expired. Please check your email inbox for the correct link to use.';
			}
		}
		else
		{
			$errorMessage = 'Invalid approach, please use the link that has been send to your email.';
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Reset Password</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Reset your password</h3>
		</div>
		<div class="d-flex">
			<?php					
			if(isset($_GET['email']) && !empty($_GET['email']) AND !($_SESSION["passwordProcessStarted"] == "true"))
			{
				include_once 'Constants.php';
					
				$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
				
				$email = mysqli_real_escape_string($con,$_GET["email"]);
				
				
				$stmt = $con->prepare("select Email from user where Email = ?");
				$stmt->bind_param("s",$email);
				$stmt->execute();
				$stmt->bind_result($userEmail);
				$stmt->fetch();
				$con->close();
							 
				if($userEmail == $email)
				{
					echo'<form action="'.htmlspecialchars($_SERVER["REQUEST_URI"]).'" class="contact-form" method="post">
							<div class="py-2">
								<span>'.$userEmail.'</span>
								<div>
									<input type="hidden" name="" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="" required>
								</div>
								
								<span>OTP</span>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text bg-warning">
											<i class="fas fa-id-badge"></i>
										</div>
									</div>
									<input type="text" placeholder="OTP" name="otpValue" class="form-contrl" id="inlineFormInputGroup" required>
									<span class="error">'.$otpValueErr.'</span>
								</div>

								<span>Password</span>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text bg-warning">
											<i class="fas fa-id-badge"></i>
										</div>
									</div>
									<input type="password" placeholder="Password" name="passwordOne" class="form-contrl" id="inlineFormInputGroup" required>
									<span class="error">'.$passwordOneErr.'</span>
								</div>
								
								<span>Re-enter password</span>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text bg-warning">
											<i class="fas fa-id-badge"></i>
										</div>
									</div>
									<input type="password" placeholder="password (again)" name="passwordTwo" class="form-contrl" id="inlineFormInputGroup" required>
									<span class="error">'.$passwordTwoErr.'</span>
								</div>
								
								<div class="d-flex justify-content-left">
									<button name="changeMyPassword"  class="btn btn-success" id="">CHANGE PASSWORD</button>
								</div>
							</div>
						</form>';
				}
				else
				{
					$errorMessage = 'The url is invalid or has expired. Please check your email inbox for the correct link to use.';
				}
			}
			else if(!($_SESSION["passwordProcessStarted"] == "true"))
			{
				$errorMessage = 'Invalid approach, please use the link that has been send to your email.';
			}
			echo '<span class="error">' . $errorMessage . '</span>';
		?>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
</body>
</html>