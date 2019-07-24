<?/// graph_complete_abandon.php

function sum_pie_calls_buys($_day,$_month, $_year, $_queue, $_source, $_store)
{
global $text_graphics_sum_pie_calls_buys;
global $text_graphics_Day;
global $text_graphics_Month;
global $text_graphics_Year;
global $text_graphics_calls_with_buys;
global $text_graphics_calls_without_buys;

global $db_con;
global $db_con_asterisk;
$complete_calls=0;
$overall_buys=0;

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
			$strParam="caption=$text_graphics_sum_pie_calls_buys $text_graphics_Day;decimalPrecision=0;formatNumberScale=1;showNames=1;animation=0";
			break;
		case '31': //Month
			$from_date=date('Y-m-d', mktime(0,0,0,$_month,$date,$_year));
			$to_date=date('Y-m-d', mktime(0,0,0,$_month+1,$date-1,$_year));
			$from_time='00:00:00';
			$to_time='23:59:59';
			$strParam="caption=$text_graphics_sum_pie_calls_buys $text_graphics_Month;decimalPrecision=0;formatNumberScale=1;showNames=1;animation=0";
			break;
		case '12': //Year
			$from_date=date('Y-m-d', mktime(0,0,0,1,$date,$_year));
			$to_date=date('Y-m-d', mktime(0,0,0,1,0,$_year+1));
			$from_time='00:00:00';
			$to_time='23:59:59';
			$strParam="caption=$text_graphics_sum_pie_calls_buys $text_graphics_Year;decimalPrecision=0;formatNumberScale=1;showNames=1;animation=0";
			break;
	}	

	/// Overall BUYS
	$query_buys="SELECT id FROM Orders.Orders WHERE source LIKE '$_source' AND (store LIKE '$_store') AND Orders.Orders.order_time >= '$from_date $from_time' AND Orders.Orders.order_time <= '$to_date $to_time' AND state='complete'";
	$overall_buys=mysql_num_rows(mysql_query($query_buys, $db_con));

	/// COMPLETE calls
	$query_connect="SELECT FROM_UNIXTIME(TIME) FROM  asterisk.queue_log WHERE (event =  'CONNECT') AND (queuename LIKE 'queue_$_queue') AND (FROM_UNIXTIME	( TIME ) >= '$from_date $from_time') AND (FROM_UNIXTIME( TIME ) <= '$to_date $to_time')";
	$complete_calls=mysql_num_rows(mysql_query($query_connect, $db_con_asterisk));
}

/// Initialize Chart
# Create Column3D chart Object 
$FC = new FusionCharts("Pie2D","500","400"); 
# set the relative path of the swf file
$FC->setSWFPath("charts/");
# Set chart attributes
global $bgcolor;
$strParam.=";bgcolor=$bgcolor";
$FC->setChartParams($strParam);

$calls=$complete_calls-$overall_buys;
$FC->addChartData($overall_buys,"name=$text_graphics_calls_with_buys;color=73b27c");
$FC->addChartData($calls,"name=$text_graphics_calls_without_buys;color=ea7697");

# Render Chart 
$FC->renderChart();
}
?>
