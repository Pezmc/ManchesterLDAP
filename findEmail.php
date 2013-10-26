<?php
ini_set('display_errors', 1); 
ini_set('log_errors', 1); 
ini_set('error_log', dirname(__FILE__) . '/error_log.txt'); 
error_reporting(E_ERROR);
    
require("lib/pegLDAP.class.php");
require("lib/uomLDAP.class.php");
require("lib/uomPersonQuery.class.php");

/* Search box, possible auto suggest, searching for name & id only */
if(isset($_POST['query'])&&!empty($_POST['query'])) {
  header("Location: ./?q=".strip_tags($_POST['query']));
}

if(isset($_GET['q'])&&!empty($_GET['q'])) {
  //Can we find the person
  $query = strip_tags(trim($_GET['q']));
  $con = new uomPersonQuery("ou=People", false); 
  $peopleFound = $con->searchPeople($query);
  if(count($peopleFound)-1==1) { 
    include("templates/result.php");
  } elseif(count($peopleFound)-1>1) {
    include("templates/results.php");
  } else {
    $error = "Oh dear, I couldn't find anyone by the name or id ".$query;
    include("templates/form.php");
  }
} else {
  include("templates/form.php");
}

?>