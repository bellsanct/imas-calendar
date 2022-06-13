<?php
require_once __DIR__ . "/config.php";


foreach ($prefixes as $query => $prefix) {
  require_once __DIR__ . "/phpQuery-onefile.php";
  $contents = file_get_contents("https://idolmaster-official.jp/schedule/?b=" . $query);
  $html = phpQuery::newDocument($contents)->find(".js-setSchedule__target");
  file_put_contents(__DIR__ . "/out-" . $prefix . ".txt", $html);
}
?>
