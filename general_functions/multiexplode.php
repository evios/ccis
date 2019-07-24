<?
function multiexplode ($delimiters,$string)
{
	$ary = explode($delimiters[0],$string);
	array_shift($delimiters);
	if($delimiters != NULL)
	{
		foreach($ary as $key => $val)
		{
			$ary[$key] = multiexplode($delimiters, $val);
		}
	}
	return  $ary;
}
?>
