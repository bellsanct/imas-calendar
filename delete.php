<?php
date_default_timezone_set("Asia/Tokyo");
require_once __DIR__ . "/vendor/autoload.php";
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

//calendar id
$calendar_id = ["e5misl0u751hbcjb05pmrb9grc@group.calendar.google.com", "bt23tob13vcs7b031ek6v1plos@group.calendar.google.com", "0sdrrc665qu1rtql8bvr3jhfc4@group.calendar.google.com", "ggplrmmqnhclf314i15fvp4m4g@group.calendar.google.com", "9m2nlm3s1s1av97ol0qlffahng@group.calendar.google.com"];

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
