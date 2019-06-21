<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
header("Location: login.php");
exit();
}

$page_limit = 3; 


$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$login_path = @preg_replace('admin','',dirname($_SERVER['PHP_SELF']));
$path   = rtrim($login_path, '/\\');

// filter GET values
foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

foreach($_POST as $key => $value) {
	$post[$key] = filter($value);
}

if(@$post['doBan'] == 'Ban') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysqli_query($link,"update users set banned='1' where id='$id' and `user_name` <> 'admin'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
 header("Location: $ret");
 exit();
}

if(@$_POST['doUnban'] == 'Unban') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysqli_query($link,"update users set banned='0' where id='$id'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
 header("Location: $ret");
 exit();
}

if(@$_POST['doDelete'] == 'Delete') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysqli_query($link,"delete from users where id='$id' and `user_name` <> 'admin'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
 header("Location: $ret");
 exit();
}

if(@$_POST['doApprove'] == 'Approve') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysqli_query($link,"update users set approved='1' where id='$id'");
		
	list($to_email) = mysqli_fetch_row(mysqli_query($link,"select user_email from users where id='$uid'"));	
 
$message = 
"Hello,\n
Thank you for registering with us. Your account has been activated...\n

*****LOGIN LINK*****\n
http://$host$path/login.php

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

@mail($to_email, "User Activation", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion()); 
	 
	}
 }
 
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];	 
 header("Location: $ret");
 exit();
}

$rs_all = mysqli_query($link,"select count(*) as total_all from users") or die(mysqli_error($link));
$rs_active = mysqli_query($link,"select count(*) as total_active from users where approved='1'") or die(mysqli_error($link));
$rs_total_pending = mysqli_query($link,"select count(*) as tot from users where approved='0'");						   

list($total_pending) = mysqli_fetch_row($rs_total_pending);
list($all) = mysqli_fetch_row($rs_all);
list($active) = mysqli_fetch_row($rs_active);


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

<title>Administration Main Page</title>

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
    <a href="mysettings.php" class="list-group-item list-group-item-action "><i class="fas fa-cog"></i> Settings</a><br>
  <?php 
  if (checkAdmin()) {
    echo '<a href="admin.php"  class="list-group-item list-group-item-action active"><i class="fas fa-users"></i> Admin CP </a>';   
      } 
    ?>
      <a href="logout.php" class="list-group-item list-group-item-action"><i class="fas fa-sign-out-alt text-danger"></i> Logout </a>
     </div>
    <?php } ?> 
    
    </div>

  <div class="col-md-10">
    <h3 class="titlehdr">Administration Page</h3>
      <table  class="table">
        <tr>
          <td>Total users: <?php echo $all;?></td>
          <td>Active users: <?php echo $active; ?></td>
          <td>Pending users: <?php echo $total_pending; ?></td>
        </tr>
      </table>
      <p><?php 
	  if(!empty($msg)) {
	  echo $msg[0];
	  }
	  ?></p>


          
          <form name="form1" method="get" action="admin.php" class="form-inline">
             
                <input name="q" type="text" id="q" class="form-control mb-2 mr-sm-2" placeholder="Type email or user name" value="<?php echo @$_GET['q']; ?>">            
              <select name="qoption" class="form-control mb-2 mr-sm-2">
              <option value="">Select status</option>
                <option value="pending"  <?php if (@$get['qoption'] == 'pending') echo 'selected="selected"'?>>Pending users </option>
                <option value="recent"  <?php if (@$get['qoption'] == 'recent') echo 'selected="selected"'?>>Recently registered  </option>
                <option value="banned"  <?php if (@$get['qoption'] == 'banned') echo 'selected="selected"'?>>Banned users  </option>
                </select>           
              
                <input name="doSearch" type="submit" id="doSearch2" value="Search"  class="btn btn-primary mb-2">
             <a href="admin.php" class="btn btn-secondary ml-2 mb-2">Reset</a>
              </form>
               <span class="example">[You can leave search blank to if you use above options]</span>
 
    
        <?php if (@$get['doSearch'] == 'Search') {
	  $cond = '';
	  if(@$get['qoption'] == 'pending') {
	  $cond = "where `approved`='0' order by date desc";
	  }
	  if(@$get['qoption'] == 'recent') {
	  $cond = "order by date desc";
	  }
	  if(@$get['qoption'] == 'banned') {
	  $cond = "where `banned`='1' order by date desc";
	  }
	  
	  if($get['q'] == '') { 
	  $sql = "select * from users $cond"; 
	  } 
	  else { 
	  $sql = "select * from users where `user_email` = '$_REQUEST[q]' or `user_name`='$_REQUEST[q]' ";
	  }

	  
	  $rs_total = mysqli_query($link,$sql) or die(mysqli_error($link));
	  $total = mysqli_num_rows($rs_total);
	  
	  if (!isset($_GET['page']) )
		{ $start=0; } else
		{ $start = ($_GET['page'] - 1) * $page_limit; }
	  
	  $rs_results = mysqli_query($link,$sql . " limit $start,$page_limit") or die(mysqli_error($link));
	  $total_pages = ceil($total/$page_limit);
	  
	  ?>
    <small>
      <p>Approve -&gt; A notification email will be sent to user notifying activation.<br>
        Ban -&gt; No notification email will be sent to the user. 
      <p><strong>*Note: </strong>Once the user is banned, he/she will never be 
        able to register new account with same email address. 
        </p>
        </small>
      <p align="right"> 
        <?php 
	  
	  // outputting the pages
		if ($total > $page_limit)
		{
		echo '<nav><ul class="pagination">';
    $i = 0;
   
		while ($i < ceil($total/$page_limit))
		{	
    $page_no = $i+1;
		$qstr = preg_replace("&page=[0-9]+&","",$_SERVER['QUERY_STRING']);
    echo "<li class='page-item'><a class='page-link' href=\"admin.php?$qstr&page=$page_no\">$page_no</a> </li>";
		$i++;
    }
		echo '</ul></nav>';
		}  ?>
		</p>
		<form name "searchform" action="admin.php" method="post">
    <div class="table-responsive" id="no-more-table">
        <table class="table table-striped table-light">
        <thead class="thead-dark">
          <tr>  
            <th scope="col" width="4%"><strong>ID</strong></th>
            <th scope="col"> <strong>Date</strong></th>
            <th scope="col"><strong>User Name</strong></th>
            <th scope="col" width="24%"><strong>Email</strong></th>
            <th scope="col" width="10%"><strong>Approval</strong></th>
            <th scope="col" width="10%"> <strong>Banned</strong></th>
            <th scope="col" width="25%">&nbsp;</th>
          </tr>
          </thead>
          <tbody>
          <?php while ($rrows = mysqli_fetch_array($rs_results)) {?>
          <tr> 
            <td data-label="Select"  scope="row">&nbsp;<input name="u[]" type="checkbox" value="<?php echo $rrows['id']; ?>" id="u[]"></td>
            <td data-label="Date">&nbsp;<?php echo $rrows['date']; ?></td>
            <td data-label="Username">&nbsp; <?php echo $rrows['user_name'];?></td>
            <td data-label="Email">&nbsp; <?php echo $rrows['user_email']; ?></td>
            <td data-label="Approval">&nbsp; <span id="approve<?php echo $rrows['id']; ?>"> 
              <?php if(!$rrows['approved']) { echo "Pending"; } else {echo "Active"; }?>
              </span> </td>
            <td data-label="Banned">&nbsp;<span id="ban<?php echo $rrows['id']; ?>"> 
              <?php if(!$rrows['banned']) { echo "no"; } else {echo "yes"; }?>
              </span> </td>
            <td data-label="Action"> <font size="2"><a href="javascript:void(0);" onclick='$.get("do.php",{ cmd: "approve", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#approve<?php echo $rrows['id']; ?>").html(data); });'>Approve</a> 
              <a href="javascript:void(0);" onclick='$.get("do.php",{ cmd: "ban", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#ban<?php echo $rrows['id']; ?>").html(data); });'>Ban</a> 
              <a href="javascript:void(0);" onclick='$.get("do.php",{ cmd: "unban", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#ban<?php echo $rrows['id']; ?>").html(data); });'>Unban</a>                  
              <!-- Button trigger modal -->
              <a href="javascript:void(0);"  data-toggle="modal" data-target="#tedit<?php echo $rrows['id']; ?>"> Edit</a>
              </font> 
        
<!-- Modal -->
<div class="modal fade" id="tedit<?php echo $rrows['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit user record</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <input type="hidden" name="id<?php echo $rrows['id']; ?>" id="id<?php echo $rrows['id']; ?>" value="<?php echo $rrows['id']; ?>">
			<div class="form-group">
      User Name: 
      <input class="form-control" name="user_name<?php echo $rrows['id']; ?>" id="user_name<?php echo $rrows['id']; ?>" type="text" size="10" value="<?php echo $rrows['user_name']; ?>" >
			</div>

      <div class="form-group">
      User Email:
      <input class="form-control" id="user_email<?php echo $rrows['id']; ?>" name="user_email<?php echo $rrows['id']; ?>" type="text" size="20" value="<?php echo $rrows['user_email']; ?>" >
			</div>

      <div class="form-group">
      Level: 
      <input class="form-control" id="user_level<?php echo $rrows['id']; ?>" name="user_level<?php echo $rrows['id']; ?>" type="text" size="5" value="<?php echo $rrows['user_level']; ?>" > 1->user,5->admin
			</div>

      <div class="form-group">
      New Password: 
      <input class="form-control" id="pass<?php echo $rrows['id']; ?>" name="pass<?php echo $rrows['id']; ?>" type="text" size="20" value="" > (leave blank)
			</div>

      </div>
      <div class="modal-footer">
      
		  <div style="color:red" id="msg<?php echo $rrows['id']; ?>" name="msg<?php echo $rrows['id']; ?>"></div>
      <input name="doSave" type="button" id="doSave" value="Save" class="btn btn-primary"  
			onclick='$.get("do.php",{ cmd: "edit", pass:$("input#pass<?php echo $rrows['id']; ?>").val(),user_level:$("input#user_level<?php echo $rrows['id']; ?>").val(),user_email:$("input#user_email<?php echo $rrows['id']; ?>").val(),user_name: $("input#user_name<?php echo $rrows['id']; ?>").val(),id: $("input#id<?php echo $rrows['id']; ?>").val() } ,function(data){ $("#msg<?php echo $rrows['id']; ?>").html(data); });'> 
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		 
      </div>
    </div>
  </div>
</div>
      </td>
          </tr>
          
          <?php } ?>
        </tbody>  
        </table>
        </div>
          <input name="doApprove" type="submit" id="doApprove" value="Approve" class="btn btn-success">
          <input name="doBan" type="submit" id="doBan" value="Ban" class="btn btn-warning">
          <input name="doUnban" type="submit" id="doUnban" value="Unban" class="btn btn-info">
          <input name="doDelete" type="submit" id="doDelete" value="Delete" class="btn btn-danger">
          <input name="query_str" type="hidden" id="query_str" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
         <small>
          <p>
          <strong>Note:</strong> If you delete the user can register again, instead 
          ban the user. </p>
        <p><strong>Edit Users:</strong> To change email, user name or password, 
          you have to delete user first and create new one with same email and 
          user name.</p>
          </small>
      </form>
	  
	  <?php } ?>
 
 
	  <?php
    $error=array();
	  if(@$_POST['doSubmit'] == 'Create')
{
$rs_dup = mysqli_query($link,"select count(*) as total from users where user_name='$post[user_name]' OR user_email='$post[user_email]'") or die(mysqli_error($link));
list($dups) = mysqli_fetch_row($rs_dup);

if($dups > 0) {
	$error[]="The user name or email already exists in the system";
	}

if(!empty($_POST['pwd'])) {
  $pwd = $post['pwd'];	
  $hash = PwdHash($post['pwd']);
 }  
 else
 {
  $pwd = GenPwd();
  $hash = PwdHash($pwd);
  
 }
 
mysqli_query($link,"INSERT INTO users (`user_name`,`user_email`,`pwd`,`approved`,`date`,`user_level`)
			 VALUES ('$post[user_name]','$post[user_email]','$hash','1',now(),'$post[user_level]')
			 ") or $error[]=mysqli_error($link); 



$message = 
"Thank you for registering with us. Here are your login details...\n
User Email: $post[user_email] \n
Passwd: $pwd \n

*****LOGIN LINK*****\n
http://$host$path/login.php

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL****
";

if($_POST['send'] == '1' && count($error)==0) {

	mail($post['user_email'], "Login Details", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion()); 
     $msg="User created with password $pwd....done.";
 }

 if(!empty($error))  {
  echo "<div class=\"alert alert-danger\">";
 foreach ($error as $e) {
   echo "* Error - $e <br>";
   }
 echo "</div>";	
  }

 if(!empty($msg))  {  
echo "<div class=\"alert alert-success\">$msg</div>"; 
}

}

	  ?>
	  
      <h3 class="titlehdr">Create New User</h3>
 
 <form name="form1" method="post" action="admin.php">
            <div class="form-group row"> 
            <label for="pwd" class="col-sm-2 col-form-label">User ID </label>
              <div class="col-sm-10">
                <input name="user_name" type="text" id="user_name"  class="form-control">
                <span class="example">(Type the username)</span>
                </div>
            </div>

                <div class="form-group row">           
                <label for="pwd" class="col-sm-2 col-form-label">Email </label>
              <div class="col-sm-10">
                <input name="user_email" type="email" id="user_email"  class="form-control">
                </div>
            </div>
             
                <div class="form-group row">
                <label for="pwd" class="col-sm-2 col-form-label"> User Level </label> 
              <div class="col-sm-10">
                <select name="user_level" id="user_level"  class="form-control">
                  <option value="1">User</option>
                  <option value="5">Admin</option>
                </select>
                </div>
            </div>
              
                <div class="form-group row">
                <label for="pwd" class="col-sm-2 col-form-label">Password </label>
            <div class="col-sm-10">
                <input name="pwd" type="password" id="pwd" class="form-control">
                <span class="example">(if empty a password will be auto generated)</span>
                </div>
            </div>
              
                <div class="form-group row">
                <div class="col-sm-10 offset-sm-2">
                <input name="send" type="checkbox" id="send" value="1" checked>
                 Send Email
                </div>
            </div>

            <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
                <input name="doSubmit" type="submit" id="doSubmit" value="Create" class="btn btn-primary">
                </div>
            </div>
              </p>
            </form>
            <p>**All created users will be approved by default.</p>


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
