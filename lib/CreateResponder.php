<?php
require_once("autoload.php");

/**
This class is used to build SchemaResponses and return 
database schema information to the client. It will be used
from at least the /api/schema API function.
*/
class CreateResponder extends Responder
{

  private $response;

  /**
  Builder function that returns a SchemaResponder object.
  This will take care of validating the request parameters
  and parsing the schema.sql file.

  Once you have the object, getResponseJSON() can be called
  to send it to the client.
  @param $get The $_GET array from a request. It should contain
  a 'db' field.
  */
  public static function getCreateResponder($get)
  {
    $r = new CreateResponder($get);
    $r->validate();
    $r->build();
  
    return $r;
  }

  /**
  Constructor for Schemaresponder. Objects are attaiend through
  the getSchemaResponder() method, rather than using new.
  @param $get The $_GET array from a request.
  */
  protected function __construct($get)
  {
    parent::__construct($get);
    $this->response = new CreateResponse();
  }


  protected function getExpectedParams()
  {
    return array("sync","version");
  }
  
  
  protected function validate()
  {
    parent::validate();
    $conf = $this->getConf();

    if($this->getRequestParam("version") !== $conf->getVersion())
    {
      Error::updateRequired();
    }
    
  }


  protected function build()
  {
    $db = new Database();
    $sync = json_decode($this->getRequestParam("sync"),true);
    $stmnt = null;

    try{
      $db->insertOrUpdate($sync["tables"]);    
    }catch(Exception $e){
      Error::sendError(new BasicResponse(), Error::BAD, $e->getMessage());
    }

  }

  /**
  Returns the actual response object that is being built. You
  probably want to use getResponseJSON instead as json_serialize()
  will not work on these objects (they contain private fields and
  mucky inheritence).
  @return The SchemaReponse that was built.
  */
  protected function getResponse()
  {
    return $this->response;
  }

}
?>
