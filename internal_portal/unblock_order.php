<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
$id=$_GET['id'];
mysql_query("UPDATE Orders.Orders SET state='' WHERE id='$id'");
//echo "javascript:window.close();";
require_once ('../conf/db_disconnect.php');
?>
