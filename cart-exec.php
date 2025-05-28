<?php
    //Start session
    session_start();
    
    //Include session details
    require_once('auth.php');
    
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
    
    //checks if id is set in the url
    if(isset($_GET['id'])){
        //retrive the first quantity from the quantities table
        $quantities=mysqli_query($conn,"SELECT * FROM quantities")
        or die("Something is wrong ... \n" . mysqli_error()); 
        $row=mysqli_fetch_assoc($quantities);
        $quantity_value = $row['quantity_value'];
        
        //get id value
        $food_id = $_GET['id'];
        $lt = $_GET['lt'];
        
        //retrive food_price from food_details based on $food_id
        if($lt == 'food')
        $result=mysqli_query($conn,"SELECT * FROM food_details WHERE food_id='$food_id'") or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours.");
        else
        $result=mysqli_query($conn,"SELECT * FROM specials WHERE special_id='$food_id'") or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours.");

        $food_row=mysqli_fetch_assoc($result);
        $food_price=$food_row[$lt.'_price'];
        
        //get member_id from session
        $member_id = $_SESSION['SESS_MEMBER_ID'];
        
        //define default values for quantity(got from $row), total($food_price*$quantity_value), and flag_0
        $quantity_id = $row['quantity_id'];
        $total = $food_price*$quantity_value;
        $flag_0 = 0;
        

        //Create INSERT query
        $qry = "INSERT INTO cart_details(member_id, food_id, quantity_id, total, flag,lt) VALUES('$member_id','$food_id','$quantity_id','$total','$flag_0','$lt')";
        $result = @mysqli_query($conn,$qry);
        
        //Check whether the query was successful or not
        if($result) {
            header("location: cart.php");
            exit();
        }else {
            die("A problem has occured with the system " . mysqli_error());
        }
    }
 ?>