<?/// average_pie_talk_wait_time_per_call.php

function average_pie_talk_wait_time_per_call($_day,$_month, $_year, $_queue)
{
global $text_graphics_average_pie_talk_wait_time_per_call;
global $text_graphics_seconds;
global $text_graphics_wait_time;
global $text_graphics_talk_time;
///
/// Define Variables
///
$host_DB='localhost';
$user_DB='asterisk';
$pass_DB='asterisk';
$db_DB='asterisk';
$table='queue_log';

$complete=0;
$talk=0;
$wait=0;

/// Initialize Chart
# Create Column3D chart Object 
$FC = new FusionCharts("Pie2D","500","400"); 
# set the relative path of the swf file
$FC->setSWFPath("charts/");
# Set chart attributes
$strParam="caption=$text_graphics_average_pie_talk_wait_time_per_call;decimalPrecision=0;formatNumberScale=1;showNames=1;animation=0";
global $bgcolor;
$strParam.=";bgcolor=$bgcolor";
$FC->setChartParams($strParam);

/// Connecting to DB
$con_DB = mysql_connect($host_DB, $user_DB, $pass_DB);
if (!$con_DB)
{
	die('Could not connect: ' . mysql_error());
}
mysql_select_db($db_DB, $con_DB);

/// Year Month or Day Selected
$count=24; /// Day
($_day==0)?$count=31:''; /// Month
($_day==0 && $_month==0 )?$count=12:''; /// Year

for($date=1;$date<=1;$date++)
{
	switch ($count)
	{
		case '24': // Day
			$from_date=$to_date=date('Y-m-d', mktime(0,0,0,$_month,$_day,$_year));
			$from_time=date('H:i:s', mktime(0,0,0,$_month,$_day,$_year));
			$to_time=date('H:i:s', mktime(23,59,59,$_month,$_day,$_year));
			$days=24;
			break;
		case '31': //Month
			$from_date=date('Y-m-d', mktime(0,0,0,$_month,$date,$_year));
			$to_date=date('Y-m-d', mktime(0,0,0,$_month+1,$date-1,$_year));
			$from_time='00:00:00';
			$to_time='23:59:59';
			$days=date('d', mktime(0,0,0,$_month+1,$date-1,$_year))-date('d', mktime(0,0,0,$_month,$date,$_year))+1;
			break;
		case '12': //Year
			$from_date=date('Y-m-d', mktime(0,0,0,1,$date,$_year));
			$to_date=date('Y-m-d', mktime(0,0,0,1,0,$_year+1));
			$from_time='00:00:00';
			$to_time='23:59:59';
			$days=12;
			break;
	}
	
	$talk_duration_day=0;
	$wait_duration_day=0;
	/// Call Duration
	$result_connect=mysql_query("SELECT `data` FROM `queue_log` WHERE ((`event` =  'COMPLETECALLER') OR (`event` =  'COMPLETEAGENT')) AND (queuename LIKE 'queue_$_queue') AND (FROM_UNIXTIME( TIME ) >= '$from_date $from_time') AND (FROM_UNIXTIME( TIME ) <= '$to_date $to_time')");
	while($row_connect = mysql_fetch_array($result_connect))
	{
		$talk_wait_time_day = explode("|", $row_connect[0]);
		/// Talk time
		$talk_duration_day+=$talk_wait_time_day[1];
		/// Wait time Before Connect
		$wait_duration_day+=$talk_wait_time_day[0];
	}
	$talk=$talk_duration_day;
	$wait=$wait_duration_day;
	/// COMPLETE calls
	$query_connect="SELECT FROM_UNIXTIME(TIME) FROM  `queue_log` WHERE (event =  'CONNECT') AND (queuename LIKE 'queue_$_queue') AND (FROM_UNIXTIME	( TIME ) >= '$from_date $from_time') AND (FROM_UNIXTIME( TIME ) <= '$to_date $to_time')";
	$complete=mysql_num_rows(mysql_query($query_connect));
}
mysql_close($con_DB);

$talk=$talk/$complete;
$wait=$wait/$complete;
$FC->addChartData($wait,"name=$text_graphics_wait_time,<br> ($text_graphics_seconds);color=ea7697");
$FC->addChartData($talk,"name=$text_graphics_talk_time,<br> ($text_graphics_seconds);color=73b27c");

# Render Chart 
$FC->renderChart();
}
?>
