<?
///
/// Define Variables
///
$date=date('Y-m-d');

$unixtime=strtotime($date);
$year_today=date('Y', $unixtime);
$month_today=date('m', $unixtime);
$day_today=date('d', $unixtime);
include 'graph/index.php';
?>

