<?php
require_once("autoload.php");

class BasicResponse extends Response
{
  public function getJSON()
  {
    return json_encode($this->getVars());
  }
}

?>
