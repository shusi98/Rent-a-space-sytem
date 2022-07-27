<?php
session_start();

$errorMessage = "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Home</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container back-image">
		<h1 class="py-4 bg-dark text-light rounded "  style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Welcome to Rent-A-Space</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
			</ul>
		</div>
		<div class="d-flex"	style="margin-top:15px;">
			<span class="error"><h6><?php echo $errorMessage;?></h6></span>
		</div>
		<div class="d-flex table-data"  style="margin-top:15px;">
			<div class="dashContent"><a href="about.php"><button>About Rent-A-Space</button></a></div>
		<?php
		if(isset($_SESSION["signedInUser"]))
		{
			if($_SESSION["signedInUser"] == "tenantSignedIn")
			{?>
				<div class="dashContent"><a href="sendEnquiry.php"><button>Contact Support</button></a></div>
				<div class="dashContent"><a href="dashboard.php"><button>My Dashboard</button></a></div>
				<div class="dashContent"><a href="availProps.php"><button>View Availables Spaces</button></a></div>
				<div class="dashContent"><a href="signOut.php"><button>Log Out</button></a></div>
			<?php
			}
			elseif($_SESSION["signedInUser"] == "adminSignedIn")
			{?>
				<div class="dashContent"><a href="dashboard.php"><button>My Dashboard</button></a></div>
				<div class="dashContent"><a href="viewApplications.php"><button>Tenant Applications</button></a></div>
				<div class="dashContent"><a href="signOut.php"><button>Log Out</button></a></div>
			<?php
			}
		}
		else
		{
		?>
			<div class="dashContent"><a href="signIn.php"><button>Login</button></a></div>
			<div class="dashContent"><a href="signUp.php"><button>Create Account</button></a></div>
		<?php
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