<?php
include_once 'validateAdmin.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Tenants</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Here is a list of all tenants on the system</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		<div class="flex-container">
			<div class="flex-containerDiv">
				<span>Which Tenants?</span>
				<select name="filterUsersBy" onchange="sortThisTenantMess()">
				<option disabled selected value="">Select option</option>
					<option value="all">View All</option>
					<option value="search">Search</option>
					<option value="active">Active Accounts</option>
					<option value="inactive">Disabled Accounts</option>
					<option value="rentDue">Rent Due</option>
					<option value="occupying">Tenants Occupying Space</option>
					<option value="notOccupying">Tenants NOT Occupying Space</option>
				</select>
			</div>
			<div id="tenantsDivSearch" class="flex-containerDiv">
				<span>Search Here</span>
				<input id="searchForThisUser" type="search">
				<br>
				<button id="tenantsDivSearchButton" onclick="sortThisTenantMess()">SEARCH</button>
			</div>
			<div class="flex-containerDiv">
				<span>Sort By</span>
				<select name="filterUsersBy" onchange="sortThisTenantMess()">
					<option value="all">Default</option>
					<option value="nameAsc">Surname - A to Z</option>
					<option value="nameDesc">Surname - Z to A</option>
					<option value="emailAsc">Email - A to Z</option>
					<option value="emailDesc">Email - Z to A</option>
					<option value="dateAsc">Date Joined - ascending</option>
					<option value="dateDesc">Date Joined - descending</option>
				</select>
			</div>
		</div>
		<div id="tbody" style="margin-top:15px;">
			<!--
			<table class="table table-striped table-dark">
				<thead class="thead-dark">
					<tr>
						<th>Name</th>
						<th>Surname</th>
						<th>Email Address</th>
						<th>Date Joined</th>
						<th>Account Active?</th>
					</tr>
				</thead>

				<tbody id="tbody">
					
				</tbody>
			</table>
			-->
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script>
	var tenantsDivSearch = document.getElementById("tenantsDivSearch");
	tenantsDivSearch.style.display = "none";
	//window.onload = function(){sortThisMess()};
	function sortThisTenantMess()
	{
		var sortBy = "";
		var findBy = "";
		findUsersByElement = document.getElementsByName("filterUsersBy")[0];
		sortByElement = document.getElementsByName("filterUsersBy")[1];
		findBy = document.getElementsByName("filterUsersBy")[0].value;
		sortBy = document.getElementsByName("filterUsersBy")[1].value;
		
		var startProcess = false;
		var searchForThisUser = document.getElementById("searchForThisUser");
		
		if(findBy != '')
		{
			startProcess = true;
			findUsersByElement.style.borderColor = "initial";
		}
		else
		{
			startProcess = false;
			findUsersByElement.style.borderColor = "red";
		}		
		if(findBy == "search")
		{
			startProcess = false;
			tenantsDivSearch.style.display = "inline-grid";
			searchForThisUser.addEventListener("keyup", function(event)
			{
				if (event.keyCode === 13)
				{
					document.getElementById("tenantsDivSearchButton").click();
				}
			});
			document.getElementById("tbody").innerHTML = '';
			if(searchForThisUser.value != '')
			{
				startProcess = true;
				findBy += "&searchString="+searchForThisUser.value;
				searchForThisUser.style.borderColor = "initial";
			}
			else
			{
				startProcess = false;
				searchForThisUser.style.borderColor = "red";
			}
		}
		else
		{
			startProcess = true;
			tenantsDivSearch.style.display = "none";
		}
			
		
		if(startProcess == true)
		{
			$.ajax({
				url: "getMeTenants.php",
				data: "sortBy="+sortBy+"&findBy="+findBy,
				method: "post"
			}).done(function(response) {
				var data = JSON.parse(response);
				if(data.status == 0)
				{
					document.getElementById("tbody").innerHTML = data.divContent;
				}
			})
		}
	}
</script>

</body>
</html>