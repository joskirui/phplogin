<?php 
include 'dbc.php';
session_start();
if(!checkAdmin()) {
header("Location: login.php");
exit();
}

$ret = $_SERVER['HTTP_REFERER'];

foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

if($get['cmd'] == 'approve')
{
mysqli_query($link,"update users set approved='1' where id='$get[id]'") or die(mysqli_error($link));
$rs_email = mysqli_query($link,"select user_email from users where id='$get[id]'") or die(mysqli_error($link));
list($to_email) = mysqli_fetch_row($rs_email);

$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$login_path = @preg_replace('admin','',dirname($_SERVER['PHP_SELF']));
$path   = rtrim($login_path, '/\\');

$message = 
"Thank you for registering with us. Your account has been activated...

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


 echo "Active";


}

if($get['cmd'] == 'ban')
{
mysqli_query($link,"update users set banned='1' where id='$get[id]' and `user_name` <> 'admin'");

//header("Location: $ret");  
echo "yes";
exit();

}
/* Editing users*/

if($get['cmd'] == 'edit')
{
/* Duplicate user name check */
$rs_usr_duplicate = mysqli_query($link,"select count(*) as total from `users` where `user_name`='$get[user_name]' and `id` != '$get[id]'") or die(mysqli_error($link));
list($usr_total) = mysqli_fetch_row($rs_usr_duplicate);
	if ($usr_total > 0)
	{
	echo "Sorry! user name already registered.";
	exit;
	} 
/* Duplicate email check */	
$rs_eml_duplicate = mysqli_query($link,"select count(*) as total from `users` where `user_email`='$get[user_email]' and `id` != '$get[id]'") or die(mysqli_error($link));
list($eml_total) = mysqli_fetch_row($rs_eml_duplicate);
	if ($eml_total > 0)
	{
	echo "Sorry! user email already registered.";
	exit;
	}
/* Now update user data*/	
mysqli_query($link,"
update users set  
`user_name`='$get[user_name]', 
`user_email`='$get[user_email]',
`user_level`='$get[user_level]'
where `id`='$get[id]'") or die(mysqli_error($link));
//header("Location: $ret"); 

if(!empty($get['pass'])) {
$hash = PwdHash($get['pass']);
mysqli_query($link,"update users set `pwd` = '$hash' where `id`='$get[id]'") or die(mysqli_error($link));
}

echo "changes done";
exit();
}

if($get['cmd'] == 'unban')
{
mysqli_query($link,"update users set banned='0' where id='$get[id]'");
echo "no";

//header("Location: $ret");  
// exit();

}


?>