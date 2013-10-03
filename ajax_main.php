<?php

session_start();

if (isset($_SESSION['nikeplus'])) {
  require_once 'ajax_run.php';
}
else {
  echo "
  <b>Connectez vous :</b><br/><br/>
  <form>
    E-Mail : <input type='text' name='nike_user' size='20'><br>
    Mot de passe : <input type='password' name='nike_pass' size='17'><br><br>
    <input type='button' value='GO' onClick='go(this.form);'>
  </form>";
}

?>