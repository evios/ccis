<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
if (isset($_POST['store']))
{
	$url=$_POST['url'];
	$address=$_POST['address'];
	if($_POST['self_delivery']=="on")
		$self_delivery=1;
	else
		$self_delivery=0;
	$dbHost=$_POST['dbHost'];
	$dbUser=$_POST['dbUser'];
	$dbPass=$_POST['dbPass'];
	$lineID=$_POST['lineID'];
	$storeEmail=$_POST['storeEmail'];
	/// Update Distributor selects
	$query_distributors="SELECT `name` FROM Distributors.List";
	$result_distributors=mysql_query($query_distributors);
	$distrs="";
	while($row_distributors = mysql_fetch_array( $result_distributors ))
	{

		$distributor=$row_distributors['name'];
		($distrs!="" && $_POST[$distributor]=='on')?$distrs.=",":"";
		if(isset($_POST[$distributor]) && $_POST[$distributor]=='on')
		{
			$distrs.="$distributor={$_POST[$distributor.'_Priority']}={$_POST[$distributor.'_Email']}={$_POST[$distributor.'_Contact']}";
		}
	}
	/// Update Graphics selects
	$query_graphics="SELECT name,id FROM Stores.Graphics";
	$result_graphics=mysql_query($query_graphics);
	$graphs="";
	while($row_graphics = mysql_fetch_array( $result_graphics ))
	{

		$graphic=$row_graphics['name'];
		$graphic_id=$row_graphics['id'];
		($graphs!="" && $_POST[$graphic]=='on')?$graphs.=",":"";
		if(isset($_POST[$graphic]) && $_POST[$graphic]=='on')
		{
			$graphs.="$graphic_id";
		}
	}
	if ($graphs=="")
		$graphs=0;

	/// Update all values
	$query_get_previous_lineID=mysql_query("SELECT lineID FROM Stores.List WHERE `name` LIKE '$store' LIMIT 1");
	$previousLineID=mysql_result($query_get_previous_lineID,0,'lineID');
	$query_unset_previous_pstn=mysql_query("UPDATE Operators.PSTN SET state='0' WHERE pstnNumber LIKE '$previousLineID'");
	$query_set_pstn=mysql_query("UPDATE Operators.PSTN SET state='1' WHERE pstnNumber LIKE '$lineID'");

	$query_set="UPDATE Stores.List SET `url`='$url', `address`='$address', `self_delivery`='$self_delivery', `db_host`='$dbHost', `db_user`='$dbUser', `db_pass`='$dbPass', `email`='$storeEmail', `distributors`='$distrs', graphics='$graphs', lineID='$lineID'  WHERE  `List`.`name` =  '$store'";
	$result_set=mysql_query($query_set);
	require_once ("../admin/stores/inc_config.php");
	require_once ("../admin/stores/inc_files.php");
	file_create_extensions($store, '../admin/configs/', '../admin/templates/');
}


$query_get="SELECT * FROM Stores.List WHERE `name` LIKE '$store' LIMIT 1";
$result_get=mysql_query($query_get);
$url=mysql_result($result_get,0,'url');
$address=mysql_result($result_get,0,'address');
$self_delivery=mysql_result($result_get,0,'self_delivery');
$dbHost=mysql_result($result_get,0,'db_host');
$dbUser=mysql_result($result_get,0,'db_user');
$dbPass=mysql_result($result_get,0,'db_pass');
$storeEmail=mysql_result($result_get,0,'email');
$lineID=mysql_result($result_get,0,'lineID');
$price_update_date=mysql_result($result_get,0,'price_update_date');
$orders_update_date=mysql_result($result_get,0,'orders_update_date');
$pstnNumber_get=mysql_query("SELECT pstnNumber FROM Operators.PSTN WHERE `state`='0'"); /// state=0 - number is not in use (FREE)
?>
<form action="" method="POST" name="frm" id="frm">
	<input type="hidden" name="view" value="<?=$_POST['view']?>"/>
	<input type="hidden" name="store" value="<?=$store?>"/>
	<br><br>
	<?=$text_preferences_store_name?>: <b><?=$store?></b><br><br>
	<?=$text_preferences_store_site?>
	<input type="text" name="url" value="<?=$url?>"/><br>
	<?=$text_preferences_store_email?>
	<input type="text" name="storeEmail" value="<?=$storeEmail?>"/><br>
	<?=$text_preferences_store_address?>
	<input type="text" name="address" value="<?=$address?>"/><br>
	<?=$text_preferences_self_delivery_option?>
	<input type=checkbox name="self_delivery" <?if($self_delivery==1)echo " checked";?>><br>
	<?=$text_preferences_sla?>
	<select name="sla" onChange="frm.submit();">
		<option value="sla" >sla</option>
	</select><br>
	<?=$text_preferences_phone_number?>
	<select name="lineID" onChange="frm.submit();">
		<?
		if ($lineID!=null)
			echo "<option value='$lineID' SELECTED>$lineID</option>";
		while($row_pstnnum = mysql_fetch_array($pstnNumber_get))
		{
			echo "<option value='{$row_pstnnum['pstnNumber']}' ";
			if($_POST['phoneLine']==$row_pstnnum['pstnNumber'])
				echo "SELECTED";
			echo ">{$row_pstnnum['pstnNumber']}</option>";
		}
		?>
	</select><br><br>
	<?=$text_preferences_db_host?>
	<input type="text" name="dbHost" value="<?=$dbHost?>"/><br>
	<?=$text_preferences_db_user?>
	<input type="text" name="dbUser" value="<?=$dbUser?>"/><br>
	<?=$text_preferences_db_pass?>
	<input type="password" name="dbPass" value="<?=$dbPass?>"/><br>
	<br>
	<table border="0" id=\"resultTable\">
		<thead>
			<tr>
				<th><?=$text_preferences_distributor_name?></th>
				<th><?=$text_preferences_distributor_priority?></th>
				<th><?=$text_preferences_distributor_email?></th>
				<th><?=$text_preferences_distributor_contact?></th>
				<th><?=$text_preferences_distributor_sync_time?></th>
			</tr>
		</thead>
		<tbody>
<?
require_once('../general_functions/multiexplode.php');
$delimeters=Array(",","=");
$query_get_distrs="SELECT `distributors` FROM Stores.List WHERE `name` LIKE '$store' LIMIT 1";
$store_get_distrs=mysql_query($query_get_distrs);
$distrs=multiexplode($delimeters,mysql_result($store_get_distrs,0,'distributors'));
for($i=0;$i<sizeof($distrs);$i++)
{
	if ($distrs[$i][0]!="")
	{
		$distributor=$distrs[$i][0];
		$active_distrs.=" AND `name` NOT LIKE '$distributor'";
		$distributor_priority=$distrs[$i][1];
		$distributor_email=$distrs[$i][2];
		$distributor_contact=$distrs[$i][3];
		echo "<tr>";
		echo "<td align=\"center\"><input type=checkbox name=\"$distributor\" checked>$distributor</td>";
		echo "<td align=\"center\"><input type=\"text\" name=\"".$distributor."_Priority\" value=\"$distributor_priority\"/></td>";
		echo "<td align=\"center\"><input type=\"text\" name=\"".$distributor."_Email\" value=\"$distributor_email\"/></td>";
		echo "<td align=\"center\"><input type=\"text\" name=\"".$distributor."_Contact\" value=\"$distributor_contact\"/></td>";
		$distributor_update_date=mysql_result(mysql_query("SELECT `update_date` FROM Distributors.List WHERE `name` LIKE '$distributor'"),0,'update_date');
		echo "<td align=\"center\"><b>$distributor_update_date</b></td>";
		echo "</tr>";
	}
}
$distributor=$distributor_priority=$distributor_email=$distributor_contact="";
$query_distributors="SELECT `name` FROM Distributors.List WHERE 1".$active_distrs;
$result_distributors=mysql_query($query_distributors);
while($row_distributors = mysql_fetch_array( $result_distributors ))
{
	$distributor=$row_distributors['name'];
	echo "<tr>";
	echo "<td align=\"center\"><input type=checkbox name=\"$distributor\">$distributor</td>";
	echo "<td align=\"center\"><input type=\"text\" name=\"".$distributor."_Priority\" value=\"$distributor_priority\"/></td>";
	echo "<td align=\"center\"><input type=\"text\" name=\"".$distributor."_Email\" value=\"$distributor_email\"/></td>";
	echo "<td align=\"center\"><input type=\"text\" name=\"".$distributor."_Contact\" value=\"$distributor_contact\"/></td>";
	echo "</tr>";
}
echo "</tbody></table>";


echo "<br>";
echo "<table border=\"0\" id=\"Graphics\">";
echo "<thead><tr><th align=\"left\">Graphics</th></tr></thead><tbody>";
$delimeters=Array(",");
$query_get_graphs="SELECT `graphics` FROM Stores.List WHERE `name` LIKE '$store' LIMIT 1";
$store_get_graphs=mysql_query($query_get_graphs);
$graphs=multiexplode($delimeters,mysql_result($store_get_graphs,0,'graphics'));
for($i=0;$i<sizeof($graphs);$i++)
{
	if ($graphs[$i]!="" && $graphs[$i]!=0) /// Esli ne perviy zaxod i vibran xot odin grafik
	{
		$graphic_id=$graphs[$i];
	 	$graphic_description=mysql_result(mysql_query("SELECT $db_graphics_language FROM Stores.Graphics WHERE id='$graphic_id' LIMIT 1"),0,$db_graphics_language);
	 	$graphic_name=mysql_result(mysql_query("SELECT name FROM Stores.Graphics WHERE id='$graphic_id' LIMIT 1"),0,'name');
		$active_graphs.=" AND `id` NOT LIKE '$graphic_id'";
		echo "<tr>";
		echo "<td align=\"left\"><input type=checkbox name=\"$graphic_name\" checked>$graphic_description</td>";
		echo "</tr>";
	}
	if($graphs[$i]=="") /// Pri pervom vxode vse grafiki vibranni
	{
		$query_graphs="SELECT * FROM Stores.Graphics WHERE 1";
		$result_graphs=mysql_query($query_graphs);
		while($row_graphs = mysql_fetch_array( $result_graphs ))
		{
			$graphic_id=$row_graphs['id'];
			$graphic_name=$row_graphs['name'];
			$graphic_description=$row_graphs['description_english'];
			$active_graphs.=" AND `id` NOT LIKE '$graphic_id'";
			echo "<tr>";
			echo "<td align=\"left\"><input type=checkbox name=\"$graphic_name\" checked>$graphic_description</td>";
			echo "</tr>";
		}
	}
}
$graphic="";
$query_graphs="SELECT * FROM Stores.Graphics WHERE 1".$active_graphs;
$result_graphs=mysql_query($query_graphs);
while($row_graphs = mysql_fetch_array( $result_graphs ))
{
//	$graphic_id=$row_graphs['id'];
	$graphic_name=$row_graphs['name'];
	$graphic_description=$row_graphs['description_english'];
	echo "<tr>";
	echo "<td align=\"left\"><input type=checkbox name=\"$graphic_name\">$graphic_description</td>";
	echo "</tr>";
}
echo "</tbody></table>";




?>

	<br>
	<?=$text_preferences_welcome_file?>
	<input type="text" name="welcomeFile"/><br><br>
	<?=$text_preferences_price_sync_time?>: <b><?=$price_update_date?></b><br>
	<?=$text_preferences_orders_sync_time?>: <b><?=$orders_update_date?></b><br>
</form>
<input type="submit" name="submit" onClick="frm.submit();"/><br><br>
<a href="../sync_store/"><?=$text_preferences_sync_script?></a><br>

<?
require_once ('../conf/db_disconnect.php');
?>
