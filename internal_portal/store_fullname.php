<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
require_once('lid.php');

$query_store_fullname = "SELECT fullname,name FROM Stores.List WHERE name LIKE '$storename'";
$result_store_fullname = mysql_query($query_store_fullname);
//$store_fullname=mysql_result($result_store_fullname,0,'fullName');
if ($store_fullname=mysql_result($result_store_fullname,0,'fullName'))
//{
//	echo "[$store_fullname]";
//	if ($store_fullname=="")
//		echo "Добрый день! Вы позвонили в интернет-магазин <b>$storename</b>";
//	else
		echo "Добрый день! Вы позвонили в интернет-магазин <b>$store_fullname</b>";
//}
else
//{
	if ($store_fullname=="" && $storename!="")
		echo "Добрый день! Вы позвонили в интернет-магазин <b>$storename</b>";
	else
		echo "У Вас нету активных вызовов";
//}
			
require_once ('../conf/db_disconnect.php');
?>
