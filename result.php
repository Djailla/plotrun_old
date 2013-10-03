<?php

require_once 'lib/include.php';

session_start();

if (isset($_SESSION['nikeplus'])) {
  $n = $_SESSION['nikeplus'];
}
else {
  return;
}

if(isset($_GET['run_id'])) {
  $run_id = $_GET['run_id'];
  $run_data = $n->get_run_data($run_id);
  // print_r($run_data);

}
else {
  print "Missing run_id";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>PlotRun - Nike+ Alternative</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--   <link rel="stylesheet" href="styles/style.css" type="text/css" />
  <link rel="stylesheet" href="styles/template.css" type="text/css" />
  <link rel="stylesheet" href="styles/wait.css" type="text/css" /> -->
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script type="text/javascript">

function export_run(run_id){
  self.location.href = 'export.php?run_id=' + run_id;
}

function rectime(secs) {
  var hr = Math.floor(secs / 3600);
  var min = Math.floor((secs - (hr * 3600))/60);
  var sec = secs - (hr * 3600) - (min * 60);

  if (hr < 10) {hr = "0" + hr; }
  if (min < 10) {min = "0" + min;}
  if (sec < 10) {sec = "0" + sec;}
  if (!hr) {hr = "00";}
  return hr + ':' + min + ':' + sec;
}

$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                zoomType: 'xy',
                type: 'line',
                marginTop: 80
            },
            title: {
                text: <?php print "'".$run_data['title']."'";?>
            },
            subtitle: {
                text: <?php print $run_data['subtitle'];?>
            },
            xAxis: {
                title: {
                    text: 'Durée (s)',
                },
                tickInterval: 600,
                gridLineWidth: 1,
            },
            yAxis: [
                {
                    title: {
                        text: 'Vitesse (km/h)'
                    },
                },
                <?php if(isset($run_data['bpm'])) print "
                {
                    title: {
                        text: 'Fréquence cardiaque (puls/min)'
                    },
                    min : 20,
                    id : 1,
                    opposite: true,
                    enabled: false,
                },"
                ;?>
            ],
            tooltip: {
                enabled: true,
                formatter: function() {
                    if (this.series.name == 'Fréquence cardiaque') {
                        return '<b>'+ this.series.name +'</b><br/>'+
                            rectime(this.x)+ ' - '+ this.y +' BPM';
                    }
                    else {
                        return '<b>'+ this.series.name +'</b><br/>'+
                            rectime(this.x)+ ' - '+ this.y.toFixed(2) +'km/h';    
                    }
                }
            },
            legend: {
                enabled: true
            },
            plotOptions: {
                line: {
                    marker: {
                        enabled: false,
                    },
                    enableMouseTracking: true
                }
            },
            credits: {
                enabled: false
            },
            series: [<?php print $run_data['serie'];?>]
        });
    });
    
});
  </script>
</head>

<body>
<script src="lib/highcharts/js/highcharts.js"></script>
<script src="lib/highcharts/js/modules/exporting.js"></script>
<!-- <?php include ("template/feedback.php"); ?> -->
  <!-- <div id="dvmaincontainer"> -->
    <!-- <?php include ("template/topcontainer.php"); ?> -->
    <!-- <div id="dvbodycontainer"> -->
      <!-- <div id="dvbannerbgcontainer" align="center"> -->
        <div id="container" style="width: 1200px; height: 1000px; margin: 0 auto"></div>
        <?php

        if (isset($run_data['gps'])) {
            print "<br/><input type='button' name='gpx_export' value='Export GPX' onclick='export_run(".$run_id.")'>";
        }
        ?>
      </div>
      <div id="dvrightpanel">
        <div>
          <input type="button" value="Retour" onclick="history.back()"/>
        </div>
        <br/><center><a href="http://action.metaffiliation.com/suivi.php?mclic=S4572B52EEE11411" target="_blank"><img src="http://action.metaffiliation.com/suivi.php?maff=S4572B52EEE11411" border="0"></a></center>
      </div>
    <!-- </div> -->
    <!-- <?php include ("template/footercontainer.php"); ?> -->
  </div>
<?php include ("template/analytics.php"); ?>
</body>
</html>