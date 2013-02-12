<?php
require_once("autoload.php");

class SyncResponse extends Response
{

  private $tables;

  public function __construct()
  {
    $this->tables = array();
  }

  public function addTables($name, $rows)
  {
    $this->tables[$name] = $rows;
  }

  public function setTables($table)
  {
    $this->tables = $table;
  }

  protected function getVars()
  {
    return array_merge(parent::getVars(),get_object_vars($this));
  }

  public function getJSON()
  {
    return json_encode($this->getVars());
  }

}

?>
