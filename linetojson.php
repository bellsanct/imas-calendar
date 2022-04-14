<?php

$prefixes = ["765", "cg", "ml", "sc", "sm"];

foreach ($prefixes as $prefix) {
  $file = fopen(__DIR__ . "/out-" . $prefix . ".txt", "r");
  $array = [];
  $array_count = 0;

  if ($file) {
    while ($line = fgets($file)) {
      //各行を一旦配列化
      //全角2文字以上のスペースを排除
      $line = preg_replace("/(|  )/", "", $line);
      $array[$array_count] = $line;
      $array_count++;
    }
  }

  $plan_array = [];
  $date_count = 0;
  $date_flg = 0;

  // 行ごとの処理をなにかする
  foreach ($array as $val) {
    //echo $plan_count.$val."\n";
    if (strpos($val, "date") !== false) {
      $date = strip_tags($val);
      $date = str_replace("\n", "", $date);
      //数字だけ取り出す
      $date = preg_replace("/[^0-9]/", "", $date);
      //日送り
      if ($date_flg === 0) {
        $date_flg = 1;
      } else {
        $date_count++;
      }
      if ($date !== "") {
        $plan_array[$date_count]["date"][] = $date;
      } elseif (strpos($val, "text") !== false || strpos($val, "week") !== false) {
        $plan_array[$date_count]["date"][] = 1;
      } else {
        $plan_array[$date_count]["date"] = $plan_array[$date_count - 1]["date"];
      }
    } elseif (strpos($val, "tag") !== false) {
      //タグ
      $tag = strip_tags($val);
      $tag = str_replace("\n", "", $tag);
      $plan_array[$date_count]["tag"][] = $tag;
    } elseif (strpos($val, "title") !== false) {
      //作品タイトル
      $title = strip_tags($val);
      $title = str_replace("\n", "", $title);
      if ($title !== "") {
        $plan_array[$date_count]["title"][] = $title;
      }
    } elseif (strpos($val, "text") !== false) {
      //イベント名
      $text = strip_tags($val);
      $text = str_replace("\n", "", $text);
      if (strlen($text) > 1) {
        $plan_array[$date_count]["text"][] = $text;
      }
    } elseif (strpos($val, "schedule") !== false) {
      //スケジュール
      $schedule = strip_tags($val);
      $schedule = str_replace("\n", "", $schedule);
      $plan_array[$date_count]["schedule"][] = $schedule;
    }
    if (strpos($val, "href") !== false) {
      // URL抽出
      preg_match_all("(https?://[-_.!~\'()a-zA-Z0-9;/?:@&=+$,%#]+)", $val, $matches);
      if ($matches[0] !== "") {
        $plan_array[$date_count]["link"] = $matches[0];
      }
    }
  }

  //最終的な配列をjson化
  $json_array = json_encode($plan_array);

  file_put_contents(__DIR__ . "/result-" . $prefix . ".json", $json_array);

  //file close
  fclose($file);
}
?>
