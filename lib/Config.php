<?php
require_once("autoload.php");

class Config
{

  private $conf;

  public function __construct()
  {
    $root = $this->getSrvRoot();
    $this->conf = parse_ini_file("$root/conf/conf.ini");
    
    if($this->conf === FALSE)
    {
      Error::sendError(new BasicResponse(), Error::SERVER,
      "Server configuration problem, there is nothing you can do.");
    }
  }

  public function getVersion()
  {
    return intval($this->conf["version"]);
  }


  public function getSrvRoot()
  {
    return $_SERVER["DOCUMENT_ROOT"]."/..";
  }

  public function getUsername()
  {
    return $this->conf["user"];
  }

  public function getPassword()
  {
    return $this->conf["pass"];
  }

  public function getDatabase()
  {
    return $this->conf["db"];
  }

  public function getHost()
  {
    return $this->conf["host"];
  }

  public function getDBMS()
  {
    return $this->conf["dbms"];
  }
  
  public function getCreate()
  {
  	return file_get_contents($this->getUiDir()."/create.json");
  }
  
  public function getSchemaFile($dbms)
  {
    return $this->getSrvRoot().
      "/conf/sql/$dbms/".
      $this->getVersion().
      "/schema.sql";
  }
  
  private function getUiDir(){
  	return $this->getSrvRoot().
      "/conf/ui/".$this->getVersion();
  }
  


}

?>
