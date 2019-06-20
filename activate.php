<?php 
include 'dbc.php';

foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

/******** EMAIL ACTIVATION LINK**********************/
if(isset($get['user']) && !empty($get['activ_code']) && !empty($get['user']) && is_numeric($get['activ_code']) ) {

$err = array();
$msg = array();

$user = mysqli_real_escape_string($link,$get['user']);
$activ = mysqli_real_escape_string($link,$get['activ_code']);

//check if activ code and user is valid
$rs_check = mysqli_query($link,"select id from users where md5_id='$user' and activation_code='$activ'") or die (mysqli_error($link)); 
$num = mysqli_num_rows($rs_check);
  // Match row found with more than 1 results  - the user is authenticated. 
    if ( $num <= 0 ) { 
	$err[] = "Sorry no such account exists or activation code invalid.";
	//header("Location: activate.php?msg=$msg");
	//exit();
	}

if(empty($err)) {
// set the approved field to 1 to activate the account
$rs_activ = mysqli_query($link,"update users set approved='1' WHERE 
						 md5_id='$user' AND activation_code = '$activ' ") or die(mysqli_error($link));
$msg[] = "Thank you. Your account has been activated.";
//header("Location: activate.php?done=1&msg=$msg");						 
//exit();
 }
}

/******************* ACTIVATION BY FORM**************************/
if (@$_POST['doActivate']=='Activate')
{
$err = array();
$msg = array();

$user_email = mysqli_real_escape_string($link,$_POST['user_email']);
$activ = mysqli_real_escape_string($link,$_POST['activ_code']);
//check if activ code and user is valid as precaution
$rs_check = mysqli_query($link,"select id from users where user_email='$user_email' and activation_code='$activ'") or die (mysqli_error($link)); 
$num = mysqli_num_rows($rs_check);
  // Match row found with more than 1 results  - the user is authenticated. 
    if ( $num <= 0 ) { 
	$err[] = "Sorry no such account exists or activation code invalid.";
	//header("Location: activate.php?msg=$msg");
	//exit();
	}
//set approved field to 1 to activate the user
if(empty($err)) {
	$rs_activ = mysqli_query($link,"update users set approved='1' WHERE 
						 user_email='$user_email' AND activation_code = '$activ' ") or die(mysqli_error($link));
	$msg[] = "Thank you. Your account has been activated.";
 }
//header("Location: activate.php?msg=$msg");						 
//exit();
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

<title>User Account Activation</title>

<script language="JavaScript" type="text/javascript" src="js/jquery.validate.js"></script>
  <script>
  $(document).ready(function(){
    $("#actForm").validate();
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

<h3 class="titlehdr">Account Activation</h3>

    
        <?php
	  /******************** ERROR MESSAGES*************************************************
	  This code is to show error messages 
	  **************************************************************************/
	if(!empty($err))  {
	   echo "<div class=\"alert alert-danger\">";
	  foreach ($err as $e) {
	    echo "* $e <br>";
	    }
	  echo "</div>";	
	   }
	   if(!empty($msg))  {
	    echo "<div class=\"alert alert-success\">" . $msg[0] . "</div>";

	   }	
	  /******************************* END ********************************/	  
	  ?>
   
      <p>Please enter your email and activation code sent to you to your email 
        address to activate your account. Once your account is activated you can 
        <a href="login.php">login here</a>.</p>
	 
      <form action="activate.php" method="post" name="actForm" id="actForm" >
          
      <div class="form-group row">
      <label  class="col-sm-2 col-form-label" for="user_email"> Your Email</label>
            <div class="col-sm-10">
            <input name="user_email" type="text" class="form-control required email" id="user_email" size="25">
            </div>
                  </div>
            
          
            <div class="form-group row">  
            <label  class="col-sm-2 col-form-label" for="activ_code">Activation code </label>
            <div class="col-sm-10">
            <input name="activ_code" type="password" class="form-control required" id="activ_code" size="25">
            </div>
                  </div>
          
           
            <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
                  <input name="doActivate" type="submit" id="doLogin3" value="Activate" class="btn btn-primary">
                  </div>
                  </div>
                
      </form>
	  

</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery-3.4.1.min.js"></script>
     <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  
</body>
</html>
