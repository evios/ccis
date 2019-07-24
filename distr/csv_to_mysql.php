<?
//header('Content-type: text/html; charset=utf-8');
//	if(!setlocale(LC_ALL, 'ru_RU.utf8')) setlocale(LC_ALL, 'en_US.utf8');
//	if(setlocale(LC_ALL, 0) == 'C') die('Не поддерживается ни одна из перечисленных локалей (ru_RU.utf8, en_US.utf8)');

// DB Table
$tbl_create_rows="`code` varchar(20) collate utf8_unicode_ci default NULL,
  `manufacturer` varchar(30) collate utf8_unicode_ci default NULL,
  `description` varchar(200) collate utf8_unicode_ci default NULL,
  `availability` varchar(20) collate utf8_unicode_ci default NULL,
  `update_date` varchar(100) collate utf8_unicode_ci default NULL";
$tbl_upload_rows="`code`, `manufacturer`, `description`, `availability`, `update_date`";


/// Create Table If New Diwstributor
$query_create_tbl="CREATE TABLE IF NOT EXISTS `D_".$remote_distr_name."` (".$tbl_create_rows.") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
$result_create_tbl = mysql_query($query_create_tbl);

/// Clear Table Before Upload New Data
$query_clear="TRUNCATE TABLE Distributors.D_$remote_distr_name";
$result_clear = mysql_query($query_clear);
///////////////////////////////////

///
/// Parse CVS
///
//$file_handle = fopen($local_cvs_file, "r");
$file_handle= fopen('php://memory', 'w+');
fwrite($file_handle, iconv("$row_encoding", "UTF-8", file_get_contents($local_cvs_file)));
rewind($file_handle);

while (!feof($file_handle) )
{
        $line_of_text = fgetcsv($file_handle, 1024);
	($line_of_text[$row_availability-1]==$row_availability_value)?$availability=1:$availability=0;
	
	/// Upload new Code's and Availabilities's
//        $query_add = "INSERT INTO `D_".$remote_distr_name."` (".$tbl_upload_rows.") VALUE ('".$line_of_text[$row_code-1]."', '".$line_of_text[$row_manufacturer-1]."', '".$line_of_text[$row_description-1]."', '".$line_of_text[$row_availability-1]."', '$date_full')";
        $query_add = "INSERT INTO Distributors.D_".$remote_distr_name." (".$tbl_upload_rows.") VALUE ('".$line_of_text[$row_code-1]."', '".$line_of_text[$row_manufacturer-1]."', '".$line_of_text[$row_description-1]."', '".$availability."', '$date_full')";
        $result_add = mysql_query($query_add);
	mysql_query("UPDATE Distributors.List SET `update_date`='$date_full' WHERE `name` LIKE '$remote_distr_name'");
}
?>
