<?php
require_once("autoload.php");

class SyncResponder extends Responder
{

  private $response;
  private $db;

  public static function getSyncResponder($get)
  {
    $r = new SyncResponder($get);
    $r->validate();
    $r->build();
    return $r;
  }
  
  protected function __construct($get)
  {
    parent::__construct($get);
    $this->response = new SyncResponse();
    $this->db = new Database();
  }

  protected function getResponse()
  {
    return $this->response;
  }

  protected function getExpectedParams()
  {
    return array("version","tables", "mod_ts");
  }

  /**
  In addition to the default validate functionality,
  this will check to make sure the client is using the 
  same schema version as the back end. If not, then
  a 409 error will be sent. The client should follow
  this up with a call to /api/schema.
  */
  protected function validate()
  {
    parent::validate();
    $conf = $this->getConf();

    if($this->getRequestParam("version") !== $conf->getVersion())
    {
      Error::updateRequired();
    }
    
  }

  /**
  Build the SyncResponse. This will open a database connection
  and get all of the rows that the client is missing, based on
  the timestamp / table pairs.
  */
  protected function build()
  {
    $response = &$this->getResponse();
    $db = new Database();
    
    $results = $db->getDiff($this->getRequestParam("tables"),
                        $this->getRequestParam("mod_ts"));
    $response->setTables($results);
  }

 


}


?>
