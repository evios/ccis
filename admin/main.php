<?
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		<title>OSIS Call Center Administration</title>
	</head>
<body>
*/
?>
<center>

<div id="tabclient" class="css-tabs">
	<ul class="menu"> 
		<li><a href="index.php?view=operators" <?if(strstr($_GET['view'],"operator")) echo "class='activeLink'";?>> Operators </a></li>
		<li><a href="index.php?view=distributors" <?if(strstr($_GET['view'],"distributor")) echo "class='activeLink'";?>> Distributors </a></li>
		<li><a href="index.php?view=stores" <?if(strstr($_GET['view'],"store")) echo "class='activeLink'";?>> Stores </a></li>
		<li><a href="index.php?view=pstns" <?if(strstr($_GET['view'],"pstn")) echo "class='activeLink'";?>> PSTNs </a></li>
	</ul> 
</div>

<?
/// Set timezone
date_default_timezone_set('Europe/Kiev');
//echo $_GET['view'];
if ($view=$_GET['view'])
{
	require_once('../conf/db_vars.php');
	require_once('../conf/db_connect.php');
	switch ($view)
	{
		case 'operators':
			echo "<p align='center'>";
			include ('operators/operators.php');
			echo "<p>";
			break;
		case 'operator':
			echo "<p align='center'>";
			include ('operators/operator.php');
			echo "<p>";
			break;
		case 'distributors':
			echo "<p align='center'>";
			include ('distributors/distributors.php');
			echo "<p>";
			break;
		case 'distributor':
			echo "<p align='center'>";
			include ('distributors/distributor.php');
			echo "<p>";
			break;
		case 'stores':
			echo "<p align='center'>";
			include ('stores/stores.php');
			echo "<p>";
			break;
		case 'store':
			echo "<p align='center'>";
			include ('stores/store.php');
			echo "<p>";
			break;
		case 'pstns':
			echo "<p align='center'>";
			include ('pstn/pstns.php');
			echo "<p>";
			break;
		case 'pstn':
			echo "<p align='center'>";
			include ('pstn/pstn.php');
			echo "<p>";
			break;
	}
	require_once('../conf/db_disconnect.php');
}

//</body>
//</html>
?>
