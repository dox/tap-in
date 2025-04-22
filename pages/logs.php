<?php
//clear out old logs
$log->purge();

$logsAll = $log->get(); // Assuming $logsClass is your Logs object
?>

<h1><?php echo icon('search', '1em'); ?> Logs</h1>
<?php
echo $log->table($logsAll);
?>