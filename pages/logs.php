
<?php

$logsAll = $log->get(); // Assuming $logsClass is your Logs object
?>

<h1>Logs</h1>
<?php
echo $log->table($logsAll);
?>