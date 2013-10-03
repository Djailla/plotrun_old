<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>PlotRun - Nike+ Alternative</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="styles/style.css" type="text/css" />
  <link rel="stylesheet" href="styles/template.css" type="text/css" />
  <link rel="stylesheet" href="styles/wait.css" type="text/css" />
  <script type='text/javascript'>
  
  function draw_run(){
    draw_image = document.getElementById('draw_image');
    run_id = document.getElementById('run_id');

    img_src = 'draw.php?run_id=' + run_id.value;
    if(document.getElementById('show_raw').checked) {
      img_src = img_src + '&show_raw=on'
    }

    if(document.getElementById('show_avg').checked) {
      img_src = img_src + '&show_avg=on'
    }

    if(document.getElementById('show_bpm').checked) {
      img_src = img_src + '&show_bpm=on'
    }

    draw_image.src = img_src;
  }

  function export_run(){
    run_id = document.getElementById('run_id');
    self.location.href = 'export.php?run_id=' + run_id.value;
  }

  </script>
</head>

<body onload='draw_run();'>
<?php include ("template/feedback.php"); ?>
  <div id="dvmaincontainer">
    <?php include ("template/topcontainer.php"); ?>
    <div id="dvbodycontainer">
      <div id="dvbannerbgcontainer" align="center">
        <img id='draw_image' src='images/blank.png'><br/><br/>
        <input type="checkbox" id="show_raw" onclick='draw_run()' />&nbsp;Vitesse brute&nbsp;&nbsp;&nbsp;
        <input type="checkbox" id="show_avg" onclick='draw_run()' CHECKED />&nbsp;Vitesse liss&eacute;e&nbsp;&nbsp;&nbsp;
        <input type='checkbox' id='show_bpm' onclick='draw_run()'/>&nbsp;BPM
        <?php

        session_start();
        if (isset($_SESSION['run_data'])) {
          // if (isset($_SESSION['run_data']['HEARTRATE'])) {
          //   print "<input type='checkbox' id='show_bpm' onclick='draw_run()'/>BPM";
          // }
          if ($_SESSION['run_data']['GPS']) {
            print "<br/><br/><input type='button' name='gpx_export' value='Export GPX' onclick='export_run()'>";
          }
        }

        ?>
        <input type="hidden" id="run_id" value='<? echo $_GET["run_id"];?>'/>
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