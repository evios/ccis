<?
///
/// PHP FILE FOR SYNC DB (CRON)
///
date_default_timezone_set('Europe/Kiev');
$date=date("dmy");
$path="/home/jenia/www/ccis";
require_once($path.'/general_functions/get_string_between.php');


require ($path.'/conf/db_vars.php');
require ($path.'/conf/db_connect.php');
require_once($path.'/general_functions/multiexplode.php');
$query_stores="SELECT `name` FROM `Stores`.`List` WHERE  1";
$result_stores = mysql_query($query_stores);
require ($path.'/conf/db_disconnect.php');

while($row_stores = mysql_fetch_array( $result_stores ))
{
	$store_name=$row_stores[0];
	require ($path.'/conf/db_vars.php');
	require ($path.'/conf/db_connect.php');
	/// Get latest Store Order ID
	$query_store_order_id="SELECT storeOrderId FROM Orders.Orders WHERE store LIKE '$store_name' group by storeOrderId desc LIMIT 1";
	$result_store_order_id = mysql_query($query_store_order_id);
	if(mysql_num_rows($result_store_order_id)==0)
		$store_order_id=0;
	else
		$store_order_id=mysql_result($result_store_order_id,0,'storeOrderId');

	/// Get Store Attributes
	$query_db_path="SELECT * FROM `Stores`.`List` WHERE  `List`.`name` =  '$store_name' LIMIT 1";
	$result_db_path = mysql_query($query_db_path);
	$host_IS=mysql_result($result_db_path,0,'db_host');
	$user_IS=mysql_result($result_db_path,0,'db_user');
	$pass_IS=mysql_result($result_db_path,0,'db_pass');
	$db_IS=mysql_result($result_db_path,0,'db_store');

	/// Code's and Price's Variables
	$code_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_code_path'));
	$price_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_price_path'));
	$code_table=$code_path_IS[0];
	$price_table="";
	if($price_path_IS[0]!=$code_path_IS[0])
		$price_table=",{$price_path_IS[0]} ";

	/// Currency Rate's Variables
	$rate_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_rate_path'));
	$cash_rate_id_field=mysql_result($result_db_path,0,'cash_rate_id_field');
	$cash_rate_id_IS=mysql_result($result_db_path,0,'cash_rate_id');
	$cashless_rate_id_IS=mysql_result($result_db_path,0,'cashless_rate_id');

	/// Order's Variables
	$order_id_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_id_path'));
	$order_time_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_time_path'));
	$order_code_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_code_path'));
	$order_cost_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_cost_path'));
	$order_quantity_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_quantity_path'));
	$order_first_name_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_first_name_path'));
	$order_last_name_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_last_name_path'));
	$order_phone_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_phone_path'));
	$order_email_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_email_path'));
	$order_town_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_town_path'));
	$order_address_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_address_path'));
	$order_shipping_type_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_shipping_type_path'));
	$order_note_path_IS=str_getcsv(mysql_result($result_db_path,0,'db_order_note_path'));
	$order_reference_IS=mysql_result($result_db_path,0,'db_order_reference');
	$code_delimeter=mysql_result($result_db_path,0,'code_delimeter');

	/// Tables for Order's
	$i=0;
	$from_path_order="";
	$tmp_arr=array();
	$order_table_name[$i++]=$order_id_path_IS[0];
	$order_table_name[$i++]=$order_time_path_IS[0];
	$order_table_name[$i++]=$order_code_path_IS[0];
	$order_table_name[$i++]=$order_cost_path_IS[0];
	$order_table_name[$i++]=$order_quantity_path_IS[0];
	$order_table_name[$i++]=$order_first_name_path_IS[0];
	$order_table_name[$i++]=$order_last_name_path_IS[0];
	$order_table_name[$i++]=$order_phone_path_IS[0];
	$order_table_name[$i++]=$order_email_path_IS[0];
	$order_table_name[$i++]=$order_town_path_IS[0];
	$order_table_name[$i++]=$order_address_path_IS[0];
	$order_table_name[$i++]=$order_shipping_type_path_IS[0];
	$order_table_name[$i++]=$order_note_path_IS[0];
//	$order_table_name[$i++]=$order_id_path_IS[0];
	$order_table_name[$i++]=$rate_path_IS[0];
	for($i=0;$i<sizeof($order_table_name);$i++)
		if (in_array($order_table_name[$i],$tmp_arr)!=true)
		{
			array_push($tmp_arr,$order_table_name[$i]);
			if ($from_path_order != "")
				$from_path_order.=", ";
			$from_path_order.="{$order_table_name[$i]}";
		}

	require ($path.'/conf/db_disconnect.php');

	if ($db_IS=='')
		continue;
	$con_IS = mysql_connect($host_IS, $user_IS, $pass_IS);
	if (!$con_IS)
	{
		continue;
		die('Could not connect: ' . mysql_error());
	}
	mysql_query ('SET NAMES UTF8');
	mysql_select_db($db_IS, $con_IS);

	///
	/// Read Code's and Price's
	///
	if ($code_path_IS[0]=='' OR $price_path_IS[0]=='' OR $code_path_IS[1]=='' OR $price_path_IS[1]=='')
		continue;
 	$query_get_price = "SELECT {$code_path_IS[1]},{$price_path_IS[1]} FROM $code_table$price_table";
	$result_get_price = mysql_query($query_get_price);

	/// Read Currency Rate's
	if ($rate_path_IS[0]=='' OR $cash_rate_id_IS=='' OR $cashless_rate_id_IS=='' OR $rate_path_IS[1]=='')
		continue;
 	$query_get_rate_cash = "SELECT * FROM {$rate_path_IS[0]} WHERE $cash_rate_id_field='$cash_rate_id_IS'";
	$result_get_rate_cash = mysql_query($query_get_rate_cash);
 	$query_get_rate_cashless = "SELECT * FROM {$rate_path_IS[0]} WHERE $cash_rate_id_field='$cashless_rate_id_IS'";
	$result_get_rate_cashless = mysql_query($query_get_rate_cashless);
 	$cash_rate=mysql_result($result_get_rate_cash,0,$rate_path_IS[1]);
	$cashless_rate=mysql_result($result_get_rate_cashless,0,$rate_path_IS[1]);

	///
	/// Read Order's
	///
	if ($order_id_path_IS[0]=='' OR $order_time_path_IS[0]=='' OR $order_code_path_IS[0]=='' OR $order_cost_path_IS[0]=='' OR $order_quantity_path_IS[0]=='' OR $order_first_name_path_IS[0]=='' OR $order_last_name_path_IS[0]=='' OR $order_phone_path_IS[0]=='' OR $order_email_path_IS[0]=='' OR $order_town_path_IS[0]=='' OR $order_address_path_IS[0]=='' OR $order_shipping_type_path_IS[0]=='' OR $order_note_path_IS[0]=='' OR $order_id_path_IS[1]=='' OR $order_time_path_IS[1]=='' OR $order_code_path_IS[1]=='' OR $order_cost_path_IS[1]=='' OR $order_quantity_path_IS[1]=='' OR $order_first_name_path_IS[1]=='' OR $order_last_name_path_IS[1]=='' OR $order_phone_path_IS[1]=='' OR $order_email_path_IS[1]=='' OR $order_town_path_IS[1]=='' OR $order_address_path_IS[1]=='' OR $order_shipping_type_path_IS[1]=='' OR $order_note_path_IS[1]=='')
		continue;
	if ($store_order_id==0)
	{
		$result_read_order_id = mysql_query("SELECT {$order_id_path_IS[1]} FROM {$order_id_path_IS[0]} group by {$order_id_path_IS[1]} desc LIMIT 1");
		$store_order_id = mysql_result($result_read_order_id,0,$order_id_path_IS[1])-1;
	}
 	$query_read_orders = "SELECT 
{$order_id_path_IS[0]}.{$order_id_path_IS[1]},
{$order_time_path_IS[0]}.{$order_time_path_IS[1]},
{$order_code_path_IS[0]}.{$order_code_path_IS[1]},
{$order_cost_path_IS[0]}.{$order_cost_path_IS[1]},
{$order_quantity_path_IS[0]}.{$order_quantity_path_IS[1]},
{$order_first_name_path_IS[0]}.{$order_first_name_path_IS[1]},
{$order_last_name_path_IS[0]}.{$order_last_name_path_IS[1]},
{$order_phone_path_IS[0]}.{$order_phone_path_IS[1]},
{$order_email_path_IS[0]}.{$order_email_path_IS[1]},
{$order_town_path_IS[0]}.{$order_town_path_IS[1]},
{$order_address_path_IS[0]}.{$order_address_path_IS[1]},
{$order_shipping_type_path_IS[0]}.{$order_shipping_type_path_IS[1]},
{$order_note_path_IS[0]}.{$order_note_path_IS[1]} 
FROM $from_path_order WHERE $order_reference_IS AND {$order_id_path_IS[0]}.{$order_id_path_IS[1]}>'$store_order_id'";
	$result_read_orders = mysql_query($query_read_orders);
 
	mysql_close($con_IS);

	$date_full=date("Y-m-d H:i:s");
	///
	/// Upload Code's and Price's to OSIS
	///
	/// Clear Table Before Upload New Data
	require ($path.'/conf/db_vars.php');
	require ($path.'/conf/db_connect.php');
	$create_table = mysql_query("CREATE TABLE IF NOT EXISTS Stores.S_$store_name (`code` varchar(20) COLLATE utf8_unicode_ci NOT NULL, `priceUAH` varchar(20) COLLATE utf8_unicode_ci NOT NULL, `priceUSD` varchar(20) COLLATE utf8_unicode_ci NOT NULL, `update_date` varchar(100) COLLATE utf8_unicode_ci NOT NULL)");
	$clear_table = mysql_query("TRUNCATE TABLE Stores.S_$store_name");

	/// Upload new Code's and Price's
	while($row_add = mysql_fetch_array( $result_get_price ))
	{
		$query_add = "INSERT INTO Stores.S_$store_name (`code`,`priceUAH`,`update_date`) VALUE ('".$row_add[0]."','".$row_add[1]."','$date_full')";
		$result_add = mysql_query($query_add);
	}
	if ($result_add)
		$update_date=mysql_query("UPDATE `Stores`.`List` SET  `price_update_date` =  '$date_full' WHERE `List`.`name` =  '$store_name'");

	/// Upload Rates
		$query_update_rates = "UPDATE  `Stores`.`List` SET  `rate_USD_cash`='$cash_rate', `rate_USD_cashless`='$cashless_rate' WHERE  `List`.`name` =  '$store_name'";
		$result_update_rates = mysql_query($query_update_rates);

	$date_full=date("Y-m-d H:i:s");

	/// Cash/Cashless Rates
	$result_rate_cash=mysql_query("SELECT `rate_USD_cash` FROM Stores.List Where `name` LIKE '$store_name'");
	$result_rate_cashless=mysql_query("SELECT `rate_USD_cashless` FROM Stores.List Where `name` LIKE '$store_name'");
	$rate_cash=mysql_result($result_rate_cash,0,'rate_USD_cash');
	$rate_cashless=mysql_result($result_rate_cashless,0,'rate_USD_cashless');
	/// Upload new Orders
	while($row_add = mysql_fetch_array( $result_read_orders ))
	{
		$code=get_string_between($row_add[$order_code_path_IS[1]],$code_delimeter[0],$code_delimeter[1]); // vibiraem iz stroki tolko code

		$query_order_id="SELECT id FROM Orders.Orders WHERE storeOrderId LIKE '{$row_add[$order_id_path_IS[1]]}'";
		$result_order_id=mysql_query($query_order_id);
		$order_id=mysql_result($result_order_id,0,'id');
		if(mysql_num_rows($result_order_id)==0)
		{
			$query_add = "INSERT INTO Orders.Orders (id, storeOrderId, source, order_time, store, priceUAH, customerFirstName, customerLastName, customerPhone, customerEmail, shippingType, customerAddress, note, update_date) VALUE (NULL, '{$row_add[$order_id_path_IS[1]]}','site', '{$row_add[$order_time_path_IS[1]]}','$store_name', '{$row_add[$order_cost_path_IS[1]]}', '{$row_add[$order_first_name_path_IS[1]]}','{$row_add[$order_last_name_path_IS[1]]}','{$row_add[$order_phone_path_IS[1]]}','{$row_add[$order_email_path_IS[1]]}','{$row_add[$order_shipping_type_path_IS[1]]}','{$row_add[$order_address_path_IS[1]]}','{$row_add[$order_note_path_IS[1]]}','$date_full')";
			$result_add = mysql_query($query_add);
			/// Get OrderID Field
			$query_order_id="SELECT id FROM Orders.Orders WHERE storeOrderId LIKE '{$row_add[$order_id_path_IS[1]]}'";
			$result_order_id=mysql_query($query_order_id);
			$order_id=mysql_result($result_order_id,0,'id');
		}

		/// Find Distributor For This Code
		/// For Orders From Site get Distributors by Priority, and search code
		$delimeters=Array(",","=");
		$query_get_distrs="SELECT `distributors` FROM Stores.List WHERE `name` LIKE '$store_name' LIMIT 1";
		$store_get_distrs=mysql_query($query_get_distrs);
		$distrs=multiexplode($delimeters,mysql_result($store_get_distrs,0,'distributors'));
		for($i=0;$i<sizeof($distrs);$i++)
		{
			$distributors[$i]=$distrs[$i][0];
			$distributors_priority[$i]=$distrs[$i][1];
		}
		asort($distributors_priority);
		reset($distributors_priority);
		for($i=0;$i<sizeof($distributors);$i++)
		{
			$distributor_tmp=$distributors[key($distributors_priority)];
			$query_code_is_present="SELECT * FROM Distributors.D_$distributor_tmp WHERE Distributors.D_$distributor_tmp.code LIKE '%$code%'";
			$code_is_present=mysql_query($query_code_is_present);
			if (mysql_num_rows($code_is_present) > 0)
			{
				$distributor=$distributor_tmp;
				break;
			}
			next($distributors_priority);
		}
			
		$manufacturer=mysql_result($code_is_present,0,'manufacturer');
		$description=mysql_result($code_is_present,0,'description');
		$availability=mysql_result($code_is_present,0,'availability');
		if($availability!=1)
			$availability=0;

		$get_prices=mysql_query("SELECT * FROM Stores.S_$store_name WHERE code LIKE '%$code%'");
		$priceUAH=mysql_result($get_prices,0,'priceUAH');
		$priceUSD=round($priceUAH/$rate_cash,2);
		$priceUAHCashless=round($priceUAH/$rate_cash*$rate_cashless,2);

		$cart_add = "INSERT INTO Orders.Orders_cart (orderID, code, distributor, manufacturer, description, quantity, priceUAH, priceUAHCashless, priceUSD, availability) VALUE ('$order_id', '$code', '$distributor', '$manufacturer', '$description', '{$row_add[$order_quantity_path_IS[1]]}', '$priceUAH', '$priceUAHCashless', '$priceUSD', '$availability')";
		mysql_query($cart_add);
	}
	if ($result_add)
	$update_date=mysql_query("UPDATE `Stores`.`List` SET  `orders_update_date` =  '$date_full' WHERE `List`.`name` =  '$store_name'");

	require ($path.'/conf/db_disconnect.php');
}

?>
