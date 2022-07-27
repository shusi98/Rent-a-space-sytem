<?php
include_once 'validateAdmin.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Listed Properties</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Here is a list of all active rentals on the system</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<select id="filterPropertiesBy" onchange="sortRentals()">
				<option value="all">View All</option>
				<option value="highest">Price - highest to lowest</option>
				<option value="lowest">Price - lowest to highest</option>
				<optgroup label="View by region">
					<option value="Eastern Cape">Eastern Cape</option>
					<option value="Free State">Free State</option>
					<option value="Gauteng">Gauteng</option>
					<option value="KwaZulu-Natal">KwaZulu-Natal</option>
					<option value="Limpopo">Limpopo</option>
					<option value="Mpumalanga">Mpumalanga</option>
					<option value="North-West">North-West</option>
					<option value="Northern Cape">Northern Cape</option>
					<option value="Western Cape">Western Cape</option>
				</optgroup>
			</select>
		</div>
		<section id="secBody">
		
		</section>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script>
	window.onload = function(){sortRentals()};
	function sortRentals()
	{
		var sortBy = document.getElementById("filterPropertiesBy").value;
		$.ajax({
			url: "getMeRentals.php",
			data: "param="+sortBy,
			method: "post"
		}).done(function(response) {
			var data = JSON.parse(response);
			if(data.status == 0)
			{
				document.getElementById("secBody").innerHTML = data.divContent;
			}
		})
	}
</script>

</body>
</html>