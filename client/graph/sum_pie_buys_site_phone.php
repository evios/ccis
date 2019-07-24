<?/// graph_complete_abandon.php

function sum_pie_buys_site_phone($_day,$_month, $_year, $_queue, $_source, $_store)
{
global $text_graphics_sum_pie_buys_site_phone;
global $text_graphics_Day;
global $text_graphics_Month;
global $text_graphics_Year;
global $text_graphics_buys;
global $text_graphics_buys_from_phone;
global $text_graphics_buys_from_site;


global $db_con;
$buys_site=0;
$buys_phone=0;

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
			$strParam="caption=$text_graphics_sum_pie_buys_site_phone $text_graphics_Day;decimalPrecision=0;formatNumberScale=1;showNames=1;animation=0";
			break;
		case '31': //Month
			$from_date=date('Y-m-d', mktime(0,0,0,$_month,$date,$_year));
			$to_date=date('Y-m-d', mktime(0,0,0,$_month+1,$date-1,$_year));
			$from_time='00:00:00';
			$to_time='23:59:59';
			$strParam="caption=$text_graphics_sum_pie_buys_site_phone $text_graphics_Month;decimalPrecision=0;formatNumberScale=1;showNames=1;animation=0";
			break;
		case '12': //Year
			$from_date=date('Y-m-d', mktime(0,0,0,1,$date,$_year));
			$to_date=date('Y-m-d', mktime(0,0,0,1,0,$_year+1));
			$from_time='00:00:00';
			$to_time='23:59:59';
			$strParam="caption=$text_graphics_sum_pie_buys_site_phone $text_graphics_Year;decimalPrecision=0;formatNumberScale=1;showNames=1;animation=0";
			break;
	}	

	/// BUYS from site
	$query_buys_site="SELECT id FROM Orders.Orders WHERE (store LIKE '$_store') AND Orders.Orders.order_time >= '$from_date $from_time' AND Orders.Orders.order_time <= '$to_date $to_time' AND source LIKE 'site' AND state='complete'";
	$buys_site=mysql_num_rows(mysql_query($query_buys_site, $db_con));

	/// BUYS from phone
	$query_buys_phone="SELECT id FROM Orders.Orders WHERE (store LIKE '$_store') AND Orders.Orders.order_time >= '$from_date $from_time' AND Orders.Orders.order_time <= '$to_date $to_time' AND source LIKE 'phone' AND state='complete'";
	$buys_phone=mysql_num_rows(mysql_query($query_buys_phone, $db_con));

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

$FC->addChartData($buys_phone,"name=$text_graphics_buys_from_phone,<br>($text_graphics_buys);color=ea7697");
$FC->addChartData($buys_site,"name=$text_graphics_buys_from_site,<br>($text_graphics_buys);color=73b27c");

# Render Chart 
$FC->renderChart();
}
?>
