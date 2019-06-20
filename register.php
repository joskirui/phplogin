<?php 
/*************** PHP LOGIN SCRIPT V 2.0*********************
***************** Auto Approve Version**********************
(c) Balakrishnan 2009. All Rights Reserved

Usage: This script can be used FREE of charge for any commercial or personal projects.

Limitations:
- This script cannot be sold.
- This script may not be provided for download except on its original site.

For further usage, please contact me.

***********************************************************/


include 'dbc.php';

$err = array();
					 
if(@$_POST['doRegister'] == 'Register') 
{ 
/******************* Filtering/Sanitizing Input *****************************
This code filters harmful script code and escapes data of all POST data
from the user submitted form.
*****************************************************************/
foreach($_POST as $key => $value) {
	$data[$key] = filter($value);
}

/********************* RECAPTCHA CHECK *******************************
This code checks and validates recaptcha
****************************************************************/
/*
 require_once('recaptchalib.php');
     
      $resp = recaptcha_check_answer ($privatekey,
                                      $_SERVER["REMOTE_ADDR"],
                                      $_POST["recaptcha_challenge_field"],
                                      $_POST["recaptcha_response_field"]);

      if (!$resp->is_valid) {
        die ("<h3>Image Verification failed!. Go back and try again.</h3>" .
             "(reCAPTCHA said: " . $resp->error . ")");			
      }
/************************ SERVER SIDE VALIDATION **************************************/
/********** This validation is useful if javascript is disabled in the browswer ***/

if(empty($data['full_name']) || strlen($data['full_name']) < 4)
{
$err[] = "ERROR - Invalid name. Please enter atleast 3 or more characters for your name";
//header("Location: register.php?msg=$err");
//exit();
}

// Validate User Name
if (!isUserID($data['user_name'])) {
$err[] = "ERROR - Invalid user name. It can contain alphabet, number and underscore.";
//header("Location: register.php?msg=$err");
//exit();
}

// Validate Email
if(!isEmail($data['usr_email'])) {
$err[] = "ERROR - Invalid email address.";
//header("Location: register.php?msg=$err");
//exit();
}
// Check User Passwords
if (!checkPwd($data['pwd'],$data['pwd2'])) {
$err[] = "ERROR - Invalid Password or mismatch. Enter 5 chars or more";
//header("Location: register.php?msg=$err");
//exit();
}
	  
$user_ip = $_SERVER['REMOTE_ADDR'];

// stores sha1 of password
$sha1pass = PwdHash($data['pwd']);

// Automatically collects the hostname or domain  like example.com) 
$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

// Generates activation code simple 4 digit number
$activ_code = rand(1000,9999);

$usr_email = $data['usr_email'];
$user_name = $data['user_name'];

/************ USER EMAIL CHECK ************************************
This code does a second check on the server side if the email already exists. It 
queries the database and if it has any existing email it throws user email already exists
*******************************************************************/

$rs_duplicate = mysqli_query($link,"select count(*) as total from users where user_email='$usr_email' OR user_name='$user_name'") or die(mysqli_error($link));
list($total) = mysqli_fetch_row($rs_duplicate);

if ($total > 0)
{
$err[] = "ERROR - The username/email already exists. Please try again with different username and email.";
//header("Location: register.php?msg=$err");
//exit();
}
/***************************************************************************/

if(empty($err)) {

$sql_insert = "INSERT into `users`
  			(`full_name`,`user_email`,`pwd`,`address`,`tel`,`fax`,`website`,`date`,`users_ip`,`activation_code`,`country`,`user_name`
			)
		    VALUES
		    ('$data[full_name]','$usr_email','$sha1pass','$data[address]','$data[tel]','$data[fax]','$data[web]'
			,now(),'$user_ip','$activ_code','$data[country]','$user_name'
			)
			";
			
mysqli_query($link,$sql_insert) or die("Insertion Failed:" . mysqli_error($link));
$user_id = mysqli_insert_id($link);  
$md5_id = md5($user_id);
mysqli_query($link,"update users set md5_id='$md5_id' where id='$user_id'");
//	echo "<h3>Thank You</h3> We received your submission.";

if($user_registration)  {
$a_link = "
*****ACTIVATION LINK*****\n
http://$host$path/activate.php?user=$md5_id&activ_code=$activ_code
"; 
} else {
$a_link = 
"Your account is *PENDING APPROVAL* and will be soon activated the administrator.
";
}

$message = 
"Hello \n
Thank you for registering with us. Here are your login details...\n

User ID: $user_name
Email: $usr_email \n 
Passwd: $data[pwd] \n

$a_link

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

	mail($usr_email, "Login Details", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion());

  header("Location: thankyou.php");  
  exit();
	 
	 } 
 }					 

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

<title>PHP Login :: Free Registration/Signup Form</title>

<script language="JavaScript" type="text/javascript" src="js/jquery.validate.js"></script>

  <script>
  $(document).ready(function(){
    $.validator.addMethod("username", function(value, element) {
        return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
    }, "Username must contain only letters, numbers, or underscore.");

    $("#regForm").validate();
  });
  </script>

<link href="css/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="index.php">PHP Login</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
      </li>
    </ul>
  </div>
</nav>
<div class="container">

	<?php 
	 if (isset($_GET['done'])) { ?>
   <div class="alert alert-success">
	  <h2>Thank you</h2> Your registration is now complete and you can <a href="login.php">login here</a>";
    </div>
	 <?php exit();
	  }
	?></p>
      <h3 class="titlehdr">Free Registration / Signup</h3>
      <p>Please register a free account, before you can start posting your ads. 
        Registration is quick and free! Please note that fields marked <span class=""  required>*</span> 
        are required.</p>
	 <?php	
	 if(!empty($err))  {
	   echo "<div class=\"alert alert-danger\">";
	  foreach ($err as $e) {
	    echo "* $e ";
	    }
	  echo "</div>";	
	   }
	 ?> 
	 
	  
      <form action="register.php" method="post" name="regForm" id="regForm" >
       
      <div class="form-group row">
      <label for="full_name" class="col-sm-2 col-form-label">
      Your Name / Company Name<span class=""  required><font color="#CC0000">*</font></span></label>  
            <div class="col-sm-10">
            <input name="full_name" type="text" id="full_name" size="40" class="form-control "  required>
              </div>
              </div>
          
              <div class="form-group row">
              <label for="address" class="col-sm-2 col-form-label">
              Contact Address (with ZIP)<span class=""  required><font color="#CC0000">*</font></span> </label> 
            <div class="col-sm-10">
            <textarea name="address" cols="40" rows="4" id="address" class="form-control "  required></textarea> 
              <span class="example">VALID CONTACT DETAILS</span> 
              </div>
              </div>
          
              <div class="form-group row">
              <label for="country" class="col-sm-2 col-form-label">
              Country <font color="#CC0000">*</font></span></label> 
            <div class="col-sm-10">
            <select name="country" class="form-control "  required id="country">
            <option value="" selected></option>
              <?php

              $result = mysqli_query($link,"select * from country where 1") or die (mysqli_error($link));
              while($row=mysqli_fetch_assoc($result))
              {
              $country=$row['nicename'];
              echo '<option value="'.$country.'">'.$country.'</option>';
              }
              ?>
              </select>
              </div>
              </div>
          
              <div class="form-group row">
              <label for="tel" class="col-sm-2 col-form-label">
              Phone<span class=""  required><font color="#CC0000">*</font></span></label> 
            <div class="col-sm-10"> 
            <input name="tel" type="text" id="tel" class="form-control "  required>
            </div>
            </div>
          
            <div class="form-group row">
            <label for="fax" class="col-sm-2 col-form-label">Fax </label> 
            <div class="col-sm-10">
            <input name="fax" type="text" id="fax" class="form-control ">
            </div>
            </div>
          
            <div class="form-group row">
            <label for="web" class="col-sm-2 col-form-label">Website</label>  
            <div class="col-sm-10">
            <input name="web" type="text" id="web" class="form-control optional defaultInvalid url"> 
              <span class="example">http://www.example.com</span>
          </div>
          </div>
           
            
          
           
            <h4><strong>Login Details</strong></h4>
          
            <div class="form-group row">
            <label for="user_name" class="col-sm-2 col-form-label">
            Username<span class=""  required><font color="#CC0000">*</font></span></label> 
            <div class="col-sm-10">
            <input name="user_name" type="text" id="user_name" class="form-control required username" minlength="5" > 
              <input name="btnAvailable" type="button" id="btnAvailable" 
			  onclick='$("#checkid").html("Please wait..."); $.get("checkuser.php",{ cmd: "check", user: $("#user_name").val() } ,function(data){  $("#checkid").html(data); });'
			  value="Check Availability"> 
			    <span style="color:red; font: bold 12px verdana; " id="checkid" ></span> 
           </div>
           </div> 
          
          <div class="form-group row">
          <label for="usr_email3" class="col-sm-2 col-form-label">
          Your Email<span class=""  required><font color="#CC0000">*</font></span></label>   
            <div class="col-sm-10"> 
            <input name="usr_email" type="email" id="usr_email3" class="form-control required email"> 
              <span class="example">** Valid email please..</span>
              </div>
              </div>
          
              <div class="form-group row">
              <label for="pwd" class="col-sm-2 col-form-label">
              Password<span class=""  required><font color="#CC0000">*</font></span></label>  
            <div class="col-sm-10">
            <input name="pwd" type="password" class="form-control required password" minlength="5" id="pwd"> 
              <span class="example">** 5 chars minimum..</span>
              </div>
              </div>
          
              <div class="form-group row">
              <label for="pwd2" class="col-sm-2 col-form-label">
              Retype Password<span class=""  required><font color="#CC0000">*</font></span></label> 
            <div class="col-sm-10">
            <input name="pwd2"  id="pwd2" class="form-control required password" type="password" minlength="5" equalto="#pwd">
          </div>
          </div>

            <strong>Image Verification </strong> <small> Recapture has been disabled </small>
            
              <?php 
			//require_once('recaptchalib.php');
			
			//	echo recaptcha_get_html($publickey);
			?>
            
          
       
            <div class="form-group row">
    <div class="col-sm-10 offset-sm-2">
          <input name="doRegister" type="submit" id="doRegister" value="Register" class="btn btn-primary">
        </div>
        </div>
      </form>
      <p align="right"><span style="font: normal 9px verdana">Powered by <a href="http://php-login-script.com">PHP 
                  Login Script v2.0</a></span></p>
	   
      
</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery-3.4.1.min.js"></script>
     <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  
</body>
</html>
