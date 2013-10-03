<?php

require_once 'lib/include.php';

session_start();

if (isset($_SESSION['nikeplus'])) {
  $n = $_SESSION['nikeplus'];
}
else {
  if (!(isset($_POST["nike_user"]) or isset($_POST["nike_pass"]))) {
    return;
  }
  $n = new NikePlusPHPPlotrun($_POST["nike_user"], $_POST["nike_pass"]);
}

$run_info = $n->latest();

if ($run_info != false) {
  # Save the session
  $_SESSION['nikeplus'] = $n;

  echo '<b>Dernier entrainement</b><br/>';
  echo '<a href="result.php?run_id='.$run_info[runId].'">'.$run_info[time_str].' : '.$run_info[distance].' km - '.$run_info[duration].'</a><br/><br/>';
  echo '<a href="more.php">Historique des entrainements</a><br/>';
  echo "<br/><input type='button' name='logout' value='Logout' onclick=\"self.location.href='logout.php'\">";
}
else {
  echo "<br/>BAD LOGIN / PASSWORD";
  echo "<br/><input type='button' name='logout' value='Retry' onclick=\"self.location.href='logout.php'\">";
}

?>    