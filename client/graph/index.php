<form action="" method="get" name="frm" id="frm">
	<input type="hidden" name="view" value="<?=$_GET['view']?>"/>
	<?
	if(isset($_GET['day'])) {$day=$_GET['day']; echo "<input type='hidden' name='day' value='$day'/>";}
	if(isset($_GET['month'])) {$month=$_GET['month']; echo "<input type='hidden' name='month' value='$month'/>";}
	if(isset($_GET['year'])) {$year=$_GET['year']; echo "<input type='hidden' name='year' value='$year'/>";}
	if(isset($_GET['queue'])){$queue=$_GET['queue'];}else{$queue=$store;}
	?>

	<select class="button" name="queue" onChange="frm.submit();">
		<option value="<?=$store?>%" <?if($_GET['queue']==$store."%"){echo 'SELECTED';$source="%";}?>>Overall</option>
		<option value="<?=$store?>_%" <?if($_GET['queue']==$store."_%"){echo 'SELECTED';$source="site";}?>>Site/Outbound</option>
		<option value="<?=$store?>" <?if($_GET['queue']==$store){echo 'SELECTED';$source="phone";}?>>Phone/Inbound</option>
	</select>

</form>
<?
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
require_once('../conf/db_connect_asterisk.php');

/// Set timezone
date_default_timezone_set('Europe/Kiev');
/// Includes
require_once('../general_functions/multiexplode.php');

$delimeters=Array(",");
$query_get_graphs="SELECT `graphics` FROM Stores.List WHERE `name` LIKE '$store' LIMIT 1";
$store_get_graphs=mysql_query($query_get_graphs);
$graphs=multiexplode($delimeters,mysql_result($store_get_graphs,0,'graphics'));
for($i=0;$i<sizeof($graphs);$i++)
{
//	$graphic_id=$graphs[$i];
// 	$graphic_description=mysql_result(mysql_query("SELECT description_english FROM Stores.Graphics WHERE id='$graphic_id' LIMIT 1"),0,'description_english');
 	$graphic_name=mysql_result(mysql_query("SELECT name FROM Stores.Graphics WHERE id='{$graphs[$i]}' LIMIT 1"),0,'name');
	include "$graphic_name.php";
}

//include 'sum_graph_complete_abandon.php';
//include 'sum_graph_buys.php';
//include 'sum_graph_calls_buys.php';
//include 'sum_graph_talk_wait_time.php';
//include 'average_graph_talk_wait_time.php';
//include 'sum_pie_complete_abandon.php';
//include 'sum_pie_buys.php';
//include 'sum_pie_calls_buys.php';
//include 'sum_pie_buys_site_phone.php';
//include 'sum_pie_talk_wait_time.php';
//include 'average_pie_talk_wait_time_per_call.php';
//include 'average_pie_talk_wait_time_per_day.php';
//include 'average_pie_complete_abandon_per_day.php';

if (isset($day_today) && isset($month_today) && isset($year_today) && isset($queue))
{

require_once('sum_graph_complete_abandon.php');
//include 'sum_graph_buys.php';
//include 'sum_graph_calls_buys.php';
require_once('sum_graph_talk_wait_time.php');
//include 'average_graph_talk_wait_time.php';
require_once('sum_pie_complete_abandon.php');
//include 'sum_pie_buys.php';
require_once('sum_pie_calls_buys.php');
require_once('sum_pie_buys_site_phone.php');
require_once('sum_pie_talk_wait_time.php');
//include 'average_pie_talk_wait_time_per_call.php';
//include 'average_pie_talk_wait_time_per_day.php';
//include 'average_pie_complete_abandon_per_day.php';
require_once('charts/FusionCharts_Gen.php');
	///Rendering Graphics For Today
echo "<div class='centerDiv'>";
echo "<div class='graph'>";
	if(function_exists('sum_pie_complete_abandon'))sum_pie_complete_abandon($day_today, $month_today, $year_today, $queue);
echo "</div>";
//	if(function_exists('sum_pie_buys'))sum_pie_buys($day_today, $month_today, $year_today, $queue, $source));
echo "<div class='graph'>";
	if(function_exists('sum_pie_calls_buys'))sum_pie_calls_buys($day_today, $month_today, $year_today, $queue, $source, $store);
echo "</div>";
echo "<div class='graph'>";
	if(function_exists('sum_graph_complete_abandon'))sum_graph_complete_abandon($day_today, $month_today, $year_today, $queue);
echo "</div>";
//	if(function_exists('sum_graph_buys'))sum_graph_buys($day_today, $month_today, $year_today, $queue, $source);
//	if(function_exists('sum_graph_calls_buys'))sum_graph_calls_buys($day_today, $month_today, $year_today, $queue, $source);
echo "<div class='graph'>";
	if(function_exists('sum_pie_buys_site_phone'))sum_pie_buys_site_phone($day_today, $month_today, $year_today, $queue, $source, $store);
echo "</div>";
echo "<div class='graph'>";
	if(function_exists('sum_graph_talk_wait_time'))sum_graph_talk_wait_time($day_today, $month_today, $year_today, $queue);
echo "</div>";
echo "<div class='graph'>";
	if(function_exists('sum_pie_talk_wait_time'))sum_pie_talk_wait_time($day_today, $month_today, $year_today, $queue);
echo "</div>";
echo "</div>";
}
else
{
	/// Set Variables
	$Y=date('Y');
	$M=date('F');
	/// Printing links on two years
echo "<div class='calendar'>";
	for($y=-1;$y<=0;$y++)
	{
	$tmp=$Y+$y;
	($_GET['year']==$tmp)?$class="act":$class="pas";
	echo "<a class='$class' href='index.php?view=history&queue=$queue&day=0&month=0&year=$tmp'> $tmp </a>";
	}
echo "</div>";
//	echo "<br>";

	/// Printing links on 12 monthes
echo "<div class='calendar'>";
	for($m=1;$m<=12;$m++)
	{
	//$M=date('F',mktime(0,0,0,$m,1,$Y)); // Full month like January
	$M=date('M',mktime(0,0,0,$m,1,$Y)); // Short month like Jan
	($_GET['month']==$m)?$class="act":$class="pas";
	if (isset($year))
		echo "<a class='$class' href='index.php?view=history&queue=$queue&day=0&month=$m&year=$year'> $M </a>";
	else
		echo "<a class='$class' href='index.php?view=history&queue=$queue&day=0&month=$m&year=$Y'> $M </a>";
	}
echo "</div>";
//	echo "<br>";

	/// Rendering Graphics
	if (isset($day) && isset($month) && isset($year) && isset($queue))
	{
		/// Printing links on days of month
echo "<div class='calendar'>";
		for($d=1;$d<=31;$d++)
		{
			if (date('m',mktime(0,0,0,$month,$d,$year))-$month==0)
			{
				$D=date('d',mktime(0,0,0,$month,$d,$year));
				($_GET['day']==$D)?$class="act":$class="pas";
				echo "<a class='$class' href='index.php?view=history&queue=$queue&day=$D&month=$month&year=$year'> $D </a>";
			}
		}
echo "</div>";
//		echo "<br><br>";
require_once('charts/FusionCharts_Gen.php');
		///Rendering Graphics For History
echo "<div class='centerDiv'>";
echo "<div class='graph'>";
		if(function_exists('sum_graph_complete_abandon'))sum_graph_complete_abandon($day, $month, $year, $queue);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('sum_graph_buys'))sum_graph_buys($day, $month, $year, $queue, $source, $store);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('sum_graph_calls_buys'))sum_graph_calls_buys($day, $month, $year, $queue, $source, $store);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('sum_pie_buys'))sum_pie_buys($day, $month, $year, $queue, $source, $store);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('sum_pie_calls_buys'))sum_pie_calls_buys($day, $month, $year, $queue, $source, $store);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('sum_pie_buys_site_phone'))sum_pie_buys_site_phone($day, $month, $year, $queue, $source, $store);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('sum_graph_talk_wait_time'))sum_graph_talk_wait_time($day, $month, $year, $queue);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('average_graph_talk_wait_time'))average_graph_talk_wait_time($day, $month, $year, $queue);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('sum_pie_complete_abandon'))sum_pie_complete_abandon($day, $month, $year, $queue);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('sum_pie_talk_wait_time'))sum_pie_talk_wait_time($day, $month, $year, $queue);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('average_pie_talk_wait_time_per_call'))average_pie_talk_wait_time_per_call ($day, $month, $year, $queue);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('average_pie_talk_wait_time_per_day'))average_pie_talk_wait_time_per_day($day, $month, $year, $queue);
echo "</div>";
echo "<div class='graph'>";
		if(function_exists('average_pie_complete_abandon_per_day'))average_pie_complete_abandon_per_day($day, $month, $year, $queue);
echo "</div>";
echo "</div>";
	}
}
require_once('../conf/db_disconnect_asterisk.php');
require_once('../conf/db_disconnect.php');

?>
