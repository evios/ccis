<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<title>OSIS Call Center Internal Portal</title>
		<link rel="stylesheet" href="../css/style.css" type="text/css" media="screen" charset="utf-8">
		<?
		require_once('../general_functions/ajaxGetPage.php');
		?>

<script type="text/javascript">
function addToCart(distributor, code, manufacturer, description, priceUAH, priceUAHCashless, priceUSD, availability, callID, operator, store, source)
{
	ajaxpage("add_to_cart.php?distributor="+distributor+"&code="+code+"&manufacturer="+manufacturer+"&description="+description+"&priceUAH="+priceUAH+"&priceUAHCashless="+priceUAHCashless+"&priceUSD="+priceUSD+"&availability="+availability+"&callID="+callID+"&operator="+operator+"&store="+store+"&source="+source+"", 'cart_info');
}
</script>


		<script type="text/javascript">
		<!--
		function NewWindowOrder(callID,id,operator)
		{
			location.reload(true);
			OrderWindow=window.open("order.php?callID="+callID+"&id="+id+"&operator="+operator+"","Place Order","width=900,height=700,resizable=yes,scrollbars=yes");
		}

		function NewWindowEdit(id, operator)
		{
			OrderWindow=window.open("edit.php?operator="+operator+"&id="+id+"","Edit Order","width=900,height=700,resizable=yes,scrollbars=yes");
		}
		// -->
		</script>

		<script>
		function timedRefresh(timeoutPeriod)
		{
			setTimeout("timedRefresh(5000);",timeoutPeriod);
			ajaxpage('site_orders_info.php', 'cc_info');
		}
		</script>

		<script>

		function welcome()
		{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
		    document.getElementById("welcome_text").innerHTML=xmlhttp.responseText;
		    }
		  }
		xmlhttp.open("GET","store_fullname.php?operator="+frmLogout.operator.value,true);
		xmlhttp.send();
		}
		</script>
	</head>
<body onMouseMove="welcome()" bgcolor="f9f9f9" onload="JavaScript:timedRefresh(0);">


<?
require_once('../general_functions/queue_operations.php');
require_once 'class.login.php';

$log = new logmein();
if($_REQUEST['action'] == "login"){
	if($log->login("auth", $_REQUEST['username'], $_REQUEST['password']) == true)
	{
	        //do something on successful login
		//$operator= $_SESSION['login'];
	}
	else
	{
	        //do something on FAILED login 
		echo "Login Failed";
	}
}
if($_REQUEST['action'] == "logout")
{
	require_once('../conf/db_vars.php');
	require_once('../conf/db_connect.php');
	$operator=$_POST['operator'];
	queue_remove($operator);
	$log->logout();
	header("Refresh: 0; url=index.php ");
	break;
	require_once ('../conf/db_disconnect.php');
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
	$operator= $_SESSION['login'];
	$phone_status=phone_status($operator);
//	if ($phone_status!=-1 && $phone_status!=4)
//	{
		require_once('../conf/db_vars.php');
		require_once('../conf/db_connect.php');
		echo'<form name="frmLogout" method="post" id="frmLogout" action="">
		<input name="action" id="action" value="logout" type="hidden">
		</form>';
		echo '<div><div class="divToolbar"><a class="hrefLogOut" href="javascript:document.frmLogout.submit()">Log&nbsp;Out</a></div></div>';
		echo "<div align=\"center\">";
		echo "<big id=\"welcome_text\"></big>";
		echo "</div>";
		echo "<br>";
		queue_add($operator);
		require_once('main.php');
		require_once ('../conf/db_disconnect.php');
/*	}
	else
	{
		echo "Please Turn Your Phone ON!";
		echo "<form name=\"frmLogout\" method=\"post\" id=\"frmLogout\" action=\"\">
		<input name=\"action\" id=\"action\" value=\"logout\" type=\"hidden\">
		<input name=\"operator\" id=\"operator\" value=\"$operator\" type=\"hidden\">
		<div><input name=\"submit\" id=\"submit\" value=\"Logout\" type=\"submit\"></div>
		</form>";

	}
*/
}


?>


</body>
</html>
