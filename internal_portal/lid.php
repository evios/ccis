<?
//if(!$operator)
if(!$operator)
	$operator=$_GET["operator"];
$name="";
//echo $operator;

//!!! znak doljen bit ">"
$query_queue="SELECT `queuename`,`time` FROM  asterisk.queue_log WHERE `time` < (SELECT `time` FROM asterisk.queue_log WHERE `agent` LIKE 'SIP/$operator' AND  (`event` LIKE 'COMPLETECALLER' OR `event` LIKE 'COMPLETEAGENT') ORDER BY TIME DESC LIMIT 1) AND `event` LIKE 'ENTERQUEUE' AND `callid` LIKE (SELECT `callid` FROM asterisk.queue_log WHERE `agent` LIKE 'SIP/$operator' AND `event` LIKE 'CONNECT' ORDER BY TIME DESC LIMIT 1) ORDER BY TIME ASC LIMIT 1";
$result_queue = mysql_query($query_queue);
//mysql_result($result_queue,0,'queuename');
$storename=substr(mysql_result($result_queue,0,'queuename'),6); //remove "queue_"
?>
