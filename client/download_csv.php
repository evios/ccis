<?
$store=$_GET['store'];
$file="../tmp/tmp_file_$store.csv";
if (file_exists($file))
{
	header('Content-Description: File Transfer');
	header('Content-Type: application/csv'); 
	header("Content-disposition: attachment; filename=".date("Y-m-d")."_$store.csv");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	readfile($file);
}
exit;
?>
