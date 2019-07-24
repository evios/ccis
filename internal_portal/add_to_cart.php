<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
date_default_timezone_set('Europe/Kiev');
$date=date('Y-m-d');
$time=date('H:i:s');
$orders_db="Orders.Orders";
$orders_cart_db="Orders.Orders_cart";
$order_time="$date $time";
$source="phone";
$callID=$_GET['callID'];
//$storename=$_GET['store'];
//$operator=$_GET['operator'];
$storename="elampa";
$operator="503";
$quantity="1";

//$quantity=$_GET['quantity'];
$distributor=$_GET['distributor'];
$code=$_GET['code'];
$manufacturer=$_GET['manufacturer'];
$description=$_GET['description'];
$priceUAH=$_GET['priceUAH'];
$priceUAHCashless=$_GET['priceUAHCashless'];
$priceUSD=$_GET['priceUSD'];
$availability=$_GET['availability'];



$order_id=mysql_result(mysql_query("SELECT id FROM $orders_db WHERE callId LIKE '$callID' LIMIT 1"),0,'id');
if ($order_id==0 OR $order_id=="" OR !isset($order_id)) /// First Product in Cart
{
	/// Create Order Cart
	$query_add_order="INSERT INTO $orders_db (id, callID, order_time, source, store, operator) VALUES (NULL, '$callID', '$order_time', '$source', '$storename', '$operator')";
	$add_order=mysql_query($query_add_order);
	$query_order_id="SELECT `id` FROM $orders_db WHERE `callID` LIKE '$callID'";
	$order_id=mysql_result(mysql_query($query_order_id),0,'id');
	/// Add product to Cart
 	$query_add_product="INSERT INTO $orders_cart_db (orderID, distributor, code, manufacturer, description, quantity, priceUAH, priceUAHCashless, priceUSD,availability) VALUES ('$order_id', '$distributor', '$code', '$manufacturer', '$description', '$quantity', '$priceUAH', '$priceUAHCashless', '$priceUSD','$availability')";
	$add_product=mysql_query($query_add_product);
}
else
{
	/// Add product to Cart
 	$query_add_product="INSERT INTO $orders_cart_db (orderID, distributor, code, manufacturer, description, quantity, priceUAH, priceUAHCashless, priceUSD,availability) VALUES ('$order_id', '$distributor', '$code', '$manufacturer', '$description', '$quantity', '$priceUAH', '$priceUAHCashless', '$priceUSD','$availability')";
	$add_product=mysql_query($query_add_product);
}

/// Print How many products in Cart
$cart_products = mysql_num_rows(mysql_query("SELECT * FROM $orders_cart_db WHERE quantity!='0' AND orderID=(SELECT id FROM Orders.Orders WHERE callID='$callID')"));
if($cart_products>0)
echo $cart_products;
require_once ('../conf/db_disconnect.php');
?>
