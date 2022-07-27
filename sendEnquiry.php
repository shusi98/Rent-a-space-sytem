<?php
include_once 'validateTenant.php';
//session_start();
include_once 'Constants.php';
$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
$stmt = $con->prepare("select FirstName,LastName,Email from user where UserID = ?");
$stmt->bind_param("i",$_SESSION["UserID"]);
$stmt->execute();
$stmt->bind_result($firstName,$lastName,$userEmail);
$stmt->fetch();
$con->close();

$errorMessage = "";
$theSubject = "";

$error = array();
$fieldErrors = false;

if(isset($_POST["sendMessage"]))
{
	if(!empty($_POST["message"]))
	{
		$theMessage = test_input($_POST["message"]);
		if(strlen($theMessage) > 20)
		{
			$error["message"] = false;
		}
		else
		{
			$error["message"] = true;
			$errorMessage = "Your message is so short. Are you sure that's all you say?";
		}
		if(strlen($theMessage) > 450)
		{
			$error["message"] = true;
			$errorMessage = "Your message is too long. Maximum characters allowed is 440";
		}
	}
	else
	{
		$error["message"] = true;
		$errorMessage = "Type your message";
	}
	
	if(!empty($_POST["subject"]))
	{
		$theSubject = test_input($_POST["subject"]);
		if(strlen($theSubject) > 3)
		{
			$error["subject"] = false;
		}
		else
		{
			$error["subject"] = true;
			$errorMessage = "Your subject is too short";
		}
	}
	else
	{
		$error["subject"] = true;
		$errorMessage = "Type a subject for your message";
	}
	
	
	foreach($error as $err => $value)
	{
		if( $error[$err] == true ){
			$fieldErrors = true;
		}
	}
	
	
	if(!$fieldErrors == true)
	{
		$headers = "Sent by " . $firstName. " ". $lastName .". Reply to: " . $userEmail;
		$to = "admin@rent-a-space.co.za";
		if(mail($to, $theSubject, $theMessage, $headers))
		{
			$errorMessage = "Thank you for your message, admin will reply shortly";
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
    <title>Rent-A-Space | Send Enquiring</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Something on your mind? Tell @Admin about it</h3>
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
					<span>Add a descriptive subject about your enquiry</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
						</div>
						<input type="text" name="subject" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="" value="<?php echo $theSubject;?>" required>
					</div>
					<span>Type your message here</span>
					<div class="input-group mb-2">
						<div class="input-group-prepend">
						</div>
						<textarea rows="7" cols="70" name="message" placeholder="Enter text here..."></textarea>
					</div>
					<div class="d-flex justify-content-left">
						<button name="sendMessage"  class="btn btn-success" id="">SEND MESSAGE</button>
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