<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../css/style.css" type="text/css" media="screen" charset="utf-8">
<?
require_once('./validation.php');
require_once('../general_functions/phone_validation.php');
?>
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
$orders_site_db="Orders.Orders_site";
$color_end="</font>";
$distributor_field_number=0; // distributor=priority=email=Conctact Person
$distributor_email_field_number=2; // erc=1=ei@osis.com.ua=Petrov Vladislav,test=2=ei@osis.com.ua=Test Name

if ($_GET['action']=="cancel")
{
	$state="canceled";
	$update_order_state=mysql_query("UPDATE $orders_db SET state='$state', state_note='{$_GET['cancel_note']}'  WHERE id='{$_GET['id']}'");
	echo "<br><br><b>Store Order #{$_GET['id']} Canceled</b>";
	break;
}

if ($_GET['action']=="order")
{
	$id=$_GET['id'];
	$operator=$_GET['operator'];
	$get_operator_consultant="SELECT consultant FROM Operators.auth WHERE login LIKE '$operator'";
	$consultation=mysql_result(mysql_query($get_operator_consultant),0,'consultant');

	$query_storename="SELECT * FROM $orders_db WHERE id LIKE '$id'";
	$storename=mysql_result(mysql_query($query_storename),0,'store');
	$storeOrderId=mysql_result($query_storename,0,'storeOrderId');
	$source=mysql_result(mysql_query($query_storename),0,'source');

	$query_product="SELECT * FROM Orders.Orders_cart WHERE quantity!='0' AND orderID='$id' order by distributor asc";
	$product=mysql_query($query_product);

	$query_store="SELECT email,distributors FROM Stores.List WHERE name LIKE '$storename'";
	$store=mysql_query($query_store);
	$store_email=mysql_result($store,0,'email');
	$delimeters=Array(",","=");
	$distrs=multiexplode($delimeters,mysql_result($store,0,'distributors'));

	$distributor="";
	$distributor_email="";
	$product_counter=0;
	$body_store = "You have order #: $id \n";
	if(isset($storeOrderId) && $storeOrderId!="")
		$body_store.= "Store Order #: $storeOrderId \n";
	$body_store.= "Order source: $source \n\n Store Name: $storename";
	$mail_distributors_success=0;

	while($row = mysql_fetch_array($product))
	{
		if($distributor!=$row['distributor'])
		{
			if($distributor_email!="")
			{
				/// Send Message to previous Distributor
				$distributor_upper=strtoupper($distributor);
				$body_store.="\n\n\t Distributor: $distributor_upper ".$body_distributor;
				$message_to_distributor = "Please set invoice: \n\n\t Order #: $id \n\t Store Name: $storename ".$body_distributor;
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
	$state="complete";

	/// Save Order to DB
	$order_time="$date $time";
	$query_add_order="UPDATE $orders_db SET customerFirstName='$customerFirstName', customerLastName='$customerLastName', customerPhone='$customerPhone', shippingType='$shippingType', customerAddress='$customerAddress', deliveryTime='$deliveryTime', note='$note', consultation='$consultation'  WHERE id='$id'";
	$add_order=mysql_query($query_add_order);

	/// Mail to Store
	$to_store = $store_email;
	$subject_store = "Order #$id \nOrder source: $source";
	$body_store .= "\n\n\t Customer First Name: $customerFirstName \n\t Customer Last Name: $customerLastName \n\t Customer Phone: $customerPhone \n\t Shipping Type : $shippingType \n\t Customer Address: $customerAddress \n\t Note: $note \n\t Delivery Time: $deliveryTime";
	$headers_store = "From: $from_email \r\n X-Mailer: php";
	if ($mail_distributors_success==1)
	{
		if (mail($to_store, $subject_store, $body_store, $headers_store))
		{
			$update_order_state=mysql_query("UPDATE $orders_db SET state='$state' WHERE id='$id'");
			echo("<p><b>Order is placed!</b></p>");
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
			echo("<p><b>Order is placed!</b></p>");
		}
		else
		{
			$state="";
			echo("<p>Error while placing an order...</p>");
			$unblock_order=mysql_query("UPDATE $orders_db SET state='$state' WHERE id='$id'");
		}
	}
	else
	{
		$state="";
		echo("<p>Error while placing an order...<br>Distributors haven't this product</p>");
		$unblock_order=mysql_query("UPDATE $orders_db SET state='$state' WHERE id='$id'");
	}
}
else
{
	require_once('../general_functions/ajaxGetPage.php');
	$callID=$_GET['callID'];

	$storename=mysql_result(mysql_query("SELECT `store` FROM $orders_db WHERE `callID` LIKE '$callID'"),0,'store');

	if(!$id=$_GET['id'] OR $_GET['id']=='phone')
	{
		$query_order_id="SELECT `id` FROM $orders_db WHERE `callID` LIKE '$callID'";
		$id=mysql_result(mysql_query($query_order_id),0,'id');
	}

	$query_product="SELECT * FROM Orders.Orders_cart WHERE orderID='$id'";
	$product=mysql_query($query_product);

	if($callID=="site")
	{
		$block_order=mysql_query("UPDATE $orders_db SET state='blocked' WHERE id='$id'");
		$query_customer_info="SELECT * FROM $orders_db where `id` LIKE '$id'";
		$customer_info=mysql_query($query_customer_info);
		
		$storeOrderId=mysql_result($customer_info,0,'storeOrderId');
		$quantity=mysql_result($customer_info,0,'quantity');
		$priceUAH=mysql_result($customer_info,0,'priceUAH');
		$customerFirstName=mysql_result($customer_info,0,'customerFirstName');
		$customerLastName=mysql_result($customer_info,0,'customerLastName');
		$customerPhone=mysql_result($customer_info,0,'customerPhone');
		$shippingType=mysql_result($customer_info,0,'shippingType');
		$customerAddress=mysql_result($customer_info,0,'customerAddress');
		$deliveryTime=mysql_result($customer_info,0,'deliveryTime');
		$note=mysql_result($customer_info,0,'note');
	
		$chars = array(" ", "-", "/", "+", ":", "_"); 
		$customerPhone = str_replace($chars, "", $customerPhone); // replace " ", "-", "/", "+", ":" with ""
	}
	else
		$storeOrderId="phone";
	?>
	<center>
	<form name="frm" id="frm" onsubmit="return checkRequired(this);">
		<input name="action" id="action" value="order" type="hidden">
		<input name="id" id="id" value="<?=$id?>" type="hidden">
		<input name="operator" value="<?=$_GET['operator']?>" type="hidden">
		<div class='storeName'>
		<b>Store Name:  <?=$storename?><input name="storename" value="<?=$storename?>" type="hidden"></b>
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
		<input  class='rounded-textbox-search' name="customerFirstName" value="<?if(isset($customerFirstName))echo $customerFirstName;?>" type="text" required><br>
		Customer Last Name:<br>
		<input  class='rounded-textbox-search' name="customerLastName" value="<?if(isset($customerLastName))echo $customerLastName;?>" type="text"><br>
		*Customer Phone:<br>
		<input  class='rounded-textbox-search' id="phoneNumber" name="customerPhone" value="<?if(isset($customerPhone))echo $customerPhone;?>" type="text" required><br>
		<?
		if($callID=="site")
			if(phone_validation($customerPhone)!=true)
				echo "<b><font color=\"ff0000\">Not Valid</font></b>";
		?>
		<br><br>
		*Shipping Type:<br>
		<?
//		echo "SELECT shippingType FROM Stores.List WHERE name LIKE '$storename'";
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
		<textarea class='rounded-textbox-search' rows="3" cols="30" name="customerAddress" required><?if(isset($customerAddress))echo $customerAddress;?></textarea><br>
		Note:<br>
		<textarea class='rounded-textbox-search' rows="5" cols="30" name="note"><?if(isset($note))echo $note;?></textarea><br>
		*Delivery Time:<br>
		<input  class='rounded-textbox-search' name="deliveryTime" value="<?if(isset($deliveryTime))echo $deliveryTime;?>" type="text" required><br>
</div></div>
<?if($callID=="site"){?>
<input class='buttonCall' type="button" VALUE="Call" OnClick='javascript:ajaxpage("../general_functions/originate.php?operator=<?=$operator?>&customerPhone="+frm.phoneNumber.value+"&storename=<?=$storename?>")'>

<?}?>
		<input class='buttonPlaceOrder' name="submit" value="Place Order" type="submit"><br>
	</form>
<form><input class='buttonCall' type=button value="Delay Order" onClick="javascript: window.close();ajaxpage('./unblock_order.php?id=<?=$id?>');"></form> 

	<div class='orderCancel'>
	<form name="frm_cancel" id="frm_cancel" onsubmit="return checkRequired(this);">
	<input name="action" id="action" value="cancel" type="hidden">
	<input name="id" id="id" value="<?=$id?>" type="hidden">
	Cancel Note:<br>
	<textarea class='rounded-textbox-search' rows="5" cols="30" name="cancel_note" required></textarea><br>
	</div>
	<input class='buttonCancelOrder' name="submit" value="Cancel" type="submit">	
	</form>

	</center>
	<?
}
require_once ('../conf/db_disconnect.php');
?>

</body>
</html>
