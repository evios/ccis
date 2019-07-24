<?php
error_reporting(E_ALL);

require_once "ConnectErc.php";

$login = "ei@osis.com.ua";
$passw = "osis";

$proc = new ConnectErc("./");//cookie file path with trailing slash

$proc->authorize($login,$passw);
$proc->btexportprice();
?>
