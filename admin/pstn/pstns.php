<?
///Initializing variables
require_once 'inc_config.php';
?>

<form method="post" action="<?=$detail_url?>action=blank" id="form_add" name="form_add"></form>
<input class="buttonShortAdmin" value="Add" type="submit" onClick="javascript:document.form_add.submit()"><br><br>

<form action="<?=$detail_url?>" method="post" name="form" id="form">
<table id='rounded-corner'>
<thead><tr>
<?
for ( $i = 0; $i < count($fields_sample_array); $i++ )
{
	if ($i==0)
		echo "<th class='rounded-first'>$fields_sample_array[$i]</th>";
	if ($i==count($fields_sample_array)-1)
		echo "<th class='rounded-last'>$fields_sample_array[$i]</th>";
	if (($i!=count($fields_sample_array)-1) && ($i!=0))
		echo "<th>$fields_sample_array[$i]</th>";
}
?>
</tr>
</thead>
<?
//require_once('../../conf/db_vars.php');
//require_once('../../conf/db_connect.php');
mysql_select_db($db_DB, $db_con);

include ("inc_generated_vars.php"); /// After DB connect and select

$result = mysql_query("SELECT $fields_sample FROM `$table`");

$pstn_cols_num=2;
echo "<tfoot><tr>";
echo "<td colspan='$pstn_cols_num' class='rounded-foot'><em><b>Numbers Quantity: ".mysql_num_rows($result)."</b></em></td>";
echo "</tr></tfoot>";
echo "<tbody>";
$u=0;
while($row = mysql_fetch_array($result))
{
	($u % 2)?$class="odd":$class="even";
	echo "<tr>";
	for ( $i = 0; $i < count($fields_sample_array); $i++ )
	{
		if ($i==0)
		{
			echo "<td class='$class'><a href=$detail_url$main_field=$row[$i]&action=view>$row[$i]</a></td>";
		}
		else
		{
			echo "<td class='$class'>$row[$i]</td>";
		}
	}
	echo "</tr>";
	$u++;
}
echo "</tbody>";
//require_once('../../conf/db_disconnect.php');
?>
</table>

</form>

<input class="buttonShortAdmin" value="Add" type="submit" onClick="javascript:document.form_add.submit()">

