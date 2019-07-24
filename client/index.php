<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		<title>OSIS Call Center Statisctics</title>
		<script language='javascript' src='charts/FusionCharts.js'></script>
		<script>
			function getcsv(flag)
			{
				document.getElementById("csv").value=flag;
				frm_csv.submit();
			}
		</script>
		<link rel="stylesheet" href="../css/style.css" type="text/css" media="screen" charset="utf-8">
	</head>
<?$bgcolor="f9f9f9";?>
<body bgcolor="<?=$bgcolor?>">
<?
require_once('class.login.php');
//require_once('../general_functions/ajaxGetPage.php');
//require_once('../language/en.php');
require_once('../language/ru.php');


$log = new logmein();
if($_REQUEST['action'] == "login"){
    if($log->login("auth", $_REQUEST['username'], $_REQUEST['password']) == true){
        //do something on successful login 
    }else{
        //do something on FAILED login 
	echo "$text_login_failed";
    }
}
if($_REQUEST['action'] == "logout"){
	$log->logout();
	header("Refresh: 0; url=index.php ");
}

$log->encrypt = true; //set encryption

//parameters are(SESSION, name of the table, name of the password field, name of the username field)
if($log->logincheck($_SESSION['loggedin'], "List", "password", "name") == false)
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
	echo "<div><div class='divToolbar'><a class='hrefLogOut' href='javascript:document.frmLogout.submit()'>$text_logout</a></div></div>";
	$store= $_SESSION['login'];
	require_once('main.php');
}

?>
</body>
</html>
