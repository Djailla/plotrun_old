<?php

require_once '../lib/include.php';
require_once 'info.php';

$run_info = $n->get_basic_info();

foreach ($run_info as &$value) {
  print "<br/>---<br/>";
  // foreach ($run_info2 as &$value2) {
  print_r($value[runId]);
  print "<br/>";
  print_r($value[startTime]);
  print "&nbsp;&nbsp;&nbsp;:";
  print_r($value[distance]);
  print "<br/>";
}

?>