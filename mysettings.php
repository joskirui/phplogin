<?php 
/********************** MYSETTINGS.PHP**************************
This updates user settings and password
************************************************************/
include 'dbc.php';
page_protect();

$err = array();
$msg = array();

if(@$_POST['doUpdate'] == 'Update')  
{


$rs_pwd = mysqli_query($link,"select pwd from users where id='$_SESSION[user_id]'");
list($old) = mysqli_fetch_row($rs_pwd);
$old_salt = substr($old,0,9);

//check for old password in md5 format
	if($old === PwdHash($_POST['pwd_old'],$old_salt))
	{
	$newsha1 = PwdHash($_POST['pwd_new']);
	mysqli_query($link,"update users set pwd='$newsha1' where id='$_SESSION[user_id]'");
	$msg[] = "Your new password is updated";
	//header("Location: mysettings.php?msg=Your new password is updated");
	} else
	{
	 $err[] = "Your old password is invalid";
	 //header("Location: mysettings.php?msg=Your old password is invalid");
	}

}

if(@$_POST['doSave'] == 'Save')  
{
// Filter POST data for harmful code (sanitize)
foreach($_POST as $key => $value) {
	$data[$key] = filter($value);
}


mysqli_query($link,"UPDATE users SET
			`full_name` = '$data[name]',
			`address` = '$data[address]',
			`tel` = '$data[tel]',
			`fax` = '$data[fax]',
			`country` = '$data[country]',
			`website` = '$data[web]'
			 WHERE id='$_SESSION[user_id]'
			") or die(mysqli_error($link));

//header("Location: mysettings.php?msg=Profile Sucessfully saved");
$msg[] = "Profile Sucessfully saved";
 }
 
$rs_settings = mysqli_query($link,"select * from users where id='$_SESSION[user_id]'"); 
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
  
<title>My Account Settings</title>

<script language="JavaScript" type="text/javascript" src="js/jquery.validate.js"></script>
  <script>
  $(document).ready(function(){
    $("#myform").validate();
	 $("#pform").validate();
  });
  </script>
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
    <a href="myaccount.php" class="list-group-item list-group-item-action "><i class="fas fa-user"></i> My Account</a><br>
    <a href="mysettings.php" class="list-group-item list-group-item-action active"><i class="fas fa-cog"></i> Settings</a><br>
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
<h3 class="titlehdr">My Account - Settings</h3>
      <p> 
        <?php	
	if(!empty($err))  {
	   echo "<div class=\"alert alert-danger\">";
	  foreach ($err as $e) {
	    echo "* Error - $e <br>";
	    }
	  echo "</div>";	
	   }
	   if(!empty($msg))  {
	    echo "<div class=\"alert alert-success\">" . $msg[0] . "</div>";

	   }
	  ?>
      </p>
      <p>Here you can make changes to your profile. Please note that you will 
        not be able to change your email which has been already registered.</p>
	  <?php while ($row_settings = mysqli_fetch_array($rs_settings)) {?>
      <form action="mysettings.php" method="post" name="myform" id="myform">

      <div class="form-group row">
      <label for="name" class="col-sm-2 col-form-label">Your Name / Company Name</label>
             <div class="col-sm-10">
            <input name="name" type="text" id="name"  class="form-control" required value="<?php echo $row_settings['full_name']; ?>" size="50"> 
              <span class="example">Your name or company name</span>
              </div>
            </div>

              <div class="form-group row">
              <label for="address" class="col-sm-2 col-form-label">Address </label>
            <div class="col-sm-10">
              <textarea name="address" cols="40" rows="4" class="form-control" required  id="address"><?php echo $row_settings['address']; ?></textarea> 
              <span class="example">(full address with ZIP)</span>
              </div>
            </div>
          
              <div class="form-group row">
              <label for="country" class="col-sm-2 col-form-label">Country</label>
            <div class="col-sm-10">
            <select name="country" class="form-control "  required id="country">
            <option value="" selected></option>
              <?php

              $result = mysqli_query($link,"select * from country where 1") or die (mysqli_error($link));
              while($row=mysqli_fetch_assoc($result))
              {
              $country=$row['nicename'];
              $selected="";
              if($country==$row_settings['country'])
              $selected=" selected='selected'";
              echo '<option value="'.$country.'" '.$selected.'>'.$country.'</option>';
              }
              ?>
              </select>
            </div>
            </div>
           
            <div class="form-group row">
            <label for="tel" class="col-sm-2 col-form-label">Phone</label>
            <div class="col-sm-10">            
            <input class="form-control" name="tel" type="text" id="tel" class="required" value="<?php echo $row_settings['tel']; ?>">
            </div>
            </div>

            <div class="form-group row">
            <label for="fax" class="col-sm-2 col-form-label">Fax</label>
            <div class="col-sm-10">
            <input class="form-control" name="fax" type="text" id="fax" value="<?php echo $row_settings['fax']; ?>">
            </div>
            </div>

            <div class="form-group row">
            <label for="web" class="col-sm-2 col-form-label"> Website</label>
            <div class="col-sm-10">
            <input name="web" type="text" id="web" class="form-control optional defaultInvalid url" value="<?php echo $row_settings['website']; ?>"> 
              <span class="example">Example: http://www.domain.com</span>
              </div>
            </div>
      
              <div class="form-group row">
              <label for="user_name" class="col-sm-2 col-form-label">User Name</label>
            <div class="col-sm-10">
            <input class="form-control" name="user_name" type="text" id="user_name" value="<?php echo $row_settings['user_name']; ?>" disabled>
            </div>
            </div>

            <div class="form-group row">
            <label for="user_email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
            <input class="form-control" name="user_email" type="text" id="user_email"  value="<?php echo $row_settings['user_email']; ?>" disabled>
            </div>
            </div>

            <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
          <input name="doSave" type="submit" id="doSave" value="Save"  class="btn btn-primary">
          </div>
            </div>
    
      </form>
	  <?php } ?>

      <h3 class="titlehdr">Change Password</h3>
      <p>If you want to change your password, please input your old and new password 
        to make changes.</p>
      <form name="pform" id="pform" method="post" action="">

      <div class="form-group row">
      <label for="pwd" class="col-sm-2 col-form-label">Old Password</label>
            <div class="col-sm-10">
            <input name="pwd_old" type="password" class="form-control password"  id="pwd_old" required>
            </div>
            </div>

            <div class="form-group row">   
            <label for="pwd" class="col-sm-2 col-form-label">New Password</label>
            <div class="col-sm-10">
            <input name="pwd_new" type="password" id="pwd_new" class="form-control password"  required>
            </div>
            </div>

            <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
          <input name="doUpdate" type="submit" id="doUpdate" value="Update" class="btn btn-primary">
          </div>
            </div>
      </form>
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
