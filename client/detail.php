<?
require_once('../conf/db_vars_asterisk.php');
require_once('../conf/db_connect.php');

///
/// Define Variables
///
$date=date('Y-m-d');
$time=date('H:i:s');
//javascript:document.form_date.submit();
?>

<form action="" method="get" name="frm_csv" id="frm_csv">
	<input type="hidden" name="view" value="<?=$_GET['view']?>"/>
	<input type="hidden" name="csv" id="csv" value="0"/>
	<select class="button" name="queue" onChange="frm_csv.submit();">
		<option value="<?=$store?>%" <?if($_GET['queue']==$store."%")echo 'SELECTED';?>>Overall</option>
		<option value="<?=$store?>_%" <?if($_GET['queue']==$store."_%")echo 'SELECTED';?>>Site/Outbound</option>
		<option value="<?=$store?>" <?if($_GET['queue']==$store)echo 'SELECTED';?>>Phone/Inbound</option>
	</select>
	<br>
	<input class="rounded-textbox" type="text" name="from_date" value="<?if ($from_date=$_GET['from_date']){echo $from_date;}else{echo $date;}?>"/>
	<input class="rounded-textbox" type="text" name="from_time" value="<?if ($from_time=$_GET['from_time']){echo $from_time;}else{echo '00:00:00';}?>"/>
	<br>
	<input class="rounded-textbox" type="text" name="to_date" value="<?if ($to_date=$_GET['to_date']){echo $to_date;}else{echo $date;}?>"/>
	<input class="rounded-textbox" type="text" name="to_time" value="<?if ($to_time=$_GET['to_time']){echo $to_time;}else{echo '23:59:59';}?>"/>
<br>
</form>
<input class="button" value="<?=$text_submit?>" type="submit" name="submit" onClick="frm_csv.submit();"/><br><br>
<input class="button" type="button" onclick="getcsv('1')" value="Get a CSV File!"><br>
<?
if ((!$from_date=$_GET['from_date']) && (!$to_date=$_GET['to_date']))
{
$from_date=$date;
$to_date=$date;
}

if ((!$from_time=$_GET['from_time']) && (!$to_time=$_GET['to_time']))
{
$from_time='00:00:00';
$to_time='23:59:59';
}

(isset($_GET['queue']))?$queue=$_GET['queue']:$queue=$store."%";

//mysql_select_db($db_DB, $con_DB);
$abandon_cols_num=3; /// 3 is columns number
//$data_detail.="<b>ABANDON calls ";
$query_abadon="SELECT FROM_UNIXTIME(TIME),`data`, `callid` FROM  $db_db.queue_log WHERE (event =  'ABANDON') AND (queuename LIKE 'queue_$queue') AND (FROM_UNIXTIME( TIME ) >= '$from_date $from_time') AND (FROM_UNIXTIME( TIME ) <= '$to_date $to_time')";
$result_abandon = mysql_query($query_abadon);
$csv_data_1_[]="";
$j=0;
//$data_detail.=mysql_num_rows($result_abandon)."</b><br>";
//$data_detail.="<div align='center'>";
//$data_detail.="<center>";
$data_detail.="<table id='rounded-corner'><thead><tr>";
$data_detail.="<th class='rounded-first'>$text_detail_call_time</th>";
	$csv_data_1_[0][$j++].=$text_detail_call_time;
$data_detail.="<th>$text_detail_wait_duration</th>";
	$csv_data_1_[0][$j++].=$text_detail_wait_duration;
$data_detail.="<th class='rounded-last'>$text_detail_caller_id</th>";
	$csv_data_1_[0][$j++].=$text_detail_caller_id;
$data_detail.="</tr></thead>";
$data_detail.="<tfoot><tr>";
$data_detail.="<td colspan='$abandon_cols_num' class='rounded-foot'><em><b>$text_detail_abandoned_calls: ".mysql_num_rows($result_abandon)."</b></em></td>";
$data_detail.="</tr></tfoot>";
$data_detail.="<tbody>";
$i=0;
while($row_abandon = mysql_fetch_array($result_abandon))
{
	$j=0;
	($i % 2)?$class="odd":$class="even";
	$data_detail.="<tr>";
	/// Abandon Caller ID
	$row_get_cid_abandon=mysql_fetch_array(mysql_query("SELECT `data` FROM $db_db.queue_log WHERE (`event` =  'ENTERQUEUE') AND (queuename LIKE 'queue_$queue') AND (`callid`='$row_abandon[2]')"));
	$cid_abandon = explode("|", $row_get_cid_abandon[0]);
	/// Wait time before Abandon
	$wait_time_abandon = explode("|", $row_abandon[1]);
	///Detail Statistics		
	$data_detail.="<td class='$class' align='center'>$row_abandon[0]</td>";
		$csv_data_1_[$i+1][$j++].=$row_abandon[0];
	$data_detail.="<td class='$class' align='center'>$wait_time_abandon[2]</td>";
		$csv_data_1_[$i+1][$j++].=$wait_time_abandon[2];
	$data_detail.="<td class='$class' align='center'>$cid_abandon[1]</td>";
		$csv_data_1_[$i+1][$j++].=$cid_abandon[1];
	$data_detail.="</tr>";
	$i++;
}
$data_detail.="</tbody>";
$data_detail.="</table>";
$data_detail.="<input class='button' type='button' onclick=\"getcsv('1')\" value='Get a CSV File!'>";
//$data_detail.="</center>";
//$data_detail.="</div>";
$data_detail.="<br><br>";
$data_detail.="<input class='button' type='button' onclick=\"getcsv('2')\" value='Get a CSV File!'>";

$complete_cols_num=8; /// 8 is columns number
//$data_detail.="<b>COMPLETE calls ";
$query_connect="SELECT FROM_UNIXTIME(TIME),`data`, `callid`, `agent` FROM  $db_db.queue_log WHERE (event =  'CONNECT') AND (queuename LIKE 'queue_$queue') AND (FROM_UNIXTIME( TIME ) >= '$from_date $from_time') AND (FROM_UNIXTIME( TIME ) <= '$to_date $to_time')";
$result_connect = mysql_query($query_connect);
$csv_data_2_[]="";
$j=0;
//$complete=mysql_num_rows($result_connect)."</b><br>";
//$data_detail.=mysql_num_rows($result_connect)."</b><br>";
//$data_detail.="<div align='center'>";
$data_detail.="<table id='rounded-corner'><thead><tr>";
$data_detail.="<th class='rounded-first'>$text_detail_call_time</th>";
	$csv_data_2_[0][$j++].=$text_detail_call_time;
$data_detail.="<th>$text_detail_wait_duration</th>";
	$csv_data_2_[0][$j++].=$text_detail_wait_duration;
$data_detail.="<th>$text_detail_caller_id</th>";
	$csv_data_2_[0][$j++].=$text_detail_caller_id;
$data_detail.="<th>$text_detail_agent</th>";
	$csv_data_2_[0][$j++].=$text_detail_agent;
$data_detail.="<th>$text_detail_talk_duration_operator</th>";
	$csv_data_2_[0][$j++].=$text_detail_talk_duration_operator;
$data_detail.="<th>$text_detail_call_route</th>";
	$csv_data_2_[0][$j++].=$text_detail_call_route;
$data_detail.="<th>$text_detail_overall_call_duration</th>";
	$csv_data_2_[0][$j++].=$text_detail_overall_call_duration;
$data_detail.="<th class='rounded-last'>$text_detail_call_record</th>";
//	$csv_data_2_[0][$j++].=$text_detail_call_record;
$data_detail.="</tr></thead>";
$data_detail.="<tfoot><tr>";
$data_detail.="<td colspan='$complete_cols_num' class='rounded-foot'><em><b>$text_detail_completed_calls: ".mysql_num_rows($result_connect)."</b></em></td>";
//$data_detail.="<td class='rounded-foot-right'>&nbsp;</td>";
$data_detail.="</tr></tfoot>";
$data_detail.="<tbody>";
$i=0;
while($row_connect = mysql_fetch_array($result_connect))
{
	$j=0;
	($i % 2)?$class="odd":$class="even";
	$call_route='';
	$overall_call_duration=0;
	/// Call Route
	$result_call_route=mysql_query("SELECT `dst`,`duration` FROM $db_db.cdr WHERE (`disposition` =  'ANSWERED') AND (`uniqueid` = '$row_connect[2]') AND (`lastdata` NOT LIKE '%queue%')");
	$row_get_overall_call_duration=mysql_fetch_array(mysql_query("SELECT `duration` FROM $db_db.cdr WHERE (`disposition` =  'ANSWERED') AND (`uniqueid` = '$row_connect[2]') AND (`lastdata` LIKE '%queue%')"));
	while($row_call_route = mysql_fetch_array($result_call_route))
	{
		$call_route="$call_route -- $row_call_route[0]";
		$overall_call_duration=$overall_call_duration+$row_call_route[1];
	}
		/// Overall Call Duration. With IVR, Transfers, Speaks
	$overall_call_duration=$overall_call_duration+$row_get_overall_call_duration[0];
	/// Call Duration. Only Speak Duration (If Need Full Call Duration, then + Wait Duration)
	$row_get_call_duration_connect=mysql_fetch_array(mysql_query("SELECT `data` FROM $db_db.queue_log WHERE ((`event` =  'COMPLETECALLER') OR (`event` =  'COMPLETEAGENT')) AND (queuename LIKE 'queue_$queue') AND (`callid`='$row_connect[2]')"));
	$call_duration_connect = explode("|", $row_get_call_duration_connect[0]);
	/// Agent Number Without "SIP/"
	$agent_number=str_replace('SIP/','',$row_connect[3]);
	/// Connect Caller ID
	$row_get_cid_connect=mysql_fetch_array(mysql_query("SELECT `data` FROM $db_db.queue_log WHERE (`event` =  'ENTERQUEUE') AND (queuename LIKE 'queue_$queue') AND (`callid`='$row_connect[2]')"));
	$cid_connect = explode("|", $row_get_cid_connect[0]);
	/// Wait time Before Connect
	$wait_time_connect = explode("|", $row_connect[1]);
	///Detail Statistics
	$data_detail.="<td class='$class' align='center'>$row_connect[0]</td>";
		$csv_data_2_[$i+1][$j++].=$row_connect[0];
	$data_detail.="<td class='$class' align='center'>$wait_time_connect[0]</td>";
		$csv_data_2_[$i+1][$j++].=$wait_time_connect[0];
	$data_detail.="<td class='$class' align='center'>$cid_connect[1]</td>";
		$csv_data_2_[$i+1][$j++].=$cid_connect[1];
	$data_detail.="<td class='$class' align='center'>$agent_number</td>";
		$csv_data_2_[$i+1][$j++].=$agent_number;
	$data_detail.="<td class='$class' align='center'>$call_duration_connect[1]</td>";
		$csv_data_2_[$i+1][$j++].=$call_duration_connect[1];
	$data_detail.="<td class='$class' align='center'>$agent_number $call_route</td>";
		$csv_data_2_[$i+1][$j++].="$agent_number $call_route";
	$data_detail.="<td class='$class' align='center'>$overall_call_duration</td>";
		$csv_data_2_[$i+1][$j++].=$overall_call_duration;
	$data_detail.="<td class='$class' align='center'><a href='/monitor/$row_connect[2].wav'>wav</a></td>";
//		$csv_data_2_[$i+1][$j++].=$row_connect[2].wav;
	$data_detail.="</tr>";
	$i++;
}
$data_detail.="</tbody>";
$data_detail.="</table>";
//$data_detail.="</center>";
//$data_detail.="</div>";

require_once ('../conf/db_disconnect.php');
echo $data_detail;
?>
<input class="button" type="button" onclick="getcsv('2')" value="Get a CSV File!">
<br><br>
<input class="button" type="button" onClick="window.print()" value="<?=$print_page?>"/>
<?
/// Abandoned CVS
if(isset($csv_data_1_) && $_GET['csv']==1)
{	
	$fp = fopen("../tmp/tmp_file_abandon_$store.csv", 'w');
	foreach ($csv_data_1_ as $fields) 
		fputcsv($fp, $fields);
	fclose($fp);
	echo '<script type="text/javascript">';
	echo "javascript:window.location='download_csv.php?store=abandon_$store';";
	echo '</script>';
}
/// Completed CVS
if(isset($csv_data_2_) && $_GET['csv']==2)
{	
	$fp = fopen("../tmp/tmp_file_complete_$store.csv", 'w');
	foreach ($csv_data_2_ as $fields) 
		fputcsv($fp, $fields);
	fclose($fp);
	echo '<script type="text/javascript">';
	echo "javascript:window.location='download_csv.php?store=complete_$store';";
	echo '</script>';
}
?>
