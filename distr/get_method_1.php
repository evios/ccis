<?
/// !!! Before Run Install php5-cli php5-curl php5-mysql catdoc

///
/// Define Variables
///
$path="/home/jenia/www/ccis";
require_once($path.'/conf/db_vars.php');
require_once($path.'/conf/db_connect.php');

date_default_timezone_set('Europe/Kiev');
$date=date("dmy");
$date_full=date("Y-m-d H:i:s");

//$local_folder="/var/ccis/distr/prices/";
$local_folder="/home/jenia/www/ccis/distr/prices/";

$query_distrs = "SELECT * FROM Distributors.List WHERE `get_file_method`='1'";
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
	/// Connecting to Authenticated Part Of SITE - Methed POST
	///
	$Curl_Session = curl_init($remote_site_to_auth);
	curl_setopt ($Curl_Session, CURLOPT_POST, 1);
	curl_setopt ($Curl_Session, CURLOPT_POSTFIELDS, $remote_post_param);
	curl_setopt ($Curl_Session, CURLOPT_FOLLOWLOCATION, 1);
	curl_exec ($Curl_Session);
	curl_close ($Curl_Session);

	///
	/// Get Price From SITE
	///
	switch ($file_type)
	{
		case 'xls':
			$res = get_file($remote_xls_file, $local_xls_file);
			/// XLS to CVS
			shell_exec ("xls2csv ".$local_xls_file." > ".$local_cvs_file);
			break;
		case 'csv':
	                $res = get_file($remote_xls_file, $local_cvs_file);
			break;
	}
	include 'csv_to_mysql.php';
}
unlink($local_cvs_file);
unlink($local_xls_file);
fclose($file_handle);
require_once ($path.'/conf/db_disconnect.php');
?>

<?
function get_file($remote, $local)
{
	/* get hostname and path of the remote file */
	$host = parse_url($remote, PHP_URL_HOST);
	$path = parse_url($remote, PHP_URL_PATH);
	
	/* prepare request headers */
	$reqhead = "GET $path HTTP/1.1\r\n"
			 . "Host: $host\r\n"
			 . "Connection: Close\r\n\r\n";
	
	/* open socket connection to remote host on port 80 */
	$fp = fsockopen($host, 80, $errno, $errmsg, 30);
	
	/* check the connection */
	if (!$fp) {
		print "Cannot connect to $host!\n";
		return false;
	}
	
	/* send request */
	fwrite($fp, $reqhead);

	/* read response */
	$res = "";
	while(!feof($fp)) {
		$res .= fgets($fp, 4096);
	}		
	fclose($fp);
	
	/* separate header and body */
	$neck = strpos($res, "\r\n\r\n");
	$head = substr($res, 0, $neck);
	$body = substr($res, $neck+4);

	/* check HTTP status */
	$lines = explode("\r\n", $head);
	preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $lines[0], $m);
	$status = $m[2];

	if ($status == 200) {
		file_put_contents($local, $body);
		return(true);
	} else {
		return(false);
	}
}


?>
