<?php

$dir = 'data';

$empedid = $_POST['empedid'];
$dir_path = $dir.'/'.$empedid;

$doc = new DOMDocument("1.0");
$run_list = $doc->createElement('run_list');
$doc->appendChild($run_list);

if(isset($empedid) && is_dir($dir_path) ) {
  if($dhemped = opendir($dir_path)) {
    while(( $file = readdir($dhemped)) != false ) {
      if(is_file( $dir_path.'/'.$file ) ){
        $file_url = $dir_path.'%2F'.str_replace('/','%2F',str_replace(' ','+',str_replace(';','%3B',$file)));

        $run_node = $doc->createElement('run_node');
        $newnode = $run_list->appendChild($run_node);
        $newnode->appendChild(new DOMText($file));
        $newnode->setAttribute("file_url", $file_url);
      }
    }
  }
}
header('Content-Type: text/xml');
echo $doc->saveXML();

?>