<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<?
require_once('./validation.php');
require_once('../general_functions/phone_validation.php');
?>
<link rel="stylesheet" href="../css/style.css" type="text/css" media="screen" charset="utf-8">
<title>Order</title>
</head>
<body>
<?
require_once('../general_functions/multiexplode.php');
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
date_default_timezone_set('Europe/Kiev');
$date=date('Y-m-d');
$time=date('H:i:s');
$from_email="osis@osis.com.ua";
$orders_db="Orders.Orders";
$color_end="</font>";
$distributor_field_number=0; // distributor=priority=email=Conctact Person
$distributor_email_field_number=2; // erc=1=ei@osis.com.ua=Petrov Vladislav,test=2=ei@osis.com.ua=Test Name

if ($_GET['action']=="cancel")
{
	$state="canceled";
	$update_order_state=mysql_query("UPDATE $orders_db SET state='$state', state_note='{$_GET['cancel_note']}'  WHERE id='{$_GET['id']}'");
	echo "<br><br><b>Order #$id Canceled</b>";
	break;
}

if ($_GET['action']=="edit")
{
	$id=$_GET['id'];
	$operator=$_GET['operator'];

//	$order_detail=mysql_query("SELECT * FROM Orders.Orders WHERE id='$id' LIMIT 1");

	$query_product="SELECT * FROM Orders.Orders_cart WHERE quantity!='0' AND orderID='$id' order by distributor asc";
	$product=mysql_query($query_product);
	
/*	$source=mysql_result($order_detail,0,'source');
	$storeOrderId=mysql_result($order_detail,0,'storeOrderId');
	$storename=mysql_result($order_detail,0,'store');
	$distributor=mysql_result($order_detail,0,'distributor');
	$code=mysql_result($order_detail,0,'code');
	$manufacturer=mysql_result($order_detail,0,'manufacturer');
	$description=mysql_result($order_detail,0,'description');
	$quantity_old=mysql_result($order_detail,0,'quantity');
	$consultation=mysql_result($order_detail,0,'consultation');

	$distributor_upper=strtoupper($distributor);
	$quantity=$_GET['quantity'];
	$priceUAH=mysql_result($order_detail,0,'priceUAH');
	$priceUAHCashless=mysql_result($order_detail,0,'priceUAHCashless');
	$priceUSD=mysql_result($order_detail,0,'priceUSD');
	$customerFirstName=$_GET['customerFirstName'];
	$customerLastName=$_GET['customerLastName'];
	$customerPhone=$_GET['customerPhone'];
	$shippingType=$_GET['shippingType'];
	$customerAddress=$_GET['customerAddress'];
	$note=$_GET['note'];
	$deliveryTime=$_GET['deliveryTime'];

	$delimeters=Array(",","=");
	$query_store="SELECT `email`,`distributors` FROM Stores.List WHERE `name` LIKE '$storename'";
	$store=mysql_query($query_store);

	if ($quantity-$quantity_old!=0)
	{
		$distrs=multiexplode($delimeters,mysql_result($store,0,'distributors'));
		for($i=0;$i<sizeof($distrs);$i++)
		{
			if($distributor==$distrs[$i][$distributor_field_number])
			{
				$distributor_email=$distrs[$i][$distributor_email_field_number];
			}
		}
	}
	$store_email=mysql_result($store,0,'email');

	
	/// Update Order in DB
	$state="edited";
 	$query_update_order="UPDATE $orders_db SET state='$state' WHERE id='$id'";
	$update_order=mysql_query($query_update_order);

	/// Save new order to DB
	$order_time="$date $time";
	$state="complete";
echo	$query_add_order="INSERT INTO $orders_db (id, order_time, source, state, storeOrderId, store, distributor, code, manufacturer, description, quantity, priceUAH, priceUAHCashless, priceUSD, customerFirstName, customerLastName, customerPhone, shippingType, customerAddress, deliveryTime, note, operator, previousID, consultation) VALUES (NULL, '$order_time', '$source', '$state',  '$storeOrderId', '$storename', '$distributor', '$code', '$manufacturer', '$description', '$quantity', '$priceUAH', '$priceUAHCashless', '$priceUSD',  '$customerFirstName', '$customerLastName', '$customerPhone', '$shippingType', '$customerAddress', '$deliveryTime', '$note', '$operator', '$id', '$consultation')";
	$add_order=mysql_query($query_add_order);
	
	$query_order_id="SELECT `id` FROM $orders_db WHERE `order_time` LIKE '$order_time' AND `store` LIKE '$storename' AND `code` LIKE '$code' AND `quantity` LIKE '$quantity' AND `customerPhone` LIKE '$customerPhone'";
	$order_id=mysql_result(mysql_query($query_order_id),0,'id');

	$update_order_state=mysql_query("UPDATE $orders_site_db SET state='$state' WHERE storeOrderId='$storeOrderId'");
	
	/// Mail to Store
	$to_store = $store_email;
	$subject_store = "UPDATE Order #$id";
	$body_store = "Old order #: $id \nNew Order #: $order_id \nStore Order #: $storeOrderId \nOrder source: $source \n\n\t Store Name: $storename \n\t Distributor: $distributor_upper ";
	$body_store .="\n\n\t Code: $code \n\t Quantity: $quantity \n\t Price UAH: $priceUAH \n\t Price UAH Cashless: $priceUAHCashless \n\t Price USD : $priceUSD \n\t Manufacturer: $manufacturer \n\t Description: $description";
	$body_store .= "\n\n\t Customer First Name: $customerFirstName \n\t Customer Last Name: $customerLastName \n\t Customer Phone: $customerPhone \n\t Shipping Type : $shippingType \n\t Customer Address: $customerAddress \n\t Note: $note \n\t Delivery Time: $deliveryTime";
	$headers_store = "From: $from_email \r\n X-Mailer: php";

	if ($quantity-$quantity_old!=0)
	{
		/// Mail to Distributor
		$to_distributor = $distributor_email;
		$subject_distributor = "Please update invoice for $storename";
		$body_distributor = "Please update invoice: \n\n\t Old order #: $id \n\t New order #: $order_id \n\t Store Name: $storename ";
		$body_distributor .="\n\n\t Code: $code \n\t Quantity: $quantity \n\t Manufacturer: $manufacturer \n\t Description: $description";
		$headers_distributor = "From: $store_email \r\n X-Mailer: php";
		mail($to_distributor, $subject_distributor, $body_distributor, $headers_distributor);
	}

	if (mail($to_store, $subject_store, $body_store, $headers_store))
	{
		echo("<p><b>Order is updated!</b></p>");
		?>
		<b>Order #: <?=$order_id?></b><br> 
		<b>Store Name:  <?=$storename?></b><br>
		<table border="1" id=\"resultTable\">
			<thead>
				<tr>
					<th>Manufacturer</th>
					<th>Code</th>
					<th>Description</th>
					<th>Price UAH</th>
					<th>Price UAH Cashless</th>
					<th>Price USD</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="center"><?=$manufacturer?>
					<td align="center"><?=$code?>
					<td align="center"><?=$description?>
					<td align="center"><?=$priceUAH?>
					<td align="center"><?=$priceUAHCashless?>
					<td align="center"><?=$priceUSD?>
				</tr>
			</tbody>
		</table>
		<br>
		Quantity:<br><input name="quantity"value="<?=$quantity?>" type="text" READONLY><br>
		<br>
		Customer First Name:<br>
		<input name="customerFirstName" value="<?if(isset($customerFirstName))echo $customerFirstName;?>" type="text"><br>
		Customer Last Name:<br>
		<input name="customerLastName" value="<?if(isset($customerLastName))echo $customerLastName;?>" type="text"><br>
		Customer Phone:<br>
		<input name="customerPhone" value="<?=$customerPhone?>" type="text" READONLY><br>
		Shipping Type:<br>
		<input name="shippingType" value="<?=$shippingType?>" type="text" READONLY><br>
		Customer Address:<br>
		<textarea rows="3" cols="30" name="customerAddress" READONLY><?=$customerAddress?></textarea><br>
		Note:<br>
		<textarea rows="5" cols="30" name="note" READONLY><?=$note?></textarea><br>
		Delivery Time:<br>
		<input name="deliveryTime" value="<?=$deliveryTime?>" type="text" READONLY><br>

		<?
		echo("<p><b>Order is updated!</b></p>");
	}
	else
	{
		echo("<p>Error while placing an order...</p>");
	}
*/
	$query_storename="SELECT * FROM $orders_db WHERE id LIKE '$id'";
	$storename=mysql_result(mysql_query($query_storename),0,'store');

	$query_store="SELECT email,distributors FROM Stores.List WHERE name LIKE '$storename'";
	$store=mysql_query($query_store);
	$store_email=mysql_result($store,0,'email');
	$delimeters=Array(",","=");
	$distrs=multiexplode($delimeters,mysql_result($store,0,'distributors'));

	$distributor="";
	$distributor_email="";
	$product_counter=0;
	$body_store = "Order #: $id UPDATED \n";
	if(isset($storeOrderId) && $storeOrderId!="")
		$body_store.= "Store Order #: $storeOrderId \n";
	$body_store.= "Order source: $source \n\n Store Name: $storename";
	$mail_distributors_success=0;

	while($row = mysql_fetch_array($product))
	{
//	echo $row['code'];
		if($distributor!=$row['distributor'])
		{
			if($distributor_email!="")
			{
				/// Send Message to previous Distributor
				$distributor_upper=strtoupper($distributor);
				$body_store.="\n\n\t Distributor: $distributor_upper ".$body_distributor;
				$message_to_distributor = "Please update invoice: \n\n\t Order #: $id \n\t Store Name: $storename ".$body_distributor;
				if(mail($distributor_email, $subject_distributor, $message_to_distributor, $headers_distributor))
					$mail_distributors_success=1;
				else
					$mail_distributors_success=0;
			}
			$distributor=$row['distributor'];

			for($i=0;$i<sizeof($distrs);$i++)
			{
				if($distributor==$distrs[$i][$distributor_field_number])
				{
					$distributor_email=$distrs[$i][$distributor_email_field_number];
				}
			}
			/// Mail to Distributor
			$subject_distributor = "Please set invoice for $storename";
			$headers_distributor = "From: $store_email \r\n X-Mailer: php";
		}
		if (mysql_num_rows($product)==($product_counter+1))
		{
			/// Send Message on Last Product of Last Distributor in Cart
			$code=$row['code'];
			$manufacturer=$row['manufacturer'];
			$description=$row['description'];
			$priceUAH=$row['priceUAH'];
			$priceUSD=$row['priceUSD'];
			$priceUAHCashless=$row['priceUAHCashless'];
			$quantity=$row['quantity'];
			$availability=$row['availability'];

			$body_distributor .="\n\n\t Code: $code \n\t Quantity: $quantity \n\t Manufacturer: $manufacturer \n\t Description: $description";
			$distributor_upper=strtoupper($distributor);
			$body_store.="\n\n\t Distributor: $distributor_upper ".$body_distributor;
			$message_to_distributor = "Please set invoice: \n\n\t Order #: $id \n\t Store Name: $storename ".$body_distributor;
			if(mail($distributor_email, $subject_distributor, $message_to_distributor, $headers_distributor))
				$mail_distributors_success=1;
			else
				$mail_distributors_success=0;
		}

		$code=$row['code'];
		$manufacturer=$row['manufacturer'];
		$description=$row['description'];
		$priceUAH=$row['priceUAH'];
		$priceUSD=$row['priceUSD'];
		$priceUAHCashless=$row['priceUAHCashless'];
		$quantity=$row['quantity'];
		$availability=$row['availability'];

		$body_distributor .="\n\n\t Code: $code \n\t Quantity: $quantity \n\t Manufacturer: $manufacturer \n\t Description: $description";
		$product_counter++;
	}

	$customerFirstName=$_GET['customerFirstName'];
	$customerLastName=$_GET['customerLastName'];
	$customerPhone=$_GET['customerPhone'];
	$shippingType=$_GET['shippingType'];
	$customerAddress=$_GET['customerAddress'];
	$note=$_GET['note'];
	$deliveryTime=$_GET['deliveryTime'];

	/// Save Order to DB
	$state="complete_edited";
	$order_time="$date $time";
	$query_add_order="UPDATE $orders_db SET customerFirstName='$customerFirstName', customerLastName='$customerLastName', customerPhone='$customerPhone', shippingType='$shippingType', customerAddress='$customerAddress', deliveryTime='$deliveryTime', note='$note', consultation='$consultation'  WHERE id='$id'";

	$add_order=mysql_query($query_add_order);

	/// Mail to Store
	$to_store = $store_email;
	$subject_store = "UPDATE Order #$id \nOrder source: $source";
	$body_store .= "\n\n\t Customer First Name: $customerFirstName \n\t Customer Last Name: $customerLastName \n\t Customer Phone: $customerPhone \n\t Shipping Type : $shippingType \n\t Customer Address: $customerAddress \n\t Note: $note \n\t Delivery Time: $deliveryTime";
	$headers_store = "From: $from_email \r\n X-Mailer: php";

//echo $distributor_email;
//echo $subject_distributor;
//echo $message_to_distributor;
//echo $headers_distributor;
	if ($mail_distributors_success==1)
	{
		if (mail($to_store, $subject_store, $body_store, $headers_store))
		{
			$update_order_state=mysql_query("UPDATE $orders_db SET state='$state' WHERE id='$id'");
			echo("<p><b>Order is updated!</b></p>");
			?>
			<b>Order #: <?=$id?></b><br> 
			<table id='rounded-corner'>
				<thead>
					<tr>
						<th class='rounded-first'>Manufacturer</th>
						<th>Code</th>
						<th>Description</th>
						<th>Quantity</th>
						<th>Price UAH</th>
						<th>Price UAH Cashless</th>
						<th>Price USD</th>
						<th class='rounded-last'>Availability</th>
					</tr>
				</thead>
				<?
				$query_product="SELECT * FROM Orders.Orders_cart WHERE quantity!='0' AND orderID='$id' order by distributor asc";
				$product=mysql_query($query_product);

				$cart_cols_num=8;
				echo "<tfoot><tr>";
				echo "<td colspan='$cart_cols_num' class='rounded-foot'><em><b>Товаров в корзине: ".mysql_num_rows($product)."</b></em></td>";
				echo "</tr></tfoot>";
				echo "<tbody>";
				$u=0;
				while($row = mysql_fetch_array($product))
				{
					($u % 2)?$class="odd":$class="even";
					$code=$row['code'];
					$manufacturer=$row['manufacturer'];
					$description=$row['description'];
					$priceUAH=$row['priceUAH'];
					$priceUSD=$row['priceUSD'];
					$priceUAHCashless=$row['priceUAHCashless'];
					$quantity=$row['quantity'];

					$availability=$row['availability'];
					if($availability=="1")
						$color="<font color=\"037e4c\">";
					else
						$color="<font color=\"ff0000\">";

					echo "<tr>";
					echo "<td class='$class' align='center'>$color $manufacturer $color_end</td>";
					echo "<td class='$class' align='center'>$color $code $color_end</td>";
					echo "<td class='$class' align='center'>$color $description $color_end</td>";
					echo "<td class='$class' align='center'>$quantity</td>";
					echo "<td class='$class' align='center'>$color $priceUAH $color_end</td>";
					echo "<td class='$class' align='center'>$color $priceUAHCashless $color_end</td>";
					echo "<td class='$class' align='center'>$color $priceUSD $color_end</td>";
					echo "<td class='$class' align='center'>";
					if($availability=="1")
						echo "$color<b>Есть</b>$color_end";
					else
						echo "$color<b>Нет</b>$color_end";
					echo "</td>";
					echo "</tr>";
					$u++;
				}
				echo "</tbody>";
				?>
			</table>
			<br>
			Customer First Name:<br>
			<input name="customerFirstName" value="<?if(isset($customerFirstName))echo $customerFirstName;?>" type="text"><br>
			Customer Last Name:<br>
			<input name="customerLastName" value="<?if(isset($customerLastName))echo $customerLastName;?>" type="text"><br>
			Customer Phone:<br>
			<input name="customerPhone" value="<?=$customerPhone?>" type="text" READONLY><br>
			Shipping Type:<br>
			<input name="shippingType" value="<?=$shippingType?>" type="text" READONLY><br>
			Customer Address:<br>
			<textarea rows="3" cols="30" name="customerAddress" READONLY><?=$customerAddress?></textarea><br>
			Note:<br>
			<textarea rows="5" cols="30" name="note" READONLY><?=$note?></textarea><br>
			Delivery Time:<br>
			<input name="deliveryTime" value="<?=$deliveryTime?>" type="text" READONLY><br>

			<?
			echo("<p><b>Order is updated!</b></p>");
		}
		else
		{
			$state="";
			echo("<p>Error while updating an order...</p>");
//			$unblock_order=mysql_query("UPDATE $orders_db SET state='$state' WHERE id='$id'");
		}
	}
	else
	{
		$state="complete";
		echo("<p>Error while placing an order...<br>Distributors haven't this product</p>");
		$unblock_order=mysql_query("UPDATE $orders_db SET state='$state' WHERE id='$id'");
	}


}
else
{
	require_once('../general_functions/ajaxGetPage.php');
	$id=$_GET['id'];
	$operator=$_GET['operator'];
	$order_detail=mysql_query("SELECT * FROM Orders.Orders WHERE id='$id' LIMIT 1");
	
	$storeOrderId=mysql_result($order_detail,0,'storeOrderId');
	$storename=mysql_result($order_detail,0,'store');

	$query_product="SELECT * FROM Orders.Orders_cart WHERE quantity!='0' AND orderID='$id'";
	$product=mysql_query($query_product);


//	$code=mysql_result($order_detail,0,'code');
//	$manufacturer=mysql_result($order_detail,0,'manufacturer');
//	$description=mysql_result($order_detail,0,'description');
//	$quantity=mysql_result($order_detail,0,'quantity');
//	$priceUAH=mysql_result($order_detail,0,'priceUAH');
//	$priceUAHCashless=mysql_result($order_detail,0,'priceUAHCashless');
//	$priceUSD=mysql_result($order_detail,0,'priceUSD');
	$customerFirstName=mysql_result($order_detail,0,'customerFirstName');
	$customerLastName=mysql_result($order_detail,0,'customerLastName');
	$customerPhone=mysql_result($order_detail,0,'customerPhone');
	$shippingType=mysql_result($order_detail,0,'shippingType');
	$customerAddress=mysql_result($order_detail,0,'customerAddress');
	$deliveryTime=mysql_result($order_detail,0,'deliveryTime');
	$note=mysql_result($order_detail,0,'note');
//	$operator_old=mysql_result($order_detail,0,'operator');

	?>
	<center>
	<form name="frm" id="frm" onsubmit="return checkRequired(this);">
		<input name="action" id="action" value="edit" type="hidden">
		<input name="id" id="id" value="<?=$id?>" type="hidden">
		<input name="operator" value="<?=$operator?>" type="hidden">
		<div class='storeName'>
		<b>Store Name:  <?=$storename?></b>
		</div>
		<table id='rounded-corner'>
			<thead>
				<tr>
					<th class='rounded-first'>Manufacturer</th>
					<th>Code</th>
					<th>Description</th>
					<th>Quantity</th>
					<th>Price UAH</th>
					<th>Price UAH Cashless</th>
					<th>Price USD</th>
					<th>Availability</th>
					<th class='rounded-last'>Delete</th>
				</tr>
			</thead>
				<?
				$cart_cols_num=9;
				echo "<tfoot><tr>";
				echo "<td colspan='$cart_cols_num' class='rounded-foot'><em><b>Товаров в корзине: ".mysql_num_rows($product)."</b></em></td>";
				echo "</tr></tfoot>";
				echo "<tbody>";
				$u=0;
				while($row = mysql_fetch_array($product))
				{
					$color="";
					$k=0;
					$class_button_delete="buttonShort";
					$class_quantity="rounded-textbox-quantity";
					($u % 2)?$class="odd":$class="even";
					$temp_class=$class;
					$code=$row['code'];
					$manufacturer=$row['manufacturer'];
					$description=$row['description'];
					$priceUAH=$row['priceUAH'];
					$priceUSD=$row['priceUSD'];
					$priceUAHCashless=$row['priceUAHCashless'];
					$quantity=$row['quantity'];
					if($quantity==0)
					{
						$class="deleted";
						$class_quantity="rounded-textbox-quantity-deleted";
						$class_button_delete="buttonShort_deleted";
						$h_class="h_deleted";
					}

					$availability=$row['availability'];
					if($availability=="1" && $quantity!=0)
						$h_class=$h_class_temp="h_available";
					if($availability=="0" && $quantity!=0)
						$h_class=$h_class_temp="h_nonavailable";
					if($availability=="1" && $quantity==0)
						$h_class_temp="h_available";
					if($availability=="0" && $quantity==0)
						$h_class_temp="h_nonavailable";


					echo "<tr>";
					echo "<td id='td_$k$u' class='$class' align='center'><h id='h_$k$u' class='$h_class'>$manufacturer</h></td>";$k++;
					echo "<td id='td_$k$u' class='$class' align='center'><h id='h_$k$u' class='$h_class'>$code</h></td>";$k++;
					echo "<td id='td_$k$u' class='$class' align='center'><h id='h_$k$u' class='$h_class'>$description</h></td>";$k++;
					echo "<td id='td_$k$u' class='$class' align='center'><h id='h_$k$u' class='$h_class'><input class='$class_quantity' id='quantity_$u' value='$quantity' size='1' onkeyup=\"javascript:
ajaxpage('./change_quantity.php?id={$row['id']}&quantity='+this.value);
if(this.value>0)
{
	document.getElementById('quantity_$u').className='rounded-textbox-quantity';
	for(var i_temp = 0; i_temp <$cart_cols_num; i_temp++){document.getElementById('td_'+i_temp+'$u').className='$temp_class'};
	document.getElementById('button_$u').className='buttonShort';
	for(var i_temp_h = 0; i_temp_h <$cart_cols_num ; i_temp_h++){document.getElementById('h_'+i_temp_h+'$u').className='$h_class_temp'};
}
\"></td>";$k++;
					echo "<td id='td_$k$u' class='$class' align='center'><h id='h_$k$u' class='$h_class'>$priceUAH</h></td>";$k++;
					echo "<td id='td_$k$u' class='$class' align='center'><h id='h_$k$u' class='$h_class'>$priceUAHCashless</h></td>";$k++;
					echo "<td id='td_$k$u' class='$class' align='center'><h id='h_$k$u' class='$h_class'>$priceUSD</h></td>";$k++;
					echo "<td id='td_$k$u' class='$class' align='center'>";
					if($availability=="1")
						echo "<h id='h_$k$u' class='$h_class'><b>Есть</b></h>";
					else
						echo "<h id='h_$k$u' class='$h_class'><b>Нет</b></h>";
					echo "</td>";$k++;
					echo "<td id='td_$k$u' class='$class' align='center'><input id='button_$u' class='$class_button_delete' type='button' name='delete' value='Delete' size='1' OnClick=\"javascript:
ajaxpage('./change_quantity.php?id={$row['id']}&quantity=0');
document.getElementById('quantity_$u').value='0';
document.getElementById('quantity_$u').className='rounded-textbox-quantity-deleted';
for(var i_temp = 0; i_temp <= $k; i_temp++){document.getElementById('td_'+i_temp+'$u').className='deleted'};
document.getElementById('button_$u').className='buttonShort_deleted';
for(var i_temp_h = 0; i_temp_h <= $k; i_temp_h++){document.getElementById('h_'+i_temp_h+'$u').className='h_deleted'};
\"></td>";
					echo "</tr>";
					$u++;
				}
				echo "</tbody>";
				?>
		</table>
		<br>
		<div class='orderInformation'>
		<div class='orderLeft'>
		<br><br>
		*Customer First Name:<br>
		<input class='rounded-textbox-search' name="customerFirstName" value="<?=$customerFirstName?>" type="text" required><br>
		Customer Last Name:<br>
		<input class='rounded-textbox-search' name="customerLastName" value="<?=$customerLastName?>" type="text"><br>
		*Customer Phone:<br>
		<input class='rounded-textbox-search' id="phoneNumber" name="customerPhone" value="<?=$customerPhone?>" type="text" required><br>
		Shipping Type:<br>
		<?
		echo "<select class='button' name=\"shippingType\">";
			$shippingTypeArray=explode(',',mysql_result(mysql_query("SELECT shippingType FROM Stores.List WHERE name LIKE '$storename'"),0,'shippingType'));

			for ($i=0;$i<sizeof($shippingTypeArray);$i++)
			{
				echo "<option value=\"{$shippingTypeArray[$i]}\"";
				if($shippingType==$shippingTypeArray[$i])
					echo " SELECTED";
				echo ">{$shippingTypeArray[$i]}</option>";
			}
		echo "</select>";
		echo "</div>";
		echo "<div class='orderRight'>";
		?>
		*Customer Address:<br>
		<textarea class='rounded-textbox-search' rows="3" cols="30" name="customerAddress" required><?=$customerAddress?></textarea><br>
		Note:<br>
		<textarea class='rounded-textbox-search' rows="5" cols="30" name="note"><?=$note?></textarea><br>
		*Delivery Time:<br>
		<input class='rounded-textbox-search' name="deliveryTime" value="<?=$deliveryTime?>" type="text" required><br>
		</div></div>
		<input class='buttonPlaceOrder' name="submit" value="Update" type="submit"><br>
	</form>

	<div class='orderCancel'>
	<form name="frm_cancel" id="frm_cancel">
	<input name="action" id="action" value="cancel" type="hidden">
	<input name="id" id="id" value="<?=$id?>" type="hidden">
	Cancel Note:<br>
	<textarea class='rounded-textbox-search' rows="5" cols="30" name="cancel_note"></textarea><br>
	</div>
	<div><input class='buttonCancelOrder' name="submit" value="Cancel" type="submit"></div><br>
	</form>

	</center>
	<?
}
require_once ('../conf/db_disconnect.php');
?>


</body>
</html>
