<?
require_once('../general_functions/phpagi/phpagi-asmanager.php');
//$operator=$_GET['operator'];
//$operator="503";
//$search_str = getCallerID($operator);
$search_str = '2061320';
$callID=getCallID($search_str);
redirectCall($callID);

function parse($data,$search_str,$result_lenght) // Parsing nujnix dannix, ID zvonka i ID Channel
{
$start_pos=strrpos($data, $search_str)+strlen($search_str);
$result=substr(trim(substr($data, $start_pos)),0,$result_lenght);
return $result;
}

function redirectCall($callID) // Perenapravlenie zvonka na konsultanta
{
	$asm = new AGI_AsteriskManager();
	if($asm->connect())
	{
		/// GET channelID
		$call = $asm->send_request('Command',
		array('Command'=>"sip show channel $callID"));
		$search_str="Owner channel ID:";
		$result_lenght = 15;
		$cannelID=parse($call['data'],$search_str,$result_lenght);

		// Redirect Call
		$call = $asm->send_request('redirect',
		array('Channel'=>$cannelID,
		'Exten'=>"0634566228",
		'Context'=>"outgoing",
		'Priority'=>"1",));

		$asm->disconnect();
	}
}

function getCallID($search_str) // ID zvonka dlia zapisi v BD i dlia perenapravleniya zvonka na konsultanta
{
	$asm = new AGI_AsteriskManager();
	if($asm->connect())
	{
		/// GET callID
		$call = $asm->send_request('Command',
		array('Command'=>"sip show channels"));
		$result_lenght = 15;
		$callID=parse($call['data'],$search_str,$result_lenght);
		return $callID;
	}
}

function getCallerID($operator)
{
require_once('../conf/db_vars.php');
require_once('../conf/db_connect.php');
//!!! znak doljen bit ">"
$query_number="SELECT data FROM  asterisk.queue_log WHERE `time` < (SELECT `time` FROM asterisk.queue_log WHERE `agent` LIKE 'SIP/$operator' AND  (`event` LIKE 'COMPLETECALLER' OR `event` LIKE 'COMPLETEAGENT') ORDER BY TIME DESC LIMIT 1) AND `event` LIKE 'ENTERQUEUE' AND `callid` LIKE (SELECT `callid` FROM asterisk.queue_log WHERE `agent` LIKE 'SIP/$operator' AND `event` LIKE 'CONNECT' ORDER BY TIME DESC LIMIT 1) ORDER BY TIME ASC LIMIT 1";
$result_number = mysql_query($query_number);
$callerID=substr(mysql_result($result_number,0,'data'),1); //remove "|"
require_once ('../conf/db_disconnect.php');
return $callerID;
}
?>
