<?php
	//Start session
	session_start();
	
	//Include database connection details
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
		
	//checks whether submit is set
	if(isset($_POST['Submit']))
	{
	    $member_id = $_SESSION['SESS_MEMBER_ID']; //gets member id from session
        $food_id = clean($_POST['food']); //gets food id and sanitizes post value
        $scale_id = clean($_POST['scale']); //gets scale id and sanitizes post value
        
        //check whether there is duplication in the polls_details table
        $check = mysqli_query($conn,"SELECT * FROM polls_details WHERE member_id='$member_id' AND food_id='$food_id'") or die("Something is wrong.\n Our team is working on it at the moment.\n Please try again after some few minutes.");
        
        if(mysqli_num_rows($check)>0){
            header("location: ratings-failed.php");
        }
        else{
	        //Create INSERT query
	        $qry = "INSERT INTO polls_details(member_id,food_id,rate_id) VALUES('$member_id','$food_id','$scale_id')";
	        mysqli_query($conn,$qry);
	        
            if($qry){
	            header("location: ratings-success.php");
            }
            else{
                die("Rating failed! Please try again after some few minutes.");
            }
        }

	}else {
		die("Rating failed! Please try again after some few minutes.");
	}
?>
