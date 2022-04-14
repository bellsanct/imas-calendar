<?php
exec("php " . __DIR__ . "/delete.php");
sleep(1);
exec("php " . __DIR__ . "/get.php");
sleep(1);
exec("php " . __DIR__ . "/linetojson.php");
sleep(1);
exec("php " . __DIR__ . "/calendar.php");
?>
