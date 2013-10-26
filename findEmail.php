<?php
ini_set('display_errors', 1); 
ini_set('log_errors', 1); 
ini_set('error_log', dirname(__FILE__) . '/error_log.txt'); 
error_reporting(E_ERROR);
    
//Stuff UoM ldap appears to return:
/*
    [cn][0] => Pez Cuckow
    [sn][0] => Cuckow
    [displayname][0] => Pez Cuckow
    [givenname][0] => Pez
    [umanprimaryou][0] => School of Computer Science
    [umanorgunitid][0] => 3060
    [umanprimarydiv][0] => School of Computer Science
    [ou] =>
            [0] => School of Computer Science
            [1] => Faculty of Engineering & Physical Sciences
    [telephonenumber][0] => School of Computer Science
    [umantelephonenumberallow][0] => 1
    [umanroomnumberallow][0] => 1
    [umanindirectory][0] => 1
    [title] => Array
            [0] => Undergraduate
    [mail] => Array
            [0] => pez.cuckow@student.manchester.ac.uk
    [employeetype]
            [0] => Undergraduate
    [edupersonaffiliation]
            [0] => student
            [1] => member
    [edupersonscopedaffiliation]
            [0] => student@manchester.ac.uk
            [1] => member@manchester.ac.uk
    [edupersonentitlement] - A horrible mess, ignored
    [umanmst][0] => CS email - UK-AC-MAN-CS-FS6 - Fuck knows
    [umanbarcode][0] => 75650250 - Barcode Number - Student Number
    [umanmagstripe][0] => 900075650250 - Barcode Strip
    [umanlegacyemailallow][0] => 0 - Does legacy email work I guess
    [edupersonprincipalname][0] => 50297445@manchester.ac.uk - No idea?!?
    [labeleduri][0] => http://personalpages.manchester.ac.uk/student/pez.cuckow - Web Page NOT for CS
    [umaniconstatus][0] => 0 - No idea
    [umanpersonid][0] => 7565025 - Your ID number, awesome!
    [umanstudentprogramofstudy][0] = 0067600553 - Course ID of some sort?!?
    [umanstudentyearofstudy][0] = 01 - Year of Study
    [umanroleid][0] = 264765 - Fuck Knows
    [dn] - Long string of stuff
*/ 

/* Generic LDAP Class */
class pegLDAP {
  private $lastSearch;
  private $connection;
  private $binding;
  private $address;
  private $timeLimit;
  private $globalLimit;
  
  /*
   * Constructor
   */
  public function __construct($address=false, $timelimit = 0, $globallimit = 0){
      //Connect
      if($address) {
        $this->address = $address;
        $this->connection = ldap_connect($address);
      }
      if(!$this->connection) die("Could not connect to ".($address ? $address : "server"));
      else {
        $this->binding = ldap_bind($this->connection);
      }
      if($this->binding) {
        $this->timeLimit = $timelimit;
        $this->globalLimit = $globallimit;
      } 
  }
  
  public function __toString() {
      return "Status:".(!$connection ? "Connected" : "Error").",Address:".$address;
  }
  
  public function __destruct() {
    if($this->connection) ldap_close($this->connection);
  }
  
  public function search($base, $search, $attributes=array(), $attronly=0, $limit=false, $timelimit=false) {
    if(!is_numeric($limit)) $limit = $this->globalLimit;
    if(!is_numeric($timelimit)) $timelimit = $this->timeLimit;
    
    $this->lastSearch = ldap_search($this->connection, $base, $search, $attributes, $attronly, $limit, $timelimit);
    return $this->lastSearch;
  }

  
  public function count() {
    if($this->lastSearch&&$this->connection)
      return ldap_count_entries($this->connection, $this->lastSearch);
    return false;
  }
  
  public function getArray($search=false) {
    if($this->lastSearch&&$this->connection)
      if($search) 
        return ldap_get_entries($this->connection, $search);
      else
        return ldap_get_entries($this->connection, $this->lastSearch);
    return false;
  }
  
  public function searchResultsFound($base, $search) {
    $this->search($base, $search, array("dn"), 1);
    return $this->count();
  }
  
  public function searchRowExists($base, $search) {
    return ($this->searchResultsFound($bae, $search) >= 1);
  }
}

class uomLDAP {
  private $connection;
  private $con;
  private $base;
  private $debugOn;
  private $lastQuery;

  public function __construct($base=false, $debug=false) {
    $this->connection = new pegLDAP("edir.manchester.ac.uk", 5, 10);
    $this->con =& $this->connection;
    $this->base = ($base ? $base.", ":"")."o=University of Manchester, c=GB";
    $this->debugOn = $debug;
  }
  
  private function debug($message) {
    if($this->debugOn) echo $message.'<br />';
  }

  public function searchBase($search, $attributes=array(), $attronly=0, $limit=0, $timelimit=0) {
    $this->lastQuery = $search;
    $this->con->search($this->base, $search, $attributes, $attronly, $limit, $timelimit);
    return $this->con->getArray();
  }
  
  public function searchBaseCount($search) {
    $this->debug("Search for ".$search);
    $this->lastQuery = $search;
    return $this->con->searchResultsFound($this->base, $search);
  }

  public function searchBaseExists($search) {
    $this->lastQuery = $search;
    return $this->con->searchRowExists($this->base, $search);
  }
  
  public function getLastQuery() {
    return $this->lastQuery;
  }
}

class uomPersonQuery {
  private $ldap;
  private $debugOn;
  
  public function __construct($base="ou=People", $debug=false) {
    $this->ldap = new uomLDAP($base, $debug);
    $this->debugOn = $debug;
  }
  
  private function debug($message) {
    if($this->debugOn) echo $message.'<br />';
  }
  
  private function clean($string) {
    return addslashes(strip_tags(trim($string)));
  }
  
  private function isInt($number) {
    return (is_numeric($number)&&$number>0);
  }
  
  public function peopleCount($query) {
    $query = $this->clean($query);
    $this->debug("Query: ".$query);
    if(is_numeric($query)) {
      $this->debug("is number");
      $count = $this->ldap->searchBaseCount("umanPersonID=".$query); 
      if(!$this->isInt($count)) $count = $this->ldap->searchBaseCount("umanPersonID=".$query."*"); 
    } else {
      $this->debug("is string");
      $count = $this->ldap->searchBaseCount("cn=".$query);
      if(!$this->isInt($count)) $count = $this->ldap->searchBaseCount("cn=".$query."*");
      if(!$this->isInt($count)) $count = $this->ldap->searchBaseCount("cn=*".$query."*");
    }
    
    return $count;
  }
  
  public function searchPeople($query, $attributes=array("cn", "umanprimaryou", "title", "mail", "umanpersonid", "umanstudentyearofstudy", "ou", "")) {
    if($this->peopleCount($query)>=1) {
      return $this->ldap->searchBase($this->ldap->getLastQuery(), $attributes);
    } else {
      return array();
    }  
  }
}

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