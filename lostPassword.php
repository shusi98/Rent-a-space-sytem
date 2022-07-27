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
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="signIn.php">Sign In</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div class="d-flex"	style="margin-top:15px;">
			<span class="error"><h6 id="errorMessageField"></h6></span>
		</div>
		<div class="d-flex">
			<div class="py-2">
				<span>Email address</span>
				<div class="input-group mb-2">
					<div class="input-group-prepend">
						<div class="input-group-text bg-warning">
							<i class="fas fa-id-badge"></i>
						</div>
					</div>
					<input type="email" name="userEmail" id="lostPasswordEmail" autocomplete="off" class="form-contrl" id="inlineFormInputGroup" placeholder="Your e-mail" required>
				</div>
				<div class="d-flex justify-content-left">
					<button onclick="retrieveMyPassword()">GET PASSWORD</button>
				</div>
			</div>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script>

	function retrieveMyPassword()
	{
		var userEmail = document.getElementById("lostPasswordEmail").value;
		if(userEmail == "")
		{
			document.getElementById("lostPasswordEmail").style.borderColor = "red";
		}
		else
		{
			document.getElementById("lostPasswordEmail").style.borderColor = "#333";
		}
		
		$.ajax({
			url: "retrievePassword.php",
			data: "userEmail=" + userEmail,
			method: "post"
		}).done(function(response) {
			var data = JSON.parse(response);
			document.getElementById("errorMessageField").innerHTML = data.comment;
		})
	}
	
</script>
</body>
</html>