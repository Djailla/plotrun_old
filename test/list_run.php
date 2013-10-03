<?php

require_once '../lib/include.php';
require_once 'info.php';

$run_info = $n->activities(5);

print_r($run_info);

foreach ($run_info as &$value) {
  print "AAAAAAA<br/>";
  // foreach ($run_info2 as &$value2) {
  print_r($value->activityId);
  print "<br/>";
  print_r($value->name);
  print "<br/>";
  //   print "BBBBBB<br/><br/><br/><br/><br/>";
  // }
}

?>