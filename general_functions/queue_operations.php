<?
require_once('../general_functions/phpagi/phpagi-asmanager.php');
function queue_remove($operator) /// RemoveQueueMember(queue_test,SIP/${EXTEN:1})
{
	$result = mysql_query("SELECT name FROM Stores.List");
	while($row = mysql_fetch_array($result))
	{
		$asm_rem = new AGI_AsteriskManager();
		if($asm_rem->connect())
		{
			$logout = $asm_rem->send_request('QueueRemove',
			array('Queue'=>"queue_{$row['name']}",
				'Interface'=>"SIP/$operator"));
			$logout = $asm_rem->send_request('QueueRemove',
			array('Queue'=>"queue_{$row['name']}_$operator",
				'Interface'=>"SIP/$operator"));
			$asm_rem->disconnect();
		}
	}
}

function queue_add($operator) /// AddQueueMember(queue_test,SIP/${EXTEN:1})
{
	$result = mysql_query("SELECT name FROM Stores.List");
	while($row = mysql_fetch_array($result))
	{
		$asm_add = new AGI_AsteriskManager();
		if($asm_add->connect())
		{
			$login = $asm_add->send_request('QueueAdd',
			array('Queue'=>"queue_{$row['name']}",
				'Interface'=>"SIP/$operator"));
			$login = $asm_add->send_request('QueueAdd',
			array('Queue'=>"queue_{$row['name']}_$operator",
				'Interface'=>"SIP/$operator"));
			$asm_add->disconnect();
		}
	}
}

function phone_status($operator)
{
	$asm_add = new AGI_AsteriskManager();
	if($asm_add->connect())
	{
		$state = $asm_add->send_request('ExtensionState',
		array('Exten'=>"$operator",
			'Context'=>"users"));
		$asm_add->disconnect();
	}
	return $state['Status'];
}


?>
