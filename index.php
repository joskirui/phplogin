<?php 
include 'dbc.php';

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

<title>Welcome</title>

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
  <a class="navbar-brand" href="#">PHP Login</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
      </li>
    </ul>
  </div>
</nav>
<div class="container">

<h3 class="titlehdr">PHP, MySQLi, Bootstrap 4.3.1 & Jquery 3.4.1</h3>
<p>jQuery validation plug-in 1.5.5</p>
<p> Font awesome Version 5.9.0 </p>
<p>popper js version 1.14.7</p>

<pre>
Original script https://github.com/olddocks/phploginscript
i found the above script to be simple membership script for beginners but it was too old so i had to update it

--replaced table layout with bootstrap grid layout 
--replaced ereg with preg 
--replaced mysql with mysqli
--upgraded jquery from 1.3.1 to Jquery 3.4.1

added countries https://gist.github.com/adhipg/1600028
and flags https://github.com/yusufshakeel/mysql-country-with-flag

--hopefully do more updates in the future
Credits to the original owner 
</pre>

<?php

$result = mysqli_query($link,"select * from country where name like'KENYA'") or die (mysqli_error($link));
$row=mysqli_fetch_assoc($result);
$code = $row['iso'];
$country=$row['name'];
$flag_image_link = 'images/flags/' . $code . '.png';

echo"<p>$country <img  src=$flag_image_link /></p>";
?>
 <a href="login.php" class="btn btn-secondary">Go to Login</a> 
  
 
	 
</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery-3.4.1.min.js"></script>
     <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  
</body>
</html>
