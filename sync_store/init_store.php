<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

</head>
<body>
<b> ШАГ 4 </b>
<br><br>

<form action="" name="osis_init" method="post">
<?(!$store_name = ($_POST['store_name']))?"":"";?>
<label>Название Магазина:</label>
<input type="text" name="store_name" size="50" value="<?=$store_name?>" class="textbox"><br>
<?(!$db_host_sync = ($_POST['host']))?$db_host_sync="localhost":"";?>
<label>IP адрес сервера:</label>
<input type="text" name="host" size="50" value="<?=$db_host_sync?>" class="textbox"><br>
<?(!$db_user_sync= ($_POST['user']))?$db_user_sync="db_osis":"";?>
<label>Пользователь:</label>
<input type="text" name="user" size="50" value="<?=$db_user_sync?>" class="textbox"><br>
<?(!$db_pass_sync= ($_POST['pass']))?"":"";?>
<label>Пароль:</label>
<input type="text" name="pass" size="50" value="<?=$db_pass_sync?>" class="textbox"><br>
<br>Информация о двух купленных на предыдущем шаге товарах:<br>
<?(!$order_id= ($_POST['order_id']))?"":"";?>
<label><b>Номера заказов</b> в Вашей системе, через запятую <small>(Например <b>1009,1010</b>)</small>:</label><br>
<input type="text" name="order_id" size="50" value="<?=$order_id?>" class="textbox"><br>
<?(!$order_time= ($_POST['order_time']))?"":"";?>
<label><b>Время заказов</b> в Вашей системе, через запятую <small>(Например <b>14:27:00,14:30:29</b>)</small>, без даты:</label><br>
<input type="text" name="order_time" size="50" value="<?=$order_time?>" class="textbox"><br>
<?(!$code_delimeter= ($_POST['code_delimeter']))?$code_delimeter="":"";?>
<label><b>Разделитель кода товара</b> <small>(Например <b>[]</b>, для "[X410-030UA] MSI MegaBook X410", где код товара X410-030UA. Если указан просто код, то оставьте поле пустым)</small>:</label><br>
<input type="text" name="code_delimeter" size="50" value="<?=$code_delimeter?>" class="textbox"><br>
<?(!$currency_cash= ($_POST['currency_cash']))?"":"";?>
<label><b>Наличный курс доллара</b> в Вашей системе <small>(Например <b>8.11012499999976</b>)</small>:</label><br>
<input type="text" name="currency_cash" size="50" value="<?=$currency_cash?>" class="textbox"><br>
<?(!$currency_cashless= ($_POST['currency_cashless']))?"":"";?>
<label><b>Безналичный курс доллара</b> в Вашей системе <small>(Например <b>8.30249999999976</b>)</small>:</label><br>
<input type="text" name="currency_cashless" size="50" value="<?=$currency_cashless?>" class="textbox"><br>

<?(!$shipping_type=($_POST['shipping_type']))?"":"";?>
<label>Возможные <b>способы доставки </b> в Вашей системе <small>(Например <b>Курьер,Самовывоз</b>). Только те, которые есть при покупке</small>:</label><br>
<input type="text" name="shipping_type" size="50" value="<?=$shipping_type?>" class="textbox"><br>



<input name="Submit" type=submit value="Выполнить скрипт">
</fieldset>
</form>

<?

require_once ('product_codes.php');
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
///
/// Search Code and Price Path
///
if ($store_name AND $db_host_sync AND $db_user_sync AND $db_pass_sync)
{
	$db_con_remote = mysql_connect($db_host_sync, $db_user_sync, $db_pass_sync);
	if (!$db_con_remote)
	{
		die('Could not connect: ' . mysql_error());
	}
	mysql_query ('SET NAMES UTF8');	
	$flag=1;
	/// 
	/// Connecting To All DBs, Except "information_schema"
	/// 
	$query_dbs = "SHOW DATABASES";
	$result_dbs = mysql_query($query_dbs);
	while($row_dbs = mysql_fetch_array( $result_dbs ))
	{
		echo $db=$row_dbs[0];
		if ($db!='information_schema')
		{
			mysql_select_db($db, $db_con_remote);
		}
		else
		{
			continue;
		}
		/// Возможно не нужно, так как может быть несколько тестовых баз под Интернет магазин


		/// 
		/// Searching Field That Contains "code" Values
		/// 
//		$counter_code=0;
		$m=0;
		for ($m=0 ; $m<=4 ; $m++)
		{
			$query_tbls = "SHOW TABLES";
			$result_tbls = mysql_query($query_tbls);
			$i = 0;
			while($row_tbls = mysql_fetch_array( $result_tbls ))
			{
				$query_flds = "SHOW FIELDS FROM `".$row_tbls[0]."`";
				$result_flds = mysql_query($query_flds);
				$j = 0;
				$query = "SELECT * FROM `".$row_tbls[0]."` WHERE ";
				while($row_flds = mysql_fetch_array( $result_flds ))
				{
					$query = $query."`".$row_flds[0]."` LIKE '".$product[$m][0]."'";
					if ($j < (mysql_num_rows ($result_flds) - 1))
					{
						$query = $query." OR ";
					}
					$j++;
				}
				$i++;
//echo $query;
				$result = mysql_query($query);

				if (mysql_num_rows($result) > 0)
				{
					$result_flds = mysql_query($query_flds);
					while($row_flds = mysql_fetch_array( $result_flds ))
					{
						$query_code = "SELECT `".$row_flds[0]."` FROM `".$row_tbls[0]."` WHERE `".$row_flds[0]."` LIKE '".$product[$m][0]."'";
						$result_code = mysql_query($query_code);
						if (mysql_num_rows($result_code) > 0)
						{
							$l = 0;
							while ($l < mysql_num_fields($result_code))
							{
								$meta = mysql_fetch_field($result_code, $l);
								if (!$meta)
								{
									echo "No information available<br />\n";
								}
								$fld[$row_tbls[0]]=$meta->name;
								if ($counter_code[$row_tbls[0]]==0)
								{
									$path_code[$row_tbls[0]]=$row_tbls[0].",".$fld[$row_tbls[0]];
//									$code_db=$db;
//									$code_table=$row_tbls[0];
//									$code_field=$fld;
								}
								if ($path_code[$row_tbls[0]] == $row_tbls[0].",".$fld[$row_tbls[0]])
								{
									$counter_code[$row_tbls[0]]++;
								}
								$l++;
							}
						}
					}
				}
			}
		}

		/// 
		/// Searching Fields That Contains "price" Values
		///
//		$counter_price=0;
		$m=0;
		for ($m=0 ; $m<=4 ; $m++)
		{
			$query_tbls = "SHOW TABLES";
			$result_tbls = mysql_query($query_tbls);
			$i = 0;
			while($row_tbls = mysql_fetch_array( $result_tbls ))
			{
//				$counter_price1[$row_tbls[0]]=0;
				$query_flds = "SHOW FIELDS FROM `".$row_tbls[0]."`";
				$result_flds = mysql_query($query_flds);
				$j = 0;
				$query = "SELECT * FROM `".$row_tbls[0]."` WHERE ";
				while($row_flds = mysql_fetch_array( $result_flds ))
				{
					$query = $query."`".$row_flds[0]."` LIKE '".$product[$m][1]."'";
					if ($j < (mysql_num_rows ($result_flds) - 1))
					{
						$query = $query." OR ";
					}
					$j++;
				}
				$i++;
				$result = mysql_query($query);

				if (mysql_num_rows($result) > 0)
				{
					$result_flds = mysql_query($query_flds);
					while($row_flds = mysql_fetch_array( $result_flds ))
					{
						$query_price = "SELECT `".$row_flds[0]."` FROM `".$row_tbls[0]."` WHERE `".$row_flds[0]."` LIKE '".$product[$m][1]."'";
						$result_price = mysql_query($query_price);
						if (mysql_num_rows($result_price) > 0)
						{
							$l = 0;
							while ($l < mysql_num_fields($result_price))
							{
								$meta = mysql_fetch_field($result_price, $l);
								if (!$meta)
								{
									echo "No information available<br />\n";
								}
								$fld[$row_tbls[0]]=$meta->name;
								if ($counter_price[$row_tbls[0]]==0)
								{
									$path_price[$row_tbls[0]]=$row_tbls[0].",".$fld[$row_tbls[0]];
//									$price_db=$db;
//									$price_table=$row_tbls[0];
//									$price_field=$fld;
								}
								if ($path_price[$row_tbls[0]] == $row_tbls[0].",".$fld[$row_tbls[0]])
								{
								$counter_price[$row_tbls[0]]++;
								}
								$l++;
							}
						}
					}
				}
			}
		}
	}
//	mysql_close($db_con_remote);
//print_r($counter_price1);
//print_r($path_price1);
echo "<br>";
/// Code
arsort($counter_code);
//reset($counter_code);
//echo $path_code[key($counter_code)];

/// Price
arsort($counter_price);
//reset($counter_price);
//echo $path_price[key($counter_price)];


//for (reset($counter_price1); $key = key ($counter_price1); next ($counter_price1))
//{
//	echo $path_price1[$key]."<br>";
//}
//echo "$counter_code<br>";
//echo "$counter_price<br>";

	if ( reset($counter_code) > 3 AND reset($counter_price) > 3 )
	{
//		require_once('../conf/db_vars.php');
//		require_once('../conf/db_connect.php');
		$query_update="UPDATE  `Stores`.`List` SET  `db_host` =  '$db_host_sync', `db_user` =  '$db_user_sync', `db_pass` =  '$db_pass_sync', `db_store` =  '$db', `db_code_path`='{$path_code[key($counter_code)]}', `db_price_path`= '{$path_price[key($counter_price)]}' WHERE  `List`.`name` =  '$store_name'";
		$result_update = mysql_query($query_update, $db_con);
//		require_once ('../conf/db_disconnect.php');
	}
}

///
/// Search Order Path
///
/// Update Prices to Price * Quantity
$product[0][1]*=$product[0][2];
$product[1][1]*=$product[1][2];
$product[0][0]="[{$product[0][0]}]%"; // isspolzovat razdeliteli, kotorie v internet magazine. Etot vigliadit tak: [123098QAZ] Description text ...
$product[1][0]="[{$product[1][0]}]%";

/*
/// Update Shipping texts
$shippingTypeArray = explode(',',$shipping_type);
$product[0][9]=$shippingTypeArray[0];
$product[1][9]=$shippingTypeArray[1];
*/
/// Data for Currency Rate's
$currency_counter=sizeof($product[0]);
$product[0][sizeof($product[0])]="$currency_cash";
$product[1][sizeof($product[1])]="$currency_cash";
$product[0][sizeof($product[0])]="$currency_cashless";
$product[1][sizeof($product[1])]="$currency_cashless";

for ($m=0 ; $m<=1 ; $m++)
	for ($s=0 ; $s<sizeof($product[$m]) ; $s++)
		$counter[$m][$s]=0;


$k=0;
if ($store_name AND $db_host_sync AND $db_user_sync AND $db_pass_sync AND $order_id AND $order_time)
{
//	$db_con_remote = mysql_connect($db_host_sync, $db_user_sync, $db_pass_sync);
//	if (!$db_con_remote)
//	{
//		die('Could not connect: ' . mysql_error());
//	}
	mysql_query ('SET NAMES UTF8');	
	$query_dbs = "SHOW DATABASES";
	$result_dbs = mysql_query($query_dbs);
	while($row_dbs = mysql_fetch_array( $result_dbs ))
	{
		$db=$row_dbs[0];
		if ($db!='information_schema')
			mysql_select_db($db, $db_con_remote);
		else
			continue;


		/// 
		/// Searching Field That Contains "code" Values
		/// 
		$counter_code=0;
		for ($m=0 ; $m<=1 ; $m++)
			for ($s=0 ; $s<sizeof($product[$m]) ; $s++)
			{
			$query_tbls = "SHOW TABLES";
			$result_tbls = mysql_query($query_tbls);
			$i = 0;
			while($row_tbls = mysql_fetch_array( $result_tbls ))
			{
				$query_flds = "SHOW FIELDS FROM `".$row_tbls[0]."`";
				$result_flds = mysql_query($query_flds);
				$j = 0;
				$query = "SELECT * FROM `".$row_tbls[0]."` WHERE ";
				while($row_flds = mysql_fetch_array( $result_flds ))
				{
					$query .= "`".$row_flds[0]."` LIKE '".$product[$m][$s]."'";
					if ($j < (mysql_num_rows ($result_flds) - 1))
						$query .= " OR ";
					$j++;
				}
				$i++;
				$result = mysql_query($query);
				if (mysql_num_rows($result) > 0)
				{
					$result_flds = mysql_query($query_flds);
					while($row_flds = mysql_fetch_array( $result_flds ))
					{
						$query_code = "SELECT `".$row_flds[0]."` FROM `".$row_tbls[0]."` WHERE `".$row_flds[0]."` LIKE '".$product[$m][$s]."'";
						$result_code = mysql_query($query_code);
						if (mysql_num_rows($result_code) > 0)
						{
							$l = 0;
							while ($l < mysql_num_fields($result_code))
							{
								$meta = mysql_fetch_field($result_code, $l);
								if (!$meta)
									echo "No information available<br />\n";
								$fld=$meta->name;
								$path_field[$m][$s][($counter[$m][$s])]=$fld;
								$path[$m][$s][($counter[$m][$s])]="$db,{$row_tbls[0]}";
								$path_tbl[$m][$s][($counter[$m][$s])]=$row_tbls[0];
								$l++;
								$counter[$m][$s]++;
							}
						}
					}
				}
			}
	}
}
	

$l=0;
for ($m=0 ; $m<=1 ; $m++)
	for ($s=0 ; $s<sizeof($product[$m]) ; $s++)
	{
		for ($i=0; $i<$counter[$m][$s];$i++)
		{
			$path_array[$l++]=$path[$m][$s][$i];
		}
	}
$array=array_count_values($path_array);
arsort($array);

$i=$k=$n=0;
for (reset($array); $key = key ($array); next ($array))
{
	$array_sorted[$i][0]=$key;
	$array_sorted[$i++][1]=$array[$key];
}
//print_r($array_sorted);
$row_arr=array();
$tmp_arr=array();
$where="";
for($l=0 ; $l<sizeof($array_sorted) ; $l++)
	for ($m=0 ; $m<=1 ; $m++)
		for ($s=0 ; $s<sizeof($product[$m]) ; $s++)
			for ($i=0; $i<$counter[$m][$s];$i++)
				if($path[$m][$s][$i]==$array_sorted[$l][0] && $db_path[$s]=="")
				{
					$db_path[$s]="{$path_tbl[$m][$s][$i]},{$path_field[$m][$s][$i]}";
					$links[$s]=$path_tbl[$m][$s][$i];
					if (in_array($links[$s],$tmp_arr)!=true)
					{
						$n=0;						
						array_push($tmp_arr,$links[$s]);
						$query_flds = "SHOW FIELDS FROM `".$links[$s]."`";
						$result_flds = mysql_query($query_flds);
						while($row_flds = mysql_fetch_array($result_flds))
						{
							$orderIdArray = explode(',',$order_id);
							for($h=0; $h<sizeof($orderIdArray); $h++)
							{
								for($f=0;$f<strlen($orderIdArray[$h]);$f++)
								{
									$order[$h]="";
									for ($j=$f;$j<strlen($orderIdArray[$h]);$j++)
										$order[$h].=$orderIdArray[$h][$j]; /// Zamenit na substr // http://php.net/manual/en/function.substr.php
									$select_order_id="SELECT {$row_flds[0]} FROM {$links[$s]} WHERE {$row_flds[0]}='{$order[$h]}' AND {$path_field[$m][$s][$i]} LIKE '{$product[$m][$s]}'";
									$result_order_id = mysql_query($select_order_id);
									if (mysql_num_rows($result_order_id)>0)
										if(strlen($order[$h])>$orderIdFieldLenght)
										{
											$orderIdFieldLenght=strlen($order[$h]);
											$orderIdFieldPath="{$links[$s]},{$row_flds[0]}";
										}
								}
							}


							$orderTimeArray = explode(',',$order_time);
							for($h=0; $h<sizeof($orderTimeArray); $h++)
							{
									$select_order_time="SELECT {$row_flds[0]} FROM {$links[$s]} WHERE {$row_flds[0]} LIKE '%{$orderTimeArray[$h]}%' AND {$path_field[$m][$s][$i]} LIKE '{$product[$m][$s]}'";
									$result_order_time = mysql_query($select_order_time);
									if (mysql_num_rows($result_order_time)>0)
										if(strlen($order[$h])>$orderTimeFieldLenght)
										{
											$orderTimeFieldLenght=strlen($order[$h]);
											$orderTimeFieldPath="{$links[$s]},{$row_flds[0]}";
										}
							}

							$row_arr_tbl[$k][$n]=$links[$s];
							$row_arr[$k][$n]=$row_flds[0];
							for($a=0;$a<=$k;$a++)
								for($b=0;$b<sizeof($row_arr[$a])-1;$b++)
									if($row_arr[$k][$n]==$row_arr[$a][$b])
									{
										if($where!="")
											$where.=" AND ";
										$where.="{$links[$s]}.{$row_arr[$k][$n]} = {$row_arr_tbl[$a][$b]}.{$row_arr[$k][$n]}";
									}
							$n++;
						}
						$k++;
					}

				}
/// Currency Rate ID search
$currencyArr = explode(',',$db_path[$currency_counter]);
$result_cash = mysql_query("SELECT * FROM {$currencyArr[0]} WHERE {$currencyArr[1]} LIKE $currency_cash");
//$result_cashless = mysql_query("SELECT * FROM {$currencyArr[0]} WHERE {$currencyArr[1]} LIKE $currency_cashless");
$l = 0;
while ($l < mysql_num_fields($result_cash))
{
	$meta = mysql_fetch_field($result_cash, $l);
	$validation_pattern="/(id)|(ID)/";
	if (preg_match($validation_pattern, $meta->name))
		$fld=$meta->name;
	$l++;
}
$result_cash = mysql_query("SELECT $fld FROM {$currencyArr[0]} WHERE {$currencyArr[1]} LIKE $currency_cash");
$result_cashless = mysql_query("SELECT $fld FROM {$currencyArr[0]} WHERE {$currencyArr[1]} LIKE $currency_cashless");
$cashID=mysql_result($result_cash,0,$fld);
$cashlessID=mysql_result($result_cashless,0,$fld);
$cash_rate_id_field=$fld;
///

mysql_close($db_con_remote);

//require_once('../conf/db_vars.php');
//require_once('../conf/db_connect.php');
$i=0;
$query_update_orders="UPDATE  `Stores`.`List` SET  `db_order_id_path`='$orderIdFieldPath', `db_order_time_path`='$orderTimeFieldPath', `db_order_code_path`='{$db_path[$i++]}', `code_delimeter`='$code_delimeter', `db_order_cost_path`='{$db_path[$i++]}', `db_order_quantity_path`='{$db_path[$i++]}', `db_order_first_name_path`='{$db_path[$i++]}', `db_order_last_name_path`='{$db_path[$i++]}', `db_order_phone_path`='{$db_path[$i++]}', `db_order_email_path`='{$db_path[$i++]}', `db_order_town_path`='{$db_path[$i++]}', `db_order_address_path`='{$db_path[$i++]}', `db_order_shipping_type_path`='{$db_path[$i++]}', `db_order_note_path`= '{$db_path[$i++]}', `db_order_reference`='$where', `shippingType`='$shipping_type' WHERE  `List`.`name` =  '$store_name'";
$result_update_orders = mysql_query($query_update_orders, $db_con);
$query_update_rates="UPDATE  `Stores`.`List` SET  `cash_rate_id_field`='$cash_rate_id_field', `db_rate_path`='{$db_path[$i++]}', `cash_rate_id`='$cashID', `cashless_rate_id`='$cashlessID' WHERE  `List`.`name` =  '$store_name'";
$result_update_rates = mysql_query($query_update_rates, $db_con);
if (!$result_update_rates)
	die('Invalid query: ' . mysql_error());
//require_once ('../conf/db_disconnect.php');
}
require_once ('../conf/db_disconnect.php');
?>
</body>
</html>
