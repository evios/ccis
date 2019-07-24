<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
$id=$_GET['id'];
//$orderID=$_GET['orderID'];
$quantity=$_GET['quantity'];
mysql_query("UPDATE Orders.Orders_cart SET quantity='$quantity' WHERE id='$id'");
require_once ('../conf/db_disconnect.php');
?>
