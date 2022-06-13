<?php
date_default_timezone_set("Asia/Tokyo");
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/config.php";
$aimJsonPath = __DIR__ . "/aonori.json";

use Carbon\Carbon;

$client = new Google_Client();
$client->setApplicationName("AONORI-CALENDAR");

//get: Google_Service_Calendar::CALENDAR_READONLY
//add: Google_Service_Calendar::CALENDAR_EVENTS
$client->setScopes(Google_Service_Calendar::CALENDAR_EVENTS);

$client->setAuthConfig($aimJsonPath);
//create service
$service = new Google_Service_Calendar($client);

$outputParams = [
  "orderBy" => "startTime",
  "singleEvents" => true,
  'timeMin' => date('c', strtotime("midnight first day of this month")),
];

foreach ($calendar_id as $calendar) {
  $results = $service->events->listEvents($calendar, $outputParams);
  $events = $results->getItems();

  foreach ($events as $array) {
    //echo var_dump($event->id);
    //delete events
    $event = new Google_Service_Calendar_Event();
    $event = $service->events->delete($calendar, $array->id);
  }
}
?>
