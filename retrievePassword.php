<?php
	//session_start();
	include_once 'Constants.php';
	if(!empty($_POST["userEmail"]))
	{
		$userEmail = test_input($_POST["userEmail"]);
		if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
		{
			$errorMessage = "Invalid e-mail - " .$userEmail;
			echo json_encode( ["status" => 0, "comment" => $errorMessage] );
			exit;
		}
		else
		{
			$con = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
			$stmt = $con->prepare("select OTP from user where Email = ?");
			$stmt->bind_param("s",$userEmail);
			$stmt->execute();
			$stmt->bind_result($userOTP);
			mysqli_stmt_store_result($stmt);
			$numReturnedRows = mysqli_stmt_num_rows($stmt);
			$stmt->fetch();
			$con->close();
			if($numReturnedRows<1)
			{
				$errorMessage = "Unknown user, you might have mistyped your email address";
				echo json_encode( ["status" => 1, "comment" => $errorMessage] );
				exit;
			}
			else
			{
				do
				{
					$newOneTimePin = rand(1000,99999);
				}
				while($newOneTimePin == $userOTP);
				$conn = new mysqli($DB_HOST,$DB_USER,$DB_PASSWORD,$DB_NAME);
				$stmt = $conn->prepare("update user set  OTP = ? where Email = ?");
				$stmt->bind_param("ss",$newOneTimePin,$userEmail);
				if($stmt->execute())
				{
					$conn->close();
	$message =  '
	
	Good day.
	
	Did you request a new password?
	This email was sent after you requested to reset your password.
	 
	------------------------
	E-mail: '.$userEmail.'
	OTP: '.$newOneTimePin.'
	------------------------
				 
	Use the above EMAIL and OTP (One-Time-Pin) to create a new PASSWORD using this link:
	'.$OUR_WEB_ADDRESS.'/resetPassword.php?email='.$userEmail.'
	
	
	
	Thank you.
	
	';
					if(mail($userEmail, "Recover Your Password", $message, $NO_REPLY_EMAIL_ADDRESS))
					{
						$errorMessage = "Password recovery email sent";
						echo json_encode( ["status" => 2, "comment" => $errorMessage] );
						exit;
					}
					else
					{
						$errorMessage = "Error, please try again";
						echo json_encode( ["status" => 3, "comment" => $errorMessage] );
						exit;
					}
				}
				else
				{
					$conn->close();
				}
				
			}
		}
	}