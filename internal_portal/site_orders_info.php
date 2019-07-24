<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
$site_orders = mysql_num_rows(mysql_query("SELECT * FROM Orders.Orders WHERE state='' AND source='site' order by `id` asc"));
if($site_orders>0)
echo $site_orders;
require_once ('../conf/db_disconnect.php');
?>
