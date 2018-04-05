<?php

$routeviews = file_get_contents('routeviews-rv2-20180330-0600.pfx2as');

preg_match_all('/([0-9]*.[0-9]*.[0-9]*.[0-9]).([0-9]*).([0-9]*)/', $routeviews, $matches, PREG_SET_ORDER, 0);

echo count($matches)." Subnets\n";

$list = array();

foreach ($matches as $match) {
  $list[$match[3]][] = $match[1]."/".$match[2];
}

echo count($list)." ASN's\n";

$slices = 4;
$output = array();

foreach ($list as $element) {
  if (count($element) == 1) {
    $output[] = $element[0];
  } else {
    $output[] = $element[rand(0,count($element)-1)];
  }
}

echo count($output)." generated Subnets\n";

$calc = floor(count($output) / $slices);

for ($i = 0; $i <= $calc *$slices; $i = $i + $calc) {
    echo $i."\n";
    echo $i + $calc."\n";
    $tmp = array_slice($output, $i, $calc);
    file_put_contents($i.'.txt', implode(PHP_EOL,$tmp));
}

echo "Slices generated.\n";

?>
