<?
function file_del($field)
{
	global $table;
	global $main_field;
	global $path;
	global $file;
	global $file_conf;
	global $path_for_include;
	global $db_operators;
	// Delete config file
	unlink ($path.$file_conf.$field)?:"Error on file delete";

	/// Update include file
	// Generate new "include" links
	$result = mysql_query("SELECT `$main_field` FROM $db_operators.$table");
	while($row = mysql_fetch_array($result))
	{
		$data=$data."#include $path_for_include$file_conf$row[0]\n";
	}
	//Open file includes for write. Create if not exist
	$f = fopen($path.$file, 'w') or die ("can't open file");
	(fwrite($f,$data))?:"Error on file create";
	// Close file
	fclose($f)?:"Error on file close";
}

function file_create($field, $field_unique, $file, $file_conf)
{
//	global $column_names;
	global $table;
	global $main_field;
	global $path;
	global $db_operators;
	global $path_for_include;
//	global $db_stores;
//	global $file;
//	global $file_conf;

	/// Update include file
	// Generate new "include" links
//	echo "SELECT `$field_unique` FROM $db_operators.$table";
	$result = mysql_query("SELECT `$field_unique` FROM $db_operators.$table WHERE userlevel!='2' AND consultant!='1'");
	while($row = mysql_fetch_array($result))
	{
		$data=$data."#include $path_for_include$file_conf$row[0]\n";
	}
	//Open file includes for write. Create if not exist
	$f = fopen($path.$file, 'w') or die ("can't open file");
	(fwrite($f,$data))?:"Error on file create";
	// Close file
	fclose($f)?:"Error on file close";

	/// Create config file
//echo "SELECT * FROM $db_operators.$table WHERE `$main_field` LIKE '$field'";
	$result_config = mysql_query("SELECT * FROM $db_operators.$table WHERE `$main_field` LIKE '$field'");
	$row_config = mysql_fetch_array($result_config);

	$data_config=generate_phones_from_template($row_config);

	//Open file config for write. Create if not exist
	$fc = fopen($path.$file_conf.$field, 'w') or die ("can't open file");
	(fwrite($fc,$data_config))?:"Error on file create";
	// Close file
	fclose($fc)?:"Error on file close";

}

///
/// Fuction For Generate Phone Configuration
///
function generate_phones_from_template($row_config)
{
	global $template_phones_file;
	global $template_path;

	/// Read template file
	$ft = fopen($template_path.$template_phones_file, 'r') or die ("can't open file");
	$text = fread($ft,filesize($template_path.$template_phones_file));
//	$text = str_replace("%%IP%%", $row['IP'], $text);
//	$text = str_replace("%%PhoneLineNumber%%", $row['LineNumber'], $text);
	$text = str_replace("%%SIPUserID%%", $row_config['phoneNumber'], $text);
//	$text = str_replace("%%PSTNNumber%%", $row['CityNumber'], $text);
	$text = str_replace("%%FullUserName%%", $row_config['full_name'], $text);
	$text = str_replace("%%SIPPass%%", $row_config['password'], $text);
//	$text = str_replace("%%CallLimit%%", $row_config['call_limit'], $text);
//	$text = str_replace("%%VMBox%%", $row_config['mailbox'], $text);
//	$text = str_replace("%%VMPass%%", $row['VoIPPass'], $text);
	return $text;
}

/*
///
/// Fuction For Generate Queues Configuration
///
function generate_queues_from_template($row_config)
{
	global $template_queues_file;
	global $template_path;

	/// Read template file
	$ft = fopen($template_path.$template_queues_file, 'r') or die ("can't open file");
	$text = fread($ft,filesize($template_path.$template_queues_file));
	$text = str_replace("%%STORENAME%%", $row_config['storename'], $text);
	$text = str_replace("%%OPERATOR%%", $row_config['full_name'], $text);
	return $text;
}
*/
?>
