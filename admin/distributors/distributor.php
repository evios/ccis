<html>
<body>
<input class="buttonShortAdmin" value="Save" type="submit" onClick="javascript:document.form_save.submit()">
<input class="buttonShortAdmin" value="Delete" type="submit" onClick="javascript:document.form_delete.submit()">
<input class="buttonShortAdmin" value="Back" type="submit" onClick="javascript:document.form_back.submit()">

<form action="" method="get" name="form_save" id="form_save">
<?
///Initializing variables
include ("inc_config.php");
//include ("inc_files.php");

//require_once('../../conf/db_vars.php');
//require_once('../../conf/db_connect.php');
mysql_select_db($db_DB, $db_con);
include ("inc_generated_vars.php"); /// After DB connect and select

/*function field_validation($field)
{
	global $validation_pattern;
	if ((!preg_match($validation_pattern, $field)) && (strlen($field)!=$validation_string_length))
	{
		echo $error_validation;
	}
	else
	{
		return "valid";
	}
}
*/
function view($field)
{
	global $column_names;
	global $table;
	global $main_field;
	/// View Phone
	$result_view = mysql_query("SELECT * FROM `$table` WHERE `$main_field` LIKE '$field'");
	$row_view = mysql_fetch_array($result_view);
	echo "<table id='rounded-corner'><tbody>";
	for ( $i = 0; $i < count($column_names); $i++ )
	{
		if($i==0)
		{
			echo "<tr><td><b>$column_names[$i]:</b></td>";			
			echo "<td><b>$row_view[$i]</b></td></tr>";
			echo "<input type='hidden' name='$column_names[$i]' value='$row_view[$i]' />";
			continue;
		}
		echo "<tr><td><b>$column_names[$i]:</b></td>";
		echo "<td><input class='rounded-textbox-search' size =\"80\" type='text' name='$column_names[$i]' value='$row_view[$i]' /></td></tr>";
	}
	echo "</tbody></table>";
	echo "<input type='hidden' name='action' value='update'/>";
}

function update($field, $update_values)
{
	global $main_field;
	global $table;
	/// Update Phone
	$result_update = mysql_query("UPDATE  `".$table."` SET $update_values WHERE `$main_field` =  '$field'");
}

function blank()
{
	global $column_names;
	/// Blank Fields For First Add Phone
	for ( $i = 0; $i < count($column_names); $i++ )
	{
		echo "$column_names[$i]:<input class='rounded-textbox-search' size =\"80\" type='text' name='$column_names[$i]' /><br>";
	}
	echo "<input type='hidden' name='action' value='add'/>";
}

function add($field, $add_values)
{
	global $main_field;
	global $table;
	global $error_add_message;
	/// Search for Identical MAC (If True, Then Error)
	$query = "SELECT `$main_field` FROM `$table` WHERE `$main_field` LIKE '$field'";
	if (mysql_num_rows(mysql_query($query)) > 0)
	{
		echo $error_add_message;
		return "err_add";
	}
	else
	{
		/// Add Phone
		$result_add = mysql_query("INSERT INTO `$table` VALUES ($add_values)");
	}
}

function delete ($field)
{
	global $main_field;
	global $table;
	/// Delete Phone
	$result_delete = mysql_query("DELETE FROM `$table` WHERE `$main_field` = '$field'");
}

if ($action=$_GET['action'])
{
	switch ($action)
	{
		case 'view':
			view($_GET[$main_field]);
		break;
		case 'update':
//			if (field_validation($_GET[$main_field])=="valid")
//			{
				for ( $i = 0; $i < count($column_names); $i++ )
				{
					if ((count($column_names)-$i)==1)
					{
						$update_values= $update_values."`$column_names[$i]`= '".$_GET[$column_names[$i]]."' ";
					}
					else
					{
						$update_values= $update_values."`$column_names[$i]`= '".$_GET[$column_names[$i]]."', ";
					}
				}
				update($_GET[$main_field], $update_values);
//				file_create($_GET[$main_field], $field_phone, $file_phones, $file_conf_phone);
//				file_create($_GET[$main_field], $field_extension, $file_extensions, $file_conf_extension);
				view($_GET[$main_field]);
//			}
		break;
		case 'add':
//			if (field_validation($_GET[$main_field])=="valid")
//			{
				for ( $i = 0; $i < count($column_names); $i++ )
				{
					if ((count($column_names)-$i)==1)
					{
						$add_values= $add_values."'".$_GET[$column_names[$i]]."' ";
					}
					else
					{
						$add_values= $add_values."'".$_GET[$column_names[$i]]."', ";
					}
				}				
				(add($_GET[$main_field], $add_values)=="err_add")?blank():view($_GET[$main_field]);
//				file_create($_GET[$main_field], $field_phone, $file_phones, $file_conf_phone);
//				file_create($_GET[$main_field], $field_extension, $file_extensions, $file_conf_extension);
//			}
		break;
		case 'blank':
			blank();			
		break;
		case 'delete':
			delete($_GET[$main_field]);
//			file_del($_GET[$main_field]);
			break;
		default:
			echo $error_case_default;
	}
}
//require_once('../../conf/db_disconnect.php');
?>



<?

?>
<input type="hidden" name="view" value="<?=$detail_view?>"/>
</form>

<form action="" method="get" name="form_delete" id="form_delete">
<input type="hidden" name="<?=$main_field?>" value="<?=$_GET[$main_field]?>"/>
<input type="hidden" name="action" value="delete"/>
<input type="hidden" name="view" value="<?=$detail_view?>"/>
</form>

<form action="" method="get" name="form_back" id="form_back">
<input type="hidden" name="view" value="<?=$sample_view?>"/>
</form>

<input class="buttonShortAdmin" value="Save" type="submit" onClick="javascript:document.form_save.submit()">
<input class="buttonShortAdmin" value="Delete" type="submit" onClick="javascript:document.form_delete.submit()">
<input class="buttonShortAdmin" value="Back" type="submit" onClick="javascript:document.form_back.submit()">

</body>
</html>
