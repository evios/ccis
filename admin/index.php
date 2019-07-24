<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>OSIS Call Center Administration</title>
	<link rel="stylesheet" href="../css/style.css" type="text/css" media="screen" charset="utf-8">
</head>
<body bgcolor="f9f9f9">
<?
require_once 'class.login.php';

$log = new logmein();
if($_REQUEST['action'] == "login"){
    if($log->login("auth", $_REQUEST['username'], $_REQUEST['password']) == true){
        //do something on successful login 
    }else{
        //do something on FAILED login 
	echo "Login Failed";
    }
}
if($_REQUEST['action'] == "logout"){
	$log->logout();
	header("Refresh: 0; url=index.php ");
}

$log->encrypt = true; //set encryption

//parameters are(SESSION, name of the table, name of the password field, name of the username field)
if($log->logincheck($_SESSION['loggedin'], "auth", "password", "login") == false)
{
	//do something if NOT logged in. For example, redirect to login page or display message.
	//parameters here are (form name, form id and form action)
	echo "<div class='loginDiv'>";
	$log->loginform("loginformname", "loginformid", "");
	echo "</div>";
}
else
{
	//do something else if logged in.
	//Log out
	echo'<form name="frmLogout" method="post" id="frmLogout" action="">
	<input name="action" id="action" value="logout" type="hidden">
	</form>';
	echo '<div><div class="divToolbar"><a class="hrefLogOut" href="javascript:document.frmLogout.submit()">Log&nbsp;Out</a></div></div>';
	//$store=$_SESSION['login'];
	$userlevel=$_SESSION['userlevel'];
	if($userlevel==2)
		require_once('main.php');
}

?>


</body>
</html>
