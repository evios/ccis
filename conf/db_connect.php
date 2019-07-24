<?
$db_con = mysql_connect($db_host, $db_user, $db_pass);
if (!$db_con)
{
        die('Could not connect: ' . mysql_error());
}
mysql_query ('SET NAMES UTF8');
?>
