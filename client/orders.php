<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
$orders_phone_db="Orders.Orders";

///
/// Define Variables
///
$date=date('Y-m-d');
$time=date('H:i:s');
//javascript:document.form_date.submit();
?>
<form action="" method="get" name="frm_csv" id="frm_csv">
	<input type="hidden" name="view" value="<?=$_GET['view']?>">
	<input type="hidden" name="csv" id="csv" value="0">
	<input class="rounded-textbox" type="text" name="from_date" value="<?if ($from_date=$_GET['from_date']){echo $from_date;}else{echo $date;}?>">
	<input class="rounded-textbox" type="text" name="to_date" value="<?if ($to_date=$_GET['to_date']){echo $to_date;}else{echo $date;}?>">
	<br>
	<input class="rounded-textbox" type="text" name="from_time" value="<?if ($from_time=$_GET['from_time']){echo $from_time;}else{echo '00:00:00';}?>">
	<input class="rounded-textbox" type="text" name="to_time" value="<?if ($to_time=$_GET['to_time']){echo $to_time;}else{echo '23:59:59';}?>">
	<br>
</form>
<input class="button" value="<?=$text_submit?>" type="submit" name="submit" onClick="frm_csv.submit();"/><br><br>
<input class="button" type="button" onclick="getcsv('1')" value="Get a CSV File!"><br>
<?
if ((!$from_date=$_GET['from_date']) && (!$to_date=$_GET['to_date']))
{
$from_date=$date;
$to_date=$date;
}

if ((!$from_time=$_GET['from_time']) && (!$to_time=$_GET['to_time']))
{
$from_time='00:00:00';
$to_time='23:59:59';
}

$orders_cols_num=13;
$query_orders="SELECT * FROM $orders_phone_db WHERE store LIKE '$store' AND order_time >= '$from_date $from_time' AND order_time <= '$to_date $to_time' order by ID DESC";
$result_orders = mysql_query($query_orders);
$csv_data[]="";
$j=0;
echo "<table id='rounded-corner'>";
	echo "<thead>";
		echo "<tr>";
			echo "<th class='rounded-first'>$text_order_source</th>";
				$csv_data[0][$j++].=$text_order_source;
			echo "<th>$text_order_order_id</th>";
				$csv_data[0][$j++].=$text_order_order_id;
			echo "<th>$text_order_order_time</th>";
				$csv_data[0][$j++].=$text_order_order_time;
			echo "<th>$text_order_distributor</th>";
				$csv_data[0][$j++].=$text_order_distributor;
			echo "<th>$text_order_code</th>";
				$csv_data[0][$j++].=$text_order_code;
			echo "<th>$text_order_quantity</th>";
				$csv_data[0][$j++].=$text_order_quantity;
			echo "<th>$text_order_price</th>";
				$csv_data[0][$j++].=$text_order_price;
			echo "<th>$text_order_manufacturer</th>";
				$csv_data[0][$j++].=$text_order_manufacturer;
			echo "<th>$text_order_customer_name</th>";
				$csv_data[0][$j++].=$text_order_customer_name;
			echo "<th>$text_order_customer_phone</th>";
				$csv_data[0][$j++].=$text_order_customer_phone;
			echo "<th>$text_order_customer_address</th>";
				$csv_data[0][$j++].=$text_order_customer_address;
			echo "<th>$text_order_delivery_time</th>";
				$csv_data[0][$j++].=$text_order_delivery_time;
			echo "<th class='rounded-last'>$text_order_note</th>";
				$csv_data[0][$j++].=$text_order_note;
		echo "</tr>";
	echo "</thead>";
	echo "<tfoot><tr>";
	echo "<td colspan='$orders_cols_num' class='rounded-foot'><em><b>$text_order_orders_quantity: ".mysql_num_rows($result_orders)."</b></em></td>";
	echo "</tr></tfoot>";
	echo "<tbody>";
$i=0;
while($row_orders = mysql_fetch_array($result_orders))
{
		$j=0;
		($i % 2)?$class="odd":$class="even";
		echo "<tr>";
			echo "<td class='$class' align=\"center\">".$row_orders['source']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['source'];
			echo "<td class='$class' align=\"center\">".$row_orders['id']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['id'];
			echo "<td class='$class' align=\"center\">".$row_orders['order_time']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['order_time'];
			echo "<td class='$class' align=\"center\">".$row_orders['distributor']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['distributor'];
			echo "<td class='$class' align=\"center\">".$row_orders['code']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['code'];
			echo "<td class='$class' align=\"center\">".$row_orders['quantity']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['quantity'];
			echo "<td class='$class' align=\"center\">".$row_orders['price']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['price'];
			echo "<td class='$class' align=\"center\">".$row_orders['manufacturer']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['manufacturer'];
			echo "<td class='$class' align=\"center\">".$row_orders['customerName']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['customerName'];
			echo "<td class='$class' align=\"center\">".$row_orders['customerPhone']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['customerPhone'];
			echo "<td class='$class' align=\"center\">".$row_orders['customerAddress']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['customerAddress'];
			echo "<td class='$class' align=\"center\">".$row_orders['deliveryTime']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['deliveryTime'];
			echo "<td class='$class' align=\"center\">".$row_orders['note']."</td>";
				$csv_data[$i+1][$j++].=$row_orders['note'];
		echo "</tr>";
		$i++;
}
	echo "</tbody>";
echo "</table>";

require_once ('../conf/db_disconnect.php');

?>
<input class="button" type="button" onclick="getcsv('1')" value="Get a CSV File!">
<br><br>
<input class="button" type="button" onClick="window.print()" value="<?=$print_page?>"/>
<?
if(isset($csv_data) && $_GET['csv']==1)
{	
	$fp = fopen("../tmp/tmp_file_orders_$store.csv", 'w');
	foreach ($csv_data as $fields) 
		fputcsv($fp, $fields);
	fclose($fp);
	echo '<script type="text/javascript">';
	echo "javascript:window.location='download_csv.php?store=orders_$store';";
	echo '</script>';
}
?>
