<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
  <title>PlotRun - Nike+ Alternative</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="styles/style.css" type="text/css" />
  <link rel="stylesheet" href="styles/template.css" type="text/css" />
  <link rel="stylesheet" href="styles/wait.css" type="text/css" />
  <script type='text/javascript' src="ajax.js"></script>
  <script type='text/javascript'>
  function main_load(){
    var xhr = getXhr();

    xhr.onreadystatechange = function(){
      if(xhr.readyState == 4 && xhr.status == 200){
        leselect = xhr.responseText;
        document.getElementById('nike_run').innerHTML = leselect;
        document.getElementById("message").className="wait_invisible";
      }
    }

    document.getElementById("message").className="wait_visible";

    xhr.open("POST", "ajax_main.php", true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.send();
  }

  function go(form){
    var xhr = getXhr();

    xhr.onreadystatechange = function(){
      if(xhr.readyState == 4 && xhr.status == 200){
        leselect = xhr.responseText;
        document.getElementById('nike_run').innerHTML = leselect;
        document.getElementById("message").className="wait_invisible";
      }
    }

    document.getElementById("message").className="wait_visible";

    xhr.open("POST", "ajax_run.php", true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

    nike_user = form.nike_user.value;
    nike_pass = form.nike_pass.value;
    xhr.send("nike_user=" + nike_user + "&nike_pass=" + nike_pass);
  }
  </script>
</head>


<body onload="main_load();">
<?php include ("template/feedback.php"); ?>
  <div id="dvmaincontainer">
    <?php include ("template/topcontainer.php"); ?>
    <div id="dvbodycontainer">
      <div id="dvbannerbgcontainer" align="center">
        <div id='nike_run' style='display:inline'>
        </div>
      </div>
      <div id="dvrightpanel">
        <div>
          <div class="wait_invisible" id="message">Veuillez patienter... <img src="images/wait.gif"></div>
          <br/><input type='button' name='logout' value='Reset' onclick="self.location.href='logout.php'">
        </div>
      </div>
    </div>
    <?php include ("template/footercontainer.php"); ?>
  </div>
<?php include ("template/analytics.php"); ?>
</body>
</html>
