<?
//require_once('../conf/db_vars.php');
//require_once('../conf/db_connect.php');
$orders_site_db="Orders.Orders";
//$storename="elampa";
//$storename="503";

//javascript:document.form_date.submit();

//$query_orders="SELECT * FROM $orders_site_db WHERE state='' AND `store` LIKE '$storename' order by `id` asc";
$query_orders="SELECT * FROM $orders_site_db WHERE state='' AND source='site' order by `id` asc";
$result_orders = mysql_query($query_orders);
$site_orders_cols_num=7;
echo "<table id='rounded-corner'>";
	echo "<thead>";
		echo "<tr>";
//			echo "<th>Order ID</th>";
			echo "<th class='rounded-first'>Store</th>";
			echo "<th>Store Order ID</th>";
			echo "<th>Order time</th>";
//			echo "<th>Distributor</th>";
//			echo "<th>Code</th>";
//			echo "<th>Quantity</th>";
			echo "<th>Price UAH</th>";
//			echo "<th>Manufacturer</th>";
//			echo "<th>Customer name</th>";
//			echo "<th>Customer phone</th>";
//			echo "<th>Customer address</th>";
//			echo "<th>Delivery time</th>";
//			echo "<th>Note</th>";
			echo "<th class='rounded-last'>Process</th>";
		echo "</tr>";
	echo "</thead>";
	echo "<tfoot><tr><td colspan='$site_orders_cols_num' class='rounded-foot'><em><b>Order from Site: ".mysql_num_rows($result_orders)."</b></em></td>	</tr></tfoot>";
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
//			echo "<td class='$class' align=\"center\">".$row_orders['id']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['store']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['storeOrderId']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['order_time']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['distributor']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['code']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['quantity']."</td>";
			echo "<td class='$class' align=\"center\">".$row_orders['priceUAH']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['manufacturer']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['customerName']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['customerPhone']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['customerAddress']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['deliveryTime']."</td>";
//			echo "<td class='$class' align=\"center\">".$row_orders['note']."</td>";
			echo "<td class='$class' align=\"center\"><input class='buttonShort' type=\"button\" VALUE=\"Process\" OnClick=\"NewWindowOrder('site','$id','$operator')\"></td>";
		echo "</tr>";
	$i++;
}
	echo "</tbody>";
echo "</table>";

//require_once ('../conf/db_disconnect.php');
?>
