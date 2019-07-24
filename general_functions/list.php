<html>
<body>
<?
///Initializing variables
include ("inc_config.php");
?>

<form action="<?=$detail_url?>" method="post" name="form" id="form">
<table border="1">
<tr>
<?
for ( $i = 0; $i < count($fields_sample_array); $i++ )
{
	echo "<td>$fields_sample_array[$i]</td>";
}
?>
</tr>


<?
require_once('../conf/db_vars_asterisk.php');
require_once('../conf/db_connect.php');
mysql_select_db($db_DB, $db_con);
include ("inc_generated_vars.php"); /// After DB connect and select

$result = mysql_query("SELECT $fields_sample FROM `$table`");

while($row = mysql_fetch_array($result))
{
	echo "<tr>";
	for ( $i = 0; $i < count($fields_sample_array); $i++ )
	{
		if ($i==0)
		{
			echo "<td><a href=$detail_url?$main_field=$row[$i]&action=view>$row[$i]</a></td>";
		}
		else
		{
			echo "<td>$row[$i]</td>";
		}
	}
	echo "</tr>";
}

require_once ('../conf/db_disconnect.php');
?>

</table>

<a href=<?=$detail_url?>?action=blank>Add</a>

</form>




</body>
</html>
