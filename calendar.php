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
$calendar_ids = [
  "765" => "e5misl0u751hbcjb05pmrb9grc@group.calendar.google.com",
//  "cg" => "bt23tob13vcs7b031ek6v1plos@group.calendar.google.com",
  "ml" => "0sdrrc665qu1rtql8bvr3jhfc4@group.calendar.google.com",
  "sc" => "ggplrmmqnhclf314i15fvp4m4g@group.calendar.google.com",
  "sm" => "9m2nlm3s1s1av97ol0qlffahng@group.calendar.google.com",
];

//add events
foreach ($calendar_ids as $prefix => $calendar) {
  $json = file_get_contents(__DIR__ . "/result-" . $prefix . ".json");
  $json = mb_convert_encoding($json, "UTF-8");
  $array = json_decode($json, true);

  foreach ($array as $value) {
    //$value contained: date, link, tag, schedule, text, title

    //scheduleが時間形式かどうか判断
    if (strpos($value["schedule"][0], ":") !== false) {
      $summary = $value["text"][0];
      $description = $value["text"][0] . "\n" . $value["link"][0];
      $time = explode(":", $value["schedule"][0]);
      $hour = $time[0];
      $minute = $time[1];

      //分に「～」がある場合
      if (strpos($minute, "～") !== false) {
        //分の部分だけ抜き出す
        $minute = mb_substr($minute, 0, 2);
      }

      //分に「~」がある場合
      if (strpos($minute, "~") !== false) {
        //分の部分だけ抜き出す
        $minute = mb_substr($minute, 0, 2);
      }

      //分に「開場」がある場合
      if (strpos($minute, "開場") !== false) {
        //分の部分だけ抜き出す
        $minute = mb_substr($minute, 0, 2);
      }

      //分に「開始」がある場合
      if (strpos($minute, "開始") !== false) {
        //分の部分だけ抜き出す
        $minute = mb_substr($minute, 0, 2);
      }

      //時に「AM」がある場合
      if (strpos($hour, "AM") !== false) {
        //分の部分だけ抜き出す
        $hour = mb_substr($hour, 2, 3);
      }

      //時に「PM」がある場合
      if (strpos($hour, "PM") !== false) {
        //分の部分だけ抜き出す
        $hour = mb_substr($hour, 2, 3);
        (int)$hour += 12;
      }


      $start = Carbon::create(date("Y"), date("m"), $value["date"][0], $hour, $minute);
      $start = $start->format("c");
      $end = Carbon::create(date("Y"), date("m"), $value["date"][0], $hour, $minute);
      $end = $end->format("c");
    } elseif (preg_match("/[0-9]{1,2}[!-~][0-9]{1,2}～[0-9]{1,2}[!-~][0-9]{1,2}/", $value["schedule"][0])) {
      $summary = $value["text"][0];
      $description = $value["text"][0] . "\n" . $value["link"][0];
      // m/dd～m/dd形式
      // ～でstart end 分割
      if (strpos($value["schedule"][0], "～") !== false) {
        //分の部分だけ抜き出す
        $dates = explode("～", $value["schedule"][0]);
        $start_date = explode("/", $dates[0]); // x/xx
        $end_date = explode("/", $dates[1]); // x/xx
        $start = Carbon::create(date("Y"), $start_date[0], $start_date[1], 10, 0);
        $start = $start->format("c");
        $end = Carbon::create(date("Y"), $end_date[0], $end_date[1], 10, 0);
        $end = $end->format("c");
      }
    } elseif (strpos($value["schedule"][0], "発売日") !== false) {
      // xx:xx のフォーマットじゃないもの　例) 発売日
      $summary = "[発売日]" . $value["text"][0];
      $description = $value["text"][0] . "\n" . $value["link"][0];
      //echo $value["schedule"][0];
      //暫定処置として 10:00で登録
      //
      $start = Carbon::create(date("Y"), date("m"), $value["date"][0], 10, 0);
      $start = $start->format("c");
      $end = Carbon::create(date("Y"), date("m"), $value["date"][0], 10, 0);
      $end = $end->format("c");
    } else {
      //発売日ではない
      $summary = $value["text"][0];
      $description = $value["text"][0] . "\n" . $value["link"][0];
      //echo $value["schedule"][0];
      //暫定処置として 10:00で登録
      //
      $start = Carbon::create(date("Y"), date("m"), $value["date"][0], 10, 0);
      $start = $start->format("c");
      $end = Carbon::create(date("Y"), date("m"), $value["date"][0], 10, 0);
      $end = $end->format("c");
    }


    //create events
    $event = new Google_Service_Calendar_Event([
      "summary" => $summary,
      "description" => $description,
      "start" => [
        "dateTime" => $start,
        "timeZone" => "Asia/Tokyo",
      ],
      "end" => [
        "dateTime" => $end,
        "timeZone" => "Asia/Tokyo",
      ],
    ]);
    $event = $service->events->insert($calendar, $event);
  }
}
?>
