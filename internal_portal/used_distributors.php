<?
require_once('lid.php');
require_once('../general_functions/multiexplode.php');

$query_distributors="SELECT `distributors` FROM  Stores.List WHERE `name` LIKE '$storename'";
$result_distributors = mysql_query($query_distributors);
$delimeters=Array(",","=");
$distrs_tmp= multiexplode($delimeters,mysql_result($result_distributors,0,'distributors'));

/// Sorting Distributors by priority - output array - $distrs
for($i=0;$i<sizeof($distrs_tmp);$i++)
{
	$distributors[$i]=$distrs_tmp[$i][0];
	$distributors_priority[$i]=$distrs_tmp[$i][1];
}
asort($distributors_priority);
for($i=0;$i<sizeof($distributors);$i++)
{
	$distrs[$i]=$distributors[key($distributors_priority)];
	next($distributors_priority);
}
?>
