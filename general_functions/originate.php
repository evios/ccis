<?
$operator=$_GET['operator'];
$customerPhone=$_GET['customerPhone'];
$storename=$_GET['storename'];
originate($operator,$customerPhone,$storename);

function originate($operator,$customerPhone,$storename)
{
	require_once('../general_functions/phpagi/phpagi-asmanager.php');
//	echo $operator;
//	echo $customerPhone;
	$remote_context='outgoing';
//	$local_context='users';
	$local_context='incoming';
	$callerID='outbound';

	$asm = new AGI_AsteriskManager();
	if($asm->connect())
	{
		$call = $asm->send_request('Originate',
		array('Channel'=>"LOCAL/$storename$operator@$local_context",
			'Context'=>"$remote_context",
			'Priority'=>1,
			'CallerID'=>"$customerPhone",
			'Exten'=>"$customerPhone"));
		$asm->disconnect();
	}
}
?>
