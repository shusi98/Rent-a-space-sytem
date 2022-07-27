<?php
include_once 'validateAdmin.php';

$errorMessage = "";
//$propertyName=$propertyDescription=$propertyRent=$propertyAddress="";

$error = array();
$fieldErrors = false;

include_once 'Constants.php';

if(isset($_GET["lipid"]) && !empty($_GET['lipid']))
{
	
	$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
	$propertyId = mysqli_real_escape_string($con,$_GET["lipid"]);
	
	if(!$con->connect_error)
	{
		$stmt = $con->prepare("select PropertyID,PropertyName,Vaccant,Description,Rent,Address,Province,City from property where PropertyID = ?");
		$stmt->bind_param("i",$propertyId);
		$stmt->execute();
		$stmt->bind_result($propertyId,$propertyName,$vaccant,$description,$rent,$address,$province,$city);
		mysqli_stmt_store_result($stmt);
		$numReturnedRows = mysqli_stmt_num_rows($stmt);
		$stmt->fetch();
		$con->close();
		/*if($numReturnedRows < 1)
		{
			$errorMessage = "You have used an incorrect link.";
		}*/
	}
}	

if(isset($_POST["addImage"]))
{
	if($_FILES["image"]["size"] == 0){		
		$error["image"] = true;
		$errorMessage = "You have not selected an image";
	} 
	else
	{
		$error["image"] = false;
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
			$file = addslashes(file_get_contents($_FILES["image"]["tmp_name"]));  
			$stmt= "update image set Image = '$file' where PropertyID = '$propertyId'";
			if(mysqli_query($con, $stmt))  
			{
				$errorMessage = "Success!";
				mysqli_close($con);
				header( "Refresh:2.5; url=property.php?pid=$propertyId", true, 303);
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
    <title>Rent-A-Space | Add Image</title>
	
	<script src="https://kit.fontawesome.com/b1131a5ec7.js" crossorigin="anonymous"></script>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

      <link rel ="stylesheet" href="style.css">  
</head>
<body>

<main>
	<div class="container">
		<h1 class="py-4 bg-dark text-light rounded " style="text-align:center;"><i class ="fas fa-swatchbook"></i>Rent-A-Space</h1>
		<div class="d-flex" style="margin-top:15px;">
			<h3>Add Property Image</h3>
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
		<div class="d-flex"	style="margin-top:15px;">
			<?php
			echo '<div class="d-flex" style="margin-top:15px;">Property name: '. $propertyName.'<br>'.'Vaccant: '.$vaccant.'<br>'.'Description: '.$description.'<br>'.'Rent: R '.$rent.'<br>'.'Address: '.$address.'<br>'.'City: '.$city.'<br>'.'Province: '.$province.'</div>';
			?>
		</div>
		<div class="d-flex">
			<form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]);?>" method="post" class="w-50" enctype="multipart/form-data">
				<div class="py-2" >
					<span>Select Image</span>
					<div class="input-group mb-2">
						<input type="file" name="image" id="image" /> 
					</div>
					<div class="d-flex justify-content-left">
						<button name="addImage"  class="btn btn-success" id="">ADD IMAGE</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>

<script type="text/javascript">
$(document).ready(function(){
	$('#insert').click(function(){
		var image_name = $('#image').val();  
		if(image_name == '')  
		{  
			alert("Please Select Image");  
			return false;  
		}  
		else  
		{  
			var extension = $('#image').val().split('.').pop().toLowerCase();  
			if(jQuery.inArray(extension, ['gif','png','jpg','jpeg']) == -1)  
			{  
				 alert('Invalid Image File');  
				 $('#image').val('');  
				 return false;  
			}  
		}  
	});  
});
</script>

</body>
</html>