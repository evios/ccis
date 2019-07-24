<?
//require_once('../conf/db_vars.php');
//require_once('../conf/db_connect.php');
$orders_db="Orders.Orders";
//$storename="elampa";
//$storename="503";

//javascript:document.form_date.submit();

$query_orders="SELECT * FROM $orders_db WHERE store LIKE '$storename' AND state NOT LIKE 'edited' AND state LIKE 'complete%' order by `id` asc";
$result_orders = mysql_query($query_orders);
$processed_orders_cols_num=12;
echo "<table id='rounded-corner'>";
	echo "<thead>";
		echo "<tr>";
			echo "<th class='rounded-first'>Order ID</th>";
//			echo "<th>Store</th>";
			echo "<th>Order time</th>";
			echo "<th>Source</th>";
			echo "<th>Store order ID</th>";
//			echo "<th>Code</th>";
//			echo "<th>Distributor</th>";
//			echo "<th>Quantity</th>";
			echo "<th>Price UAH</th>";
//			echo "<th>Price USD</th>";
//			echo "<th>Manufacturer</th>";
			echo "<th>Customer first name</th>";
			echo "<th>Customer last name</th>";
			echo "<th>Customer phone</th>";
//			echo "<th>Customer address</th>";
//			echo "<th>Delivery time</th>";
//			echo "<th>Note</th>";
			echo "<th class='rounded-last'>Edit</th>";
		echo "</tr>";
	echo "</thead>";
	echo "<tfoot><tr><td colspan='$processed_orders_cols_num' class='rounded-foot'><em><b>Processed Orders: ".mysql_num_rows($result_orders)."</b></em></td>	</tr></tfoot>";
	echo "<tbody>";
$i=0;
while($row_orders = mysql_fetch_array($result_orders))
{
	($i % 2)?$class="odd":$class="even";
	$id=$row_orders['id'];
//	$storeOrderId=$row_orders['storeOrderId'];
	$storename=$row_orders['store'];
	$code=$row_orders['code'];
	$distributor_field=$row_orders['distributor'];
		echo "<tr>";
			echo "<td class='$class' align=\"center\">".$row_orders['id']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['store']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['order_time']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['source']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['storeOrderId']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['code']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['distributor']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['quantity']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['priceUAH']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['priceUSD']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['manufacturer']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['customerFirstName']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['customerLastName']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['customerPhone']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['customerAddress']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['deliveryTime']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['note']."</td>";
			echo "<td class='$class' align=\"center\"><input class='buttonShort' type=\"button\" VALUE=\"Edit\" OnClick=\"NewWindowEdit('$id','$operator')\"></td>";
		echo "</tr>";
	$i++;
}
	echo "</tbody>";
echo "</table>";

//require_once ('../conf/db_disconnect.php');
?>
