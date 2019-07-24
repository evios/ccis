<?/// average_pie_complete_abandon_per_day.php

function average_pie_complete_abandon_per_day($_day,$_month, $_year, $_queue)
{
global $text_graphics_average_pie_complete_abandon_per_day;
global $text_graphics_Hour;
global $text_graphics_Day;
global $text_graphics_Month;
global $text_graphics_abandon;
global $text_graphics_complete;
global $text_graphics_calls;

///
/// Define Variables
///
$host_DB='localhost';
$user_DB='asterisk';
$pass_DB='asterisk';
$db_DB='asterisk';
$table='queue_log';

$complete=0;
$abandon=0;
$days=0;

/// Connecting to DB
$con_DB = mysql_connect($host_DB, $user_DB, $pass_DB, $_source);
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
			$strParam="caption=$text_graphics_average_pie_complete_abandon_per_day $text_graphics_Hour;decimalPrecision=1;formatNumberScale=1;showNames=1;animation=0";
			break;
		case '31': //Month
			$from_date=date('Y-m-d', mktime(0,0,0,$_month,$date,$_year));
			$to_date=date('Y-m-d', mktime(0,0,0,$_month+1,$date-1,$_year));
			$from_time='00:00:00';
			$to_time='23:59:59';
			$days=date('d', mktime(0,0,0,$_month+1,$date-1,$_year))-date('d', mktime(0,0,0,$_month,$date,$_year))+1;
			$strParam="caption=$text_graphics_average_pie_complete_abandon_per_day $text_graphics_Day;decimalPrecision=1;formatNumberScale=1;showNames=1;animation=0";
			break;
		case '12': //Year
			$from_date=date('Y-m-d', mktime(0,0,0,1,$date,$_year));
			$to_date=date('Y-m-d', mktime(0,0,0,1,0,$_year+1));
			$from_time='00:00:00';
			$to_time='23:59:59';
			$days=12;
			$strParam="caption=$text_graphics_average_pie_complete_abandon_per_day $text_graphics_Month;decimalPrecision=0;formatNumberScale=1;showNames=1;animation=0";
			break;
	}
	
	/// ABANDON calls
	$query_abadon="SELECT FROM_UNIXTIME(TIME) FROM  `queue_log` WHERE (event =  'ABANDON') AND (queuename LIKE 'queue_$_queue') AND (FROM_UNIXTIME( TIME ) >= '$from_date $from_time') AND (FROM_UNIXTIME( TIME ) <= '$to_date $to_time')";
	$abandon=mysql_num_rows(mysql_query($query_abadon));

	/// COMPLETE calls
	$query_connect="SELECT FROM_UNIXTIME(TIME) FROM  `queue_log` WHERE (event =  'CONNECT') AND (queuename LIKE 'queue_$_queue') AND (FROM_UNIXTIME	( TIME ) >= '$from_date $from_time') AND (FROM_UNIXTIME( TIME ) <= '$to_date $to_time')";
	$complete=mysql_num_rows(mysql_query($query_connect));
//	$days++;
}
mysql_close($con_DB);
$average_complete=$complete/$days;
$average_abandon=$abandon/$days;

/// Initialize Chart
# Create Column3D chart Object 
$FC = new FusionCharts("Pie2D","500","400"); 
# set the relative path of the swf file
$FC->setSWFPath("charts/");
# Set chart attributes
global $bgcolor;
$strParam.=";bgcolor=$bgcolor";
$FC->setChartParams($strParam);

$FC->addChartData($average_abandon,"name=$text_graphics_abandon,<br> ($text_graphics_calls);color=ea7697");
$FC->addChartData($average_complete,"name=$text_graphics_complete,<br> ($text_graphics_calls);color=73b27c");

# Render Chart 
$FC->renderChart();
}
?>
