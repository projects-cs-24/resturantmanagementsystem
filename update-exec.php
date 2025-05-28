<?php
	//checking connection and connecting to a database
	require_once('connection/config.php');
	//Connect to mysqli server
	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE);
	if(!$conn) {
		die('Failed to connect to server: ' . mysqli_error());
	}
	
 
 //Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
global $conn;
		$str = @trim($str);
		// Removed get_magic_quotes_gpc() check as it is deprecated and removed in PHP 7.4+
		return mysqli_real_escape_string($conn,$str);
	}
	
	//Sanitize the POST values
	$OldPassword = clean($_POST['opassword']);
	$NewPassword = clean($_POST['npassword']);
	$ConfirmNewPassword = clean($_POST['cpassword']);
	
     // check if the 'id' variable is set in URL
     if (isset($_GET['id']))
     {
         // get id value
         $id = $_GET['id'];
         
         // update the entry
         $result = mysqli_query($conn,"UPDATE members SET passwd='".md5($_POST['npassword'])."' WHERE member_id='$id' AND passwd='".md5($_POST['opassword'])."'")
         or die("Password changing failed! Please try again after a few minutes"); 
         
         if($result){
             // redirect back to the member profile
             header("Location: member-profile.php");
         }
         else{
            header("Location: reset-failed.php"); // failed to update password
         }
     }
     else
     // if id isn't set, give an error
     {
        die("Password changing failed! Please try again after a few minutes");
     } 
?>