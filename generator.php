<?php

function fetchFile($url) {
  $result = array();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  $result['content'] = curl_exec($ch);
  $result['http'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  return $result;
}

$list = array();

//Fetch TOR
for ($retry = 0; $retry <= 10; $retry++) {
    echo "Fetching TOR List\n";
    $torList = fetchFile("https://check.torproject.org/exit-addresses");
    if ($torList['http'] == 200) {
      preg_match_all('/ExitAddress ([0-9]*.[0-9]*.[0-9]*.[0-9]*)/', $torList['content'], $matches, PREG_SET_ORDER, 0);
      foreach ($matches as $match) {
        $list[] = $match[1]."/32";
      }
      break;
    } else {
      echo "Failed to fetch TOR list\n";
      sleep(rand(300, 900));
    }
}

//Fetch Spamhaus
for ($retry = 0; $retry <= 10; $retry++) {
    echo "Fetching Spamhaus List\n";
    $spamhausList = fetchFile("https://www.spamhaus.org/drop/drop.lasso");
    if ($spamhausList['http'] == 200) {
      preg_match_all('/([0-9]*.[0-9]*.[0-9]*.[0-9]*\/[0-9]*) ;/', $spamhausList['content'], $matches, PREG_SET_ORDER, 0);
      foreach ($matches as $match) {
        $list[] = $match[1];
      }
      break;
    } else {
      echo "Failed to fetch Spamhaus list\n";
      sleep(rand(300, 900));
    }
}

file_put_contents('blocklist.txt', implode(PHP_EOL,$list));

?>
