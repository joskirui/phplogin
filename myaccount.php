<?php 
include 'dbc.php';
page_protect();


?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

  <!-- fontawesome CSS -->
  <link href="fontawesome/css/all.css" rel="stylesheet"> <!--load all styles -->
  
<title>My Account</title>

<link href="css/styles.css" rel="stylesheet" type="text/css">
</head>

<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="myaccount.php">PHP Login</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="myaccount.php">Dashboard <span class="sr-only">(current)</span></a>
      </li>
    </ul>
  </div>
</nav>
<div class="container-fluid">
<div class="row">
<div class="col-md-2">
<?php 
/*********************** MYACCOUNT MENU ****************************
This code shows my account menu only to logged in users. 
Copy this code till END and place it in a new html or php where
you want to show myaccount options. This is only visible to logged in users
*******************************************************************/
if (isset($_SESSION['user_id'])) {?>
<p><strong>My Account</strong></p>
<div class="list-group">
  <a href="myaccount.php" class="list-group-item list-group-item-action active"><i class="fas fa-user"></i> My Account</a><br>
  <a href="mysettings.php" class="list-group-item list-group-item-action "><i class="fas fa-cog"></i> Settings</a><br>
<?php 
if (checkAdmin()) {
  echo '<a href="admin.php"  class="list-group-item list-group-item-action"><i class="fas fa-users"></i> Admin CP </a>';   
    } 
  ?>
    <a href="logout.php" class="list-group-item list-group-item-action"><i class="fas fa-sign-out-alt text-danger"></i> Logout </a>
   </div>
  <?php } ?> 
  
  </div>

  <div class="col-md-10">
      <h3 class="titlehdr">Welcome <?php echo $_SESSION['user_name'];?></h3>  
	  <?php	
      if (isset($_GET['msg'])) {
	  echo "<div class=\"error\">$_GET[msg]</div>";
	  }
	  	  
	  ?>
      <p>This is the my account page</p>
</div>
</div>
	 

</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery-3.4.1.min.js"></script>
     <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  
</body>
</html>
