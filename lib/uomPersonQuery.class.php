<?php

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
  
  public function searchPeople($query, $attributes=array("cn", "umanprimaryou", "title", "mail", "umanpersonid", "umanstudentyearofstudy", "ou", "umantelephonenumberallow", "telephonenumber")) {
    if($this->peopleCount($query)>=1) {
      return $this->ldap->searchBase($this->ldap->getLastQuery(), $attributes);
    } else {
      return array();
    }  
  }
}

?>