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
	$result = mysql_query("SELECT `$main_field` FROM $db_stores.$table");
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
	global $column_names;
	global $table;
	global $main_field;
	global $path;
	global $db_operators;
	global $table_operators;
	global $db_stores;
	global $path_for_include;
	global $template_path;
	global $file_conf_extension;
//	global $file;
//	global $file_conf;

	/// Update include file
	// Generate new "include" links
	$result = mysql_query("SELECT $main_field FROM $db_stores.$table");
	while($row = mysql_fetch_array($result))
	{
		$data_queues.="#include $path_for_include$file_conf$row[0]\n";
	}
	//Open file includes for write. Create if not exist
	$f = fopen($path.$file, 'w') or die ("can't open file");
	(fwrite($f,$data_queues))?:"Error on file create";
	// Close file
	fclose($f)?:"Error on file close";

	/// Create config file
	$result_config = mysql_query("SELECT $main_field FROM $db_stores.$table WHERE `$main_field` LIKE '$field'");
	$row_config = mysql_fetch_array($result_config);

	$data_config.=generate_queues_from_template($row_config);

	$result_operators = mysql_query("SELECT * FROM $db_operators.$table_operators WHERE userlevel='0'");
	//$row_operators = mysql_fetch_array($result_operators);
	while($row_operator = mysql_fetch_array($result_operators))
	{
		$data_config.= generate_queues_operators_from_template($row_config, $row_operator['login']);
		$data_config_ext_oper.= generate_extensions_operators_from_template($row_config, $row_operator['login']);
	}
	//Open file config for write. Create if not exist
	$fc = fopen($path.$file_conf.$field, 'w') or die ("can't open file");
	(fwrite($fc,$data_config))?:"Error on file create";
	// Close file
	fclose($fc)?:"Error on file close";

	/// Create Extension Files
	//Open file config for write. Create if not exist
	$fcext = fopen($path.$file_conf_extension.$field, 'w') or die ("can't open file");
	(fwrite($fcext,$data_config_ext_oper))?:"Error on file create";
	// Close file
	fclose($fcext)?:"Error on file close";
	file_create_extensions($field, $path, $template_path);
}

function file_create_extensions($field, $path, $template_path)
{
//echo $field;
//	global $column_names;
	global $table;
	global $main_field;
//	global $path;
//	global $db_operators;
//	global $table_operators;
	global $db_stores;
	global $file_extensions;
	global $file_conf_extension;
	global $path_for_include;
	global $pstnNumber_field;
//	global $file;
//	global $file_conf;

	/// Update include file
	// Generate new "include" links
	$result = mysql_query("SELECT $main_field FROM $db_stores.$table");
	while($row = mysql_fetch_array($result))
	{
		$data_extensions.="#include $path_for_include$file_conf_extension$row[0]\n";
//		$data_extensions.="exten => _XXX,1,Queue(queue_$row[0]_${EXTEN},twh)";
	}
	//Open file includes for write. Create if not exist
	$fext = fopen($path.$file_extensions, 'w') or die ("can't open file");
	(fwrite($fext,$data_extensions))?:"Error on file create";
	// Close file
	fclose($fext)?:"Error on file close";

	/// Create config file
	$result_config = mysql_query("SELECT $pstnNumber_field,	$main_field FROM $db_stores.$table WHERE `$main_field` LIKE '$field'");
	$row_config = mysql_fetch_array($result_config);
	$data_config_extensions=generate_extensions_from_template($row_config, $template_path);

	//Open file config for write. Create if not exist
	$fcext = fopen($path.$file_conf_extension.$field, 'a') or die ("can't open file");
	(fwrite($fcext,$data_config_extensions))?:"Error on file create";
	// Close file
	fclose($fcext)?:"Error on file close";
}

///
/// Fuction For Generate Queues Configuration
///
function generate_queues_from_template($row_config)
{
	global $template_queue_file;
	global $template_path;
	global $main_field;

	/// Read template file
	$ft = fopen($template_path.$template_queue_file, 'r') or die ("can't open file");
	$text = fread($ft,filesize($template_path.$template_queue_file));
	$text = str_replace("%%STORENAME%%", $row_config[$main_field], $text);
	return $text;
}

///
/// Fuction For Generate Queues Operators (Outbound) Configuration
///
function generate_queues_operators_from_template($row_config, $row_operator)
{
	global $template_queue_operator_file;
	global $template_path;
	global $main_field;

	/// Read template file
	$ft = fopen($template_path.$template_queue_operator_file, 'r') or die ("can't open file");
	$text = fread($ft,filesize($template_path.$template_queue_operator_file));
	$text = str_replace("%%STORENAME%%", $row_config[$main_field], $text);
	$text = str_replace("%%OPERATOR%%", $row_operator, $text);
	return $text;
}


///
/// Fuction For Generate Extensions Operators (Outbound) Configuration
///
function generate_extensions_operators_from_template($row_config, $row_operator)
{
	global $template_extension_operator_file;
	global $template_path;
	global $main_field;

	/// Read template file
	$ft = fopen($template_path.$template_extension_operator_file, 'r') or die ("can't open file");
	$text = fread($ft,filesize($template_path.$template_extension_operator_file));
	$text = str_replace("%%STORENAME%%", $row_config[$main_field], $text);
	$text = str_replace("%%OPERATOR%%", $row_operator, $text);
	return $text;
}


///
/// Fuction For Generate Extensions Configuration
///
function generate_extensions_from_template($row_config, $template_path)
{
	global $template_extension_file;
//	global $template_path;
	global $main_field;
	global $pstnNumber_field;

	/// Read template file
	$ft = fopen($template_path.$template_extension_file, 'r') or die ("can't open file");
	$text = fread($ft,filesize($template_path.$template_extension_file));
	$text = str_replace("%%PSTNNUMBER%%", $row_config[$pstnNumber_field], $text);
	$text = str_replace("%%STORENAME%%", $row_config[$main_field], $text);
	return $text;
}

?>
