<?/// sum_graph_talk_wait_time.php

function sum_graph_talk_wait_time($_day, $_month, $_year, $_queue)
{
global $text_graphics_sum_graph_talk_wait_time;
global $text_graphics_Hours;
global $text_graphics_Days;
global $text_graphics_Months;
global $text_graphics_overall_call_time;
global $text_graphics_talk_time;
global $text_graphics_Minutes;
///
/// Define Variables
///
$host_DB='localhost';
$user_DB='asterisk';
$pass_DB='asterisk';
$db_DB='asterisk';
$table='queue_log';

/// Initialize Chart
# Create Column3D chart Object 
$FC = new FusionCharts("MSArea2D","500","400"); 
# set the relative path of the swf file
$FC->setSWFPath("charts/");

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

for($date=1;$date<=$count;$date++)
{
	$month=date('m',mktime(0,0,0,$_month,$date,$_year));
	if($month-$_month==0 || $count==12)
	{
		switch ($count)
		{
			case '24': // Day
				$from_date=$to_date=date('Y-m-d', mktime(0,0,0,$_month,$_day,$_year));
				$from_time=date('H:i:s', mktime($date-1,0,0,$_month,$_day,$_year));
				$to_time=date('H:i:s', mktime($date-1,59,59,$_month,$_day,$_year));
				$from_time_graph=date('H:i', mktime($date-1,0,0,$_month,$_day,$_year));
				$FC->addCategory($from_time_graph);
$strParam="rotateNames=1;yaxisname=$text_graphics_Minutes;numdivlines=9;divLineColor=CCCCCC;divLineAlpha=80;decimalPrecision=0;showAlternateHGridColor=1;AlternateHGridAlpha=30;AlternateHGridColor=CCCCCC;caption=$text_graphics_sum_graph_talk_wait_time $text_graphics_Hours;animation=0";
				break;
			case '31': //Month
				$from_date=$to_date=date('Y-m-d', mktime(0,0,0,$_month,$date,$_year));
				$from_time='00:00:00';
				$to_time='23:59:59';
				$FC->addCategory($from_date);
$strParam="rotateNames=1;yaxisname=$text_graphics_Minutes;numdivlines=9;divLineColor=CCCCCC;divLineAlpha=80;decimalPrecision=0;showAlternateHGridColor=1;AlternateHGridAlpha=30;AlternateHGridColor=CCCCCC;caption=$text_graphics_sum_graph_talk_wait_time $text_graphics_Days;animation=0";
				break;
			case '12': //Year
				$from_date=date('Y-m-d', mktime(0,0,0,$date,1,$_year));
				$to_date=date('Y-m-d', mktime(0,0,0,$date+1,0,$_year));
				$from_time='00:00:00';
				$to_time='23:59:59';
				$from_time_graph=date('F', mktime(0,0,0,$date,1,$_year));
				$FC->addCategory($from_time_graph);
$strParam="rotateNames=1;yaxisname=$text_graphics_Minutes;numdivlines=9;divLineColor=CCCCCC;divLineAlpha=80;decimalPrecision=0;showAlternateHGridColor=1;AlternateHGridAlpha=30;AlternateHGridColor=CCCCCC;caption=$text_graphics_sum_graph_talk_wait_time $text_graphics_Months;animation=0";
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

		$overall_duration_day=$talk_duration_day+$wait_duration_day;
		$talk_duration_day=round($talk_duration_day/60,2);
		$overall_duration_day=round($overall_duration_day/60,2);
		$FC_dataset_1[$date]=$overall_duration_day;
		$FC_dataset_2[$date]=$talk_duration_day;
		$days++;
	}
}
mysql_close($con_DB);

# Set chart attributes
global $bgcolor;
$strParam.=";bgcolor=$bgcolor";
$FC->setChartParams($strParam);

$FC->setParamDelimiter("\n");
$FC->addDataset("$text_graphics_overall_call_time", "color=e5124d\nshowValues=0\nareaAlpha=55\nshowAreaBorder=1\nareaBorderThickness=2\nareaBorderColor=FF0000");
for($i=1;$i<=$days;$i++)
{
		$FC->addChartData($FC_dataset_1[$i]);
}
$FC->addDataset("$text_graphics_talk_time", "color=12e565\nshowValues=0\nareaAlpha=55\nshowAreaBorder=1\nareaBorderThickness=2\nareaBorderColor=006600");
for($i=1;$i<=$days;$i++)
{
		$FC->addChartData($FC_dataset_2[$i]);
}

$FC->renderChart();
}
?>
