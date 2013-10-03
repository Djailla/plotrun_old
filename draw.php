<?php

require_once 'lib/include.php';

$show_raw = (isset($_GET['show_raw']) && $_GET['show_raw'] == 'on' ? true : false);
$show_avg = (isset($_GET['show_avg']) && $_GET['show_avg'] == 'on' ? true : false);
$show_bpm = (isset($_GET['show_bpm']) && $_GET['show_bpm'] == 'on' ? true : false);

date_default_timezone_set('Europe/Paris');
setlocale (LC_TIME, 'fr_FR');

if(isset($_GET['run_id'])) {
  session_start();

  if (isset($_SESSION['run_id']))
    $session_run_id = $_SESSION['run_id'];
  $run_id = $_GET['run_id'];

  if ($run_id == $session_run_id) {
    $run_data = $_SESSION['run_data'];
  }
  else {
    $nikePHP = $_SESSION['nikeplus'];
    $run_data = $nikePHP->getRunData($run_id);

    $_SESSION['run_id'] = $run_id;
    $_SESSION['run_data'] = $run_data;
  }
  
  draw($run_data, $show_avg, $show_raw, $show_bpm);
}

function draw ($run_data, $show_avg = true, $show_raw = false, $show_bpm = false)
{

  include ("lib/jpgraph/jpgraph.php");
  include ("lib/jpgraph/jpgraph_line.php");
  include ("lib/jpgraph/jpgraph_scatter.php");

  $raw_stack = array();
  $avg_stack = array();
  $bpm_stack = array();
  $time_stack = array();

  $dist = $run_data['DISTANCE'];

  $bpm = NULL;

  // $dist = explode(",", $distance_str);
  if (isset($run_data['HEARTRATE'])) {
    $bpm = $run_data['HEARTRATE'];
  }
  $array_count = count($dist);

  $graph = new Graph(750, 450, "auto");
  $graph->img->SetAntiAliasing();
  $graph->SetMarginColor('#F7F7F7'); 
  $graph->SetScale("intint", 0, 0, 0, $maxtime);
  $graph->xgrid->Show();

  $km_iterator = 1;
  for ($i = 0; $i < $array_count - 1; $i++) {
    $speed = ( $dist[$i+1] - $dist[$i] ) * 360;
    $time = $i / 6;
    array_push ($raw_stack, $speed);
    array_push ($time_stack, $time);
    if ( ($dist[$i+1] >= $km_iterator ) && ($dist[$i] < $km_iterator ) ) {
      $line = new PlotLine(VERTICAL, $time, "green", 1);
      $graph->AddLine($line);
      $km_iterator += 1;
    }
  }

  $last_valid_bpm = 0;

  for ($i = 0; $i < $array_count - 1; $i++) {
    array_push ($avg_stack, compute_avg($raw_stack, $i, $array_count - 1));
    if ($bpm != NULL) {
      $current_bpm = $bpm[$i];
      if ($current_bpm == 0)
        array_push ($bpm_stack, $last_valid_bpm);
      else {
        array_push ($bpm_stack, $current_bpm);
        $last_valid_bpm = $current_bpm;
      }
    }
  }

  $average = $dist[$array_count - 1] / ($array_count - 1) * 360;
  if($average == 0)
    $pace = 0;
  else
    $pace = 1 / $average * 60;

  $pace_minutes = intval($pace);
  $pace_sec = round (($pace - $pace_minutes)*60);
  $pace_seconds = sprintf("%02d", $pace_sec);
  $average = round($average, 2);

  $maxtime = round($time) + 1;

  $total_duration = sec2hms($run_data['total_duration']/1000);
  $total_distance = number_format($run_data['total_distance'], 2);
  $start_time = strftime('%A %d/%m/%Y %H:%M', strtotime($run_data['start_time']));

  // Add the title to the graph
  $graph->title->Set("Entrainement du ".$start_time);

  // Add the subtitle to the graph
  $subtitle  = "Vitesse moyenne : ".$average." km/h - ";
  $subtitle .= "Rythme : ".$pace_minutes."'".$pace_seconds."\"/ km\n";
  $subtitle .= "Distance totale : ".$total_distance." km - ";
  $subtitle .= "Duree totale : ".$total_duration;

  $graph->subtitle->Set($subtitle);
  $graph->subtitle->SetColor('darkred');

  $graph->xaxis->title->Set("Temps (minutes)");

  if ($show_raw or $show_avg) {
    $graph->yaxis->title->Set("Vitesse (km/h)");
  }
  else {
    $graph->yaxis->title->Set("BPM");
  }

  if ($show_raw) {
    $raw_plot=new LinePlot($raw_stack, $time_stack);
    $raw_plot->SetColor("blue");
    $raw_plot->SetLegend('Vitesse brute');

    $graph->Add($raw_plot);
  }
  if ($show_avg) {
    $avg_plot=new LinePlot($avg_stack, $time_stack);
    $avg_plot->SetColor("red");
    $avg_plot->SetWeight(40);
    $avg_plot->SetLegend('Vitesse moyenne');

    $graph->Add($avg_plot);
  }

  if ($show_bpm and ($bpm != NULL)) {
    $bpm_plot=new LinePlot($bpm_stack, $time_stack);
    $bpm_plot->SetColor("darkred");
    $bpm_plot->SetLegend('Pulsations/minutes');

    if ($show_raw or $show_avg) {
      $graph->SetMargin(45,45,0,50);
      $graph->SetYScale(0, 'lin');
      $graph->ynaxis[0]->title->Set("BPM");
      $graph->ynaxis[0]->SetColor('darkred');;

      $graph->AddY(0, $bpm_plot);
    }
    else {
      $graph->Add($bpm_plot);
    } 
  }

  if ( !$show_raw and !$show_avg and !$show_bpm  ) {
    $empty_plot = new LinePlot(array(0), array(0));
    $empty_plot->SetLegend('No Info');
    $graph->Add($empty_plot);
  }

  $graph->legend->SetLayout(LEGEND_HOR);
  $graph->legend->Pos(0.5, 0.99, "center", "bottom");

  // Display the graph
  $graph->Stroke();
}
?>