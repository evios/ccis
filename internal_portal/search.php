<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
require_once('../general_functions/redirect_call.php');
$callID="124"; /// Test CallID
echo "<div id='tabclient' class='css-tabs'><ul class='menu'><li><div id='cart_info' class='top_right_num'></div><a href OnClick=\"NewWindowOrder('$callID','phone','{$_GET['operator']}')\">Order</a></li></ul></div>";
//echo "<input class='buttonShort' type=\"button\" VALUE=\"Cart\" OnClick=\"NewWindowOrder('$callID')\">";

$q=$_GET["q"];
if (strlen($q)>3)
{
require_once('used_distributors.php');
/// Cash/Cashless Rates
$result_rate_cash=mysql_query("SELECT `rate_USD_cash` FROM Stores.List Where `name` LIKE '$storename'");
$result_rate_cashless=mysql_query("SELECT `rate_USD_cashless` FROM Stores.List Where `name` LIKE '$storename'");
$rate_cash=mysql_result($result_rate_cash,0,'rate_USD_cash');
$rate_cashless=mysql_result($result_rate_cashless,0,'rate_USD_cashless');

$keywords = preg_split("/[\s,]+/", $q);
$color_end="</font>";

$query="";
for($d=0;$d<sizeof($distrs);$d++)
{
	$i=0;
	$distributor=$distrs[$d];
	$query.="( SELECT * FROM  Distributors.D_$distributor, Stores.S_$storename WHERE ";
	foreach ($keywords as $key)
	{
		$i++;
		$query.="(Distributors.D_$distributor.code LIKE  '%".$key."%' OR  Distributors.D_$distributor.description LIKE  '%".$key."%' OR  Distributors.D_$distributor.manufacturer LIKE  '%".$key."%') AND (Stores.S_$storename.code = Distributors.D_$distributor.code)";
		($i<sizeof($keywords))?$query.=" AND ":"";
	}
	$query=$query." AND Distributors.D_$distributor.availability LIKE 1 )"; // search only products that available
	((sizeof($distrs)-$d)==1)?"":$query.=" UNION ";
}
$query." order by `manufacturer` asc";

if ($result = mysql_query($query))
{
	if(mysql_num_rows($result)==0)
	{
		echo "<b>Найдено: 0</b><br><br>";
	}
	else
	{
		$search_cols_num=7;
		echo "<b>Найдено: ".mysql_num_rows($result)."</b><br><br>";
		echo "<table id='rounded-corner' cellpadding=\"1\" cellspacing=\"1\">
		<thead><tr>
		<th class='rounded-first'>Manufacturer</th>
		<th>Code</th>
		<th>Description</th>
		<th>PriceUAH</th>
		<th>PriceUSD</th>
		<th>Availability</th>
		<th class='rounded-last'>Order</th>
		</tr></thead>
		<tfoot><tr>
		<td colspan='$search_cols_num' class='rounded-foot'><em><b>Найдено: ".mysql_num_rows($result)."</b></em></td>
		</tr></tfoot>
		<tbody>";
		if($result!=0)
		{
			$u=0;
			while($row = mysql_fetch_array($result))
			{
				($u % 2)?$class="odd":$class="even";
				$priceUAHCashless=round($row['priceUAH']/$rate_cash*$rate_cashless,2);
				$priceUSD=round($row['priceUAH']/$rate_cash,2);
				$distributor_field=$row['distributor'];
				$code=$row['code'];
				if ($row['availability']=="1")
				{
					$availability="Есть";
					$color="<font color=\"037e4c\">";
				}
				else
				{
					$availability="Нет";
					$color="<font color=\"ff0000\">";
				}
			  	echo "<tr>";
				echo "<td class='$class' align=\"center\"> $color {$row['manufacturer']} $color_end </td>";
				echo "<td class='$class' align=\"center\"> $color $code $color_end </td>";
				echo "<td class='$class' align=\"center\"> $color {$row['description']} $color_end </td>";
				echo "<td class='$class' align=\"center\"> $color {$row['priceUAH']} $color_end </td>";
				echo "<td class='$class' align=\"center\"> $color $priceUSD $color_end </td>";
				echo "<td class='$class' align=\"center\"> $color $availability $color_end </td>";
//				echo "<td class='$class' align=\"center\"><input class='buttonShort' type=\"button\" VALUE=\"Order\" OnClick=\"NewWindowOrder('$storename','$distributor_field','$code','phone','$operator')\"></td>";
				echo "<td class='$class' align=\"center\"><input class='buttonShort' type=\"button\" VALUE=\"Add\" OnClick=\"javascript:addToCart('$distributor_field','$code','{$row['manufacturer']}','{$row['description']}','{$row['priceUAH']}','$priceUAHCashless','$priceUSD','{$row['availability']}','$callID','$operator','$storename', 'phone');\"></td>";
//				echo "<td class='$class' align=\"center\"><input class='buttonShort' type=\"button\" VALUE=\"Order\" OnClick=\"javascript:addToCart()\"></td>";

				echo "</tr>";
				if($i++ >100){break;}
				$u++;
			  }
		}
		echo "</tbody></table> ";
	}
}
else
{
	echo "<b>У Вас нету активных вызовов</b><br><br>";
}
require_once ('../conf/db_disconnect.php');
}
?>
