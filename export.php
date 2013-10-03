<?php

require_once 'lib/include.php';

$run_id = $_GET['run_id'];

session_start();

if (isset($_SESSION['nikeplus']))
  $n = $_SESSION['nikeplus'];
else
  echo '<srcipt>alert("Session expired"); </srcipt>';

$run = $n->run($run_id); 

if($run === NULL) {
  return false;
}

Header("Content-type: application/gpx");
Header("Content-Disposition: attachment; filename=".$run_id.".gpx");
print $n->toGpx($run);

?>