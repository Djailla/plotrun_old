<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>PlotRun - Nike+ Alternative</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="styles/style.css" type="text/css" />
  <link rel="stylesheet" href="styles/template.css" type="text/css" />
  <link rel="stylesheet" href="styles/wait.css" type="text/css" />
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>

<body>
<?php include("template/feedback.php"); ?>
  <div id="dvmaincontainer">
    <?php include ("template/topcontainer.php"); ?>
    <div id="dvbodycontainer">
      <div id="dvbannerbgcontainer" align="center">

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

$global_info = $n->allTime();

if ($global_info != false) {
  # Save the session
  $_SESSION['nikeplus'] = $n;
  // echo "<b>Total : ".number_format($global_info->lifetimeTotals->distance, 2)." km</b><br/>";
}
else {
  echo "<br/>BAD LOGIN / PASSWORD";
  echo "<br/><input type='button' name='logout' value='Retry' onclick=\"self.location.href='logout.php'\">";
  return;
}

$count = 20;

if(isset($_GET['index'])) {
  $index = $_GET['index'];
  $run_info = $n->get_basic_info($index, $count);
}
else {
  $index = 0;
  $run_info = $n->get_basic_info(0, $count);
}

if ($run_info != false) {
  echo '<div class="CSSTableGenerator">';
  echo '<table><tr><th colspan="4" text-align="left">'.$n->userId.'</th><th text-align="right">Lien</th></tr>';
  foreach ($run_info as &$value) {
    echo '<tr>';
    echo '<td><i>'.$value[time_str].'</i></td>';
    echo '<td><b>'.$value[distance].' km</b></td>';
    echo '<td>'.$value[duration].'</td>';


    echo '<td>';
    # Display GPS icon
    if ($value[gps] == 1) {
      echo '<img src="images/gps_mini.png" alt="GPS" width="20" height="20"/>';
    }

    # Display HeartRate icon
    if ($value[heartrate] == 1) {
      echo '<img src="images/heart.png" alt="Cardio" width="20" height="20"/>';
    }
    echo '</td>';

    # Disply link
    echo '<td><a href="result.php?run_id='.$value[runId].'">Graphe</a></td>';

    echo '</tr>';
  }
  echo '</table></div><br/>';

  if($index == 0) {
    $disabled = " disabled='disabled'";
  }
  else {
    $disabled = '';
  }

  echo "<input type='button' name='newer' value='Precedent' onclick=\"self.location.href='more.php?index=".($index - $count)."'".$disabled."\">&nbsp;&nbsp;&nbsp;";
  echo "<input type='button' name='older' value='Suivant' onclick=\"self.location.href='more.php?index=".($index + $count)."'\">";

  echo "</br><br/><input type='button' name='logout' value='Logout' onclick=\"self.location.href='logout.php'\">";
}
else {
  echo "<br/>BAD LOGIN / PASSWORD";
  echo "<br/><input type='button' name='logout' value='Retry' onclick=\"self.location.href='logout.php'\">";
}

?>
      </div>
      <div id="dvrightpanel">
        <div>
          <input type="button" value="Retour" onclick="history.back()"/>
        </div>
      </div>
    </div>
    <?php include ("template/footercontainer.php"); ?>
  </div>
<?php include ("template/analytics.php"); ?>
</body>
</html>

