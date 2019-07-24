<?
/// !!! Before Run Install php5-cli php5-curl php5-mysql catdoc

///
/// Define Variables
///
include '/home/jenia/www/ccis/conf/db_vars.php';
$db_distributor='Distributors';

date_default_timezone_set('Europe/Kiev');
$date=date("dmy");
$date_full=date("Y-m-d H:i:s");

//$local_folder="/var/ccis/distr/prices/";
$local_folder="/home/jenia/www/ccis/distr/prices/";

///
/// Connecting to DB
///
$db_con = mysql_connect($db_host, $db_user, $db_pass);
if (!$db_con)
{
        die('Could not connect: ' . mysql_error());
}
mysql_select_db($db_distributor, $db_con);
mysql_query ('SET NAMES UTF8');

$query_distrs = "SELECT * FROM `List` WHERE `get_file_method`='2'";
$result_distrs = mysql_query($query_distrs);
while($row_distrs = mysql_fetch_array( $result_distrs ))
{
	$remote_distr_name=$row_distrs['name'];
	$remote_site_to_auth=$row_distrs['auth_site'];
	$remote_post_param=$row_distrs['post_param'];
        $file_type=$row_distrs['file_type'];
	$remote_xls_file=$row_distrs['file_name'];
	$row_code=$row_distrs['field_code'];
        $row_manufacturer=$row_distrs['field_manufacturer'];
        $row_description=$row_distrs['field_description'];
	$row_availability=$row_distrs['field_availability'];
        $row_encoding=$row_distrs['encoding'];
        $row_availability_value=$row_distrs['availability_value'];

	$local_xls_file=$local_folder.$remote_distr_name.$date.".xls";
	$local_cvs_file=$local_folder.$remote_distr_name.$date.".cvs";

	///
	/// Get Price From SITE
	///
	include 'index.php';
	switch ($file_type)
        {
                case 'xls':
                        //$res = get_file($remote_xls_file, $local_xls_file);
                        /// XLS to CVS
                        shell_exec ("xls2csv ".$local_xls_file." > ".$local_cvs_file);
                        break;
                case 'csv':
                        //$res = get_file($remote_xls_file, $local_cvs_file);
                        break;
        }

	include '../csv_to_mysql.php';
}
unlink($local_cvs_file);
unlink($local_xls_file);
fclose($file_handle);
mysql_close($db_con);
?>
