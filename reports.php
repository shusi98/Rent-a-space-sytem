<?php
include_once 'validateAdmin.php';
include_once 'Constants.php';

/*if(isset($_GET['pid']) && !empty($_GET['pid']))
{
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	
	$propId = mysqli_real_escape_string($con,$_GET["pid"]);

	if(!$con->connect_error)
	{
		$stmt = $con->prepare("select PropertyID,PropertyName,Vaccant,Description,Rent,Address,Province,City from property where PropertyID = ?");
		$stmt->bind_param("i",$propId);
		$stmt->execute();
		$stmt->bind_result($propertyId,$propertyName,$vaccant,$description,$rent,$address,$province,$city);
		mysqli_stmt_store_result($stmt);
		$numReturnedRows = mysqli_stmt_num_rows($stmt);
		$stmt->fetch();
		$con->close();
		if($numReturnedRows > 0)
		{
			$_SESSION["findThisProperty"] = $propertyId;
		}
	}
}
else
{
	header('location: listedProperties.php');
}*/
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent-A-Space | Reports</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
	  <style>
		@page
		{
		  size:A4;
		  margin: 0;
		}
	  </style>
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Lets get you the report you need...</h3>
		</div>
		<div class="d-flex" style="margin-top:15px;">
			<ul class="breadcrumb">
				<li><a href="index.php">Home</a></li>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="reports.php">Reports</a></li>
				<li><a href="" onclick="window.history.go(-1); return false;">Back</a></li>
			</ul>
		</div>
		
		<div class="d-flex" style="margin-top:15px;">
			<div class="py-2">
				<span>Report Type</span>
				<br>
				<select id="reportType" onchange="getReportType()">
					<option disabled selected>Select option</option>
					<option value="tenants">Tenants</option>
					<option value="properties">Properties</option>
					<option value="finances">Finances</option>
				</select>
			</div>
		</div>
		
		<div id="tenantsDiv" class="flex-container">
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
							<option value="dateDesc">Date Joined - descending</option>
							<option value="dateAsc">Date Joined - ascending</option>
						</select>
				</div>
			</div>
			<div  id="tbody" style="margin-top:15px;">
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
			<button onclick="window.print();" class="btn btn-primary" id="print-btn-1">Print</button>
		</div>
		<div id="propertiesDiv" class="flex-container">
			<div class="flex-container">
				<div class="flex-containerDiv">
					<span>Which Properties?</span>
					<select name="filterPropertiesBy" onchange="sortThisPropertyMess()">
						<option disabled selected value ="">Select option</option>
						<option value="all">View All</option>
						<option value="search">Search</option>
						<option value="rentDue">Rent Due</option>
						<option value="vaccant">Vaccant Properties Only</option>
						<option value="occupied">Occupied Properties Only</option>
					</select>
				</div>
				<div class="flex-containerDiv" id="propertiesDivSearch">
					<span>Search Properties</span>
					<input id="searchForThisProperty" type="search">
					<br>
					<button id="propertiesDivSearchButton" onclick="sortThisPropertyMess()">SEARCH</button>
				</div>
				<div class="flex-containerDiv">
					<span>Filter by Region</span>
					<select name="filterPropertiesBy" onchange="sortThisPropertyMess()">
						<option value="all">View All</option>
						<option value="Eastern Cape">Eastern Cape</option>
						<option value="Free State">Free State</option>
						<option value="Gauteng">Gauteng</option>
						<option value="KwaZulu-Natal">KwaZulu-Natal</option>
						<option value="Limpopo">Limpopo</option>
						<option value="Mpumalanga">Mpumalanga</option>
						<option value="North-West">North-West</option>
						<option value="Northern Cape">Northern Cape</option>
						<option value="Western Cape">Western Cape</option>
					</select>
				</div>
				<div class="flex-containerDiv">
					<span>Sort By</span>
					<select name="filterPropertiesBy" onchange="sortThisPropertyMess()">
						<option value="all">Default</option>
						<option value="nameAsc">Name - Ascending</option>
						<option value="nameDesc">Name - Descending</option>
						<option value="highest">Price - highest to lowest</option>
						<option value="lowest">Price - lowest to highest</option>
						<option value="dateAsc">Date Added - ascending</option>
						<option value="dateDesc">Date Added - descending</option>
					</select>
				</div>
			</div>
			<div id="prop-tbody" style="margin-top:15px;">
				<!--
				<table class="table table-striped table-dark">
					<thead class="thead-dark">
						<tr>
							<th>Name</th>
							<th>Vaccant?</th>
							<th>Description</th>
							<th>Rent</th>
							<th>Address</th>
							<th>Date Added</th>
						</tr>
					</thead>

					<tbody id="prop-tbody">
						
					</tbody>
				</table>
				-->
			</div>
			<button onclick="window.print();" class="btn btn-primary" id="print-btn-2">Print</button>
		</div>
		<div id="financesDiv" class="flex-container">
			<div class="flex-containerDiv">
				<span>Which Finance Report?</span>
				<select name="filterFinancesBy" onclick="sortThisFinanceMess()">
					<option disabled selected value ="">Select option</option>
					<option value="all">All Transactions</option>
					<option value="search">Search</option>
				</select>
			</div>
			<div id="financesDivSearch" class="flex-containerDiv">
				<div>
					<span>Search Here</span>
					<input id="searchForThisFinance" type="search">
				</div>
			</div>
			<div class="flex-containerDiv">
				<span>Sort By</span>
				<select name="filterFinancesBy">
					<option value="all">Default</option>
					<option value="pNameAsc">Property Name - Ascending</option>
					<option value="pNameDesc">Property Name - Descending</option>
					<option value="tNameAsc">Tenant Name - Ascending</option>
					<option value="tNameDesc">Tenant Name - Descending</option>
					<option value="emailAsc">Tenant Email - Ascending</option>
					<option value="emailDesc">Tenant Email - Descending</option>
					<option value="amountDueAsc">Amount Due - Ascending</option>
					<option value="amountDueDesc">Amount Due - Descending</option>
					<option value="amountPaidAsc">Amount Paid - Ascending</option>
					<option value="amountPaidDesc">Amount Paid - Descending</option>
					<option value="dateDueAsc">Date Due - Ascending</option>
					<option value="dateDueDesc">Date Due - Descending</option>
					<option value="datePaidAsc">Date Paid - Ascending</option>
					<option value="datePaidDesc">Date Paid - Descending</option>
					<option value="createdAsc">Created - Ascending</option>
					<option value="createdDesc">Created - Descending</option>
					<option value="lastUpdateAsc">Last Update - Ascending</option>
					<option value="lastUpdateDesc">Last Update - Descending</option>
				</select>
			</div>
			<div class="flex-containerDiv">
				<span>Select Dates</span>
				<div>
					<div>
					<label for="dateFrom">From</label>
						<input type="date" id="dateFrom" name="dateFrom">
					</div>
					<div>
					<label for="dateTo">To</label>
						<input type="date" id="dateTo" name="dateTo">
					</div>
				</div>
			</div>
			<div class="flex-containerDiv">
				<span></span>
				<button class="btn btn-success" onclick="sortThisFinanceMess()">Generate Report</button>
			</div>
			
			<div class="d-flex table-data"  style="margin-top:15px;">
				<!---->
				<table class="table table-striped table-dark">
					<thead class="thead-dark">
						<tr>
							<th>Trace ID</th>
							<th>Property</th>
							<th>Tenant Name</th>
							<th>Tenant Email</th>
							<th>Account Active?</th>
							<th>Amount Due</th>
							<th>Amount Paid</th>
							<th>Date Due</th>
							<th>Date Paid</th>
							<th>Created</th>
							<th>Last Update</th>
						</tr>
					</thead>

					<tbody id="finance-tbody">
						
					</tbody>
				</table>
				<!---->
			</div>
			
		<button onclick="window.print();" class="btn btn-primary" id="print-btn-3">Print</button>	
		</div>
		
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script>
	var printBtn1 = document.getElementById("print-btn-1");
	var printBtn2 = document.getElementById("print-btn-2");
	var printBtn3 = document.getElementById("print-btn-3");
	
	printBtn1.style.display = "none";
	printBtn2.style.display = "none";
	printBtn3.style.display = "none";
	
	var tenantsDiv = document.getElementById("tenantsDiv");
	var propertiesDiv = document.getElementById("propertiesDiv");
	var financesDiv = document.getElementById("financesDiv");
	
	tenantsDiv.style.display = "none";
	propertiesDiv.style.display = "none";
	financesDiv.style.display = "none";
	
	document.getElementById("tenantsDivSearch").style.display = "none";
	document.getElementById("propertiesDivSearch").style.display = "none";
	document.getElementById("financesDivSearch").style.display = "none";
	
	function getReportType()
	{
		var reportType = document.getElementById("reportType").value;
		//console.log(reportType);
		switch(reportType)
		{
			case "tenants":
				propertiesDiv.style.display = "none";
				financesDiv.style.display = "none";
				tenantsDiv.style.display = "block";
				break;
			case "properties":
				financesDiv.style.display = "none";
				tenantsDiv.style.display = "none";
				propertiesDiv.style.display = "block";
				break;
			case "finances":
				propertiesDiv.style.display = "none";
				tenantsDiv.style.display = "none";
				financesDiv.style.display = "block";
				break;
		}
	}
	
	var tenantsDivSearch = document.getElementById("tenantsDivSearch");
	tenantsDivSearch.style.display = "none";
	
	var propertiesDivSearch  = document.getElementById("propertiesDivSearch");
	propertiesDivSearch.style.display = "none";
	
	var financesDivSearch  = document.getElementById("financesDivSearch");
	financesDivSearch.style.display = "none";

	
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
					printBtn1.style.display = "block";
				}
			})
		}
	}
	
	function sortThisPropertyMess()
	{
		var sortBy = "";
		var findBy = "";
		findPropertiesByElement = document.getElementsByName("filterPropertiesBy")[0];
		regionPropertiesByElement = document.getElementsByName("filterPropertiesBy")[1];
		sortByElement = document.getElementsByName("filterPropertiesBy")[2];
		
		findBy = document.getElementsByName("filterPropertiesBy")[0].value;
		region = document.getElementsByName("filterPropertiesBy")[1].value;
		sortBy = document.getElementsByName("filterPropertiesBy")[2].value;
		
		var startProcess = false;
		var searchForThisProperty = document.getElementById("searchForThisProperty");
		/*console.log(findBy);
		console.log(region);
		console.log(sortBy);*/
		
		if(findBy != '')
		{
			startProcess = true;
			findPropertiesByElement.style.borderColor = "initial";
		}
		else
		{
			startProcess = false;
			findPropertiesByElement.style.borderColor = "red";
		}
		if(findBy == "search")
		{
			startProcess = false;
			propertiesDivSearch.style.display = "inline-grid";
			searchForThisProperty.addEventListener("keyup", function(event)
			{
				if (event.keyCode === 13)
				{
					document.getElementById("propertiesDivSearchButton").click();
				}
			});
			document.getElementById("prop-tbody").innerHTML = '';
			if(searchForThisProperty.value != '')
			{
				startProcess = true;
				findBy += "&searchString="+searchForThisProperty.value;
				searchForThisProperty.style.borderColor = "initial";
			}
			else
			{
				startProcess = false;
				searchForThisProperty.style.borderColor = "red";
			}
		}
		else
		{
			startProcess = true;
			propertiesDivSearch.style.display = "none";
		}
		
		if(startProcess == true)
		{
			$.ajax({
				url: "getMeProps.php",
				data: "sortBy="+sortBy+"&findBy="+findBy+"&region="+region,
				method: "post"
			}).done(function(response) {
				var data = JSON.parse(response);
				if(data.status == 0)
				{
					document.getElementById("prop-tbody").innerHTML = data.divContent;
					printBtn2.style.display = "block";
				}
			})
		}
	}

	function sortThisFinanceMess()
	{
		var dateFromField = document.getElementById("dateFrom");
		var dateToField = document.getElementById("dateTo");
		var dateFrom = document.getElementById("dateFrom").value;
		var dateTo = document.getElementById("dateTo").value;
		
		var sortBy = "";
		var findBy = "";
		var datesBetween = "";
		
		
		findFinancesByElement = document.getElementsByName("filterFinancesBy")[0];
		sortByElement = document.getElementsByName("filterFinancesBy")[1];
		findBy = document.getElementsByName("filterFinancesBy")[0].value;
		sortBy = document.getElementsByName("filterFinancesBy")[1].value;
		
		/*console.log("From => "+dateFrom);
		console.log("To => "+dateTo);*/
		
		var startProcess = false;
		var searchForThisFinance = document.getElementById("searchForThisFinance");
		/*console.log(findBy);
		console.log(sortBy);*/
		
		if(findBy != '')
		{
			startProcess = true;
			findFinancesByElement.style.borderColor = "initial";
		}
		else
		{
			startProcess = false;
			findFinancesByElement.style.borderColor = "red";
		}
		if(findBy == "search")
		{
			startProcess = false;
			financesDivSearch.style.display = "inline-grid";
			searchForThisFinance.addEventListener("keyup", function(event)
			{
				if (event.keyCode === 13)
				{
					sortThisFinanceMess();
				}
			});
			document.getElementById("finance-tbody").innerHTML = '';
			if(searchForThisFinance.value != '')
			{
				startProcess = true;
				findBy += "&searchString="+searchForThisFinance.value;
				searchForThisFinance.style.borderColor = "initial";
			}
			else
			{
				startProcess = false;
				searchForThisFinance.style.borderColor = "red";
			}
		}
		else
		{
			startProcess = true;
			financesDivSearch.style.display = "none";
		}
		
		if(dateTo != '' && dateFrom != '')
		{
			dateFromField.style.borderColor = "initial";
			dateToField.style.borderColor = "initial";
			datesBetween = "&dateFrom="+dateFrom+"&dateTo="+dateTo;
			startProcess = true;
		}
		else if(dateTo != '' && dateFrom == '')
		{
			dateFromField.style.borderColor = "red";
			dateToField.style.borderColor = "initial";
			startProcess = false;
		}
		else if(dateTo == '' && dateFrom != '')
		{
			dateFromField.style.borderColor = "initial";
			dateToField.style.borderColor = "red";
			startProcess = false;
		}
			
		if(startProcess == true)
		{
			$.ajax({
				url: "getMeFinances.php",
				data: "sortBy="+sortBy+"&findBy="+findBy+datesBetween,
				method: "post"
			}).done(function(response) {
				var data = JSON.parse(response);
				if(data.status == 0)
				{
					document.getElementById("finance-tbody").innerHTML = data.divContent;
					printBtn3.style.display = "block";
				}
			})
		}
	}

	
	
	
</script>

</body>
</html>