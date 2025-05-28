<?php
    //Start session
    session_start();
    
    require_once('auth.php');
    
    //Include database connection details
    require_once('connection/config.php');
    
    //Connect to mysqli server
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE);
    if(!$conn) {
        die('Failed to connect to server: ' . mysqli_error());
    }
    
    //Select database
    
    
    //Function to sanitize values received from the form. Prevents SQL injection
    function clean($str) {
global $conn;
		$str = @trim($str);
        // Removed get_magic_quotes_gpc() check as it is deprecated and removed in PHP 7.4+
        return mysqli_real_escape_string($conn,$str);
    }
    
    if(isset($_POST['quantity']) && isset($_POST['item']))
        {
            //get quantity_id
            $quantity_id = clean($_POST['quantity']);
                
            //get member_id from session
            $member_id = $_SESSION['SESS_MEMBER_ID'];
            
            //get cart_id
            $cart_id = clean($_POST['item']);
            //$cart_id = 5;
            
            //get the quantity value based on quantity_id
            $qry_select=mysqli_query($conn,"SELECT * FROM quantities WHERE quantity_id='$quantity_id'")
            or die("The system is experiencing technical issues. Please try again after some few minutes.");
            
            //storing the quantity_value into a variable
            $row=mysqli_fetch_array($qry_select);
            $quantity_value=$row['quantity_value'];
            
            //get the price of a food based on cart_details and food_details tables
            $cdq = mysqli_query($conn,"SELECT * FROM cart_details where cart_id = '$cart_id'") or die("Error : SELECT * FROM cart_details where cart_id = $cart_id");
            $res = mysqli_fetch_array($cdq);
            $lt = $res['lt'];
            if($lt == 'food')
            $result=mysqli_query($conn,"SELECT * FROM food_details where food_id = {$res['food_id']} ") or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours."); 
            else
            $result=mysqli_query($conn,"SELECT * FROM specials where special_id = {$res['food_id']} ") or die("A problem has occured ... \n" . "Our team is working on it at the moment ... \n" . "Please check back after few hours."); 

            
            //storing the value of food price into a variable
            $row=mysqli_fetch_array($result);
            $food_price=$row[$lt.'_price'];
            
            //perform a simple calculation to get a total value of a food based on quantity_value and food_price
            $total = $quantity_value * $food_price;
            
            //Create UPDATE query (updates total and quantity_id in the cart based on cart_id and member_id)
            $qry_update = "UPDATE cart_details SET quantity_id='$quantity_id', total='$total' WHERE cart_id='$cart_id' AND member_id='$member_id'";
            mysqli_query($conn,$qry_update);
            
            if($qry_update){
                header("location: cart.php");
            }
            else{
                //Do nothing
            }
            
        }else {
            die("Something went wrong! Our technical team are working on solving the problem. Please try again after few minutes.");
        }
?>