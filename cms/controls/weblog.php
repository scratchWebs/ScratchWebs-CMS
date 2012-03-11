<?
/*
 * Script assumes $weblog is set to an instance of swWebLog
*/
$weblog = (isset($weblog)) ? $weblog : null;
if (!isset($weblog)) throw new Exception('$weblog is not set to an instance of swWebLog');


?>

<h3><?= $weblog->weblog_name ?></h3>