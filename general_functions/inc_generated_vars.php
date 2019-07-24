<?
///
/// Generated Variables
///

/// Create Sample Fields (Important Fields, for sample bulk view) From Array
for ( $i = 0; $i < count($fields_sample_array); $i++ )
{
	if ((count($fields_sample_array)-$i)==1)
	{
		$fields_sample=$fields_sample."`$fields_sample_array[$i]`";
	}
	else
	{
		$fields_sample=$fields_sample."`$fields_sample_array[$i]`, ";
	}
}

/// Get Columns Name From MYSQL
$result_column_names = mysql_query("SELECT * FROM `".$table."` LIMIT 1");
$column_num = mysql_num_fields( $result_column_names );
for ( $i = 0; $i < $column_num; $i++ )
{
	$column_names[] = mysql_field_name( $result_column_names, $i );
}
?>
