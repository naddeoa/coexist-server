<?php
require_once("autoload.php");

/**
This class is used to build SchemaResponses and return 
database schema information to the client. It will be used
from at least the /api/schema API function.
*/
class SchemaResponder extends Responder
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
  public static function getSchemaResponder($get)
  {
    $r = new SchemaResponder($get);
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
    $this->response = new SchemaResponse();
  }


  protected function getExpectedParams()
  {
    return array("db");
  }


  protected function getSchemaFile($request)
  {
    return $this->getConf()->getSchemaFile($request["db"]);
  }

  /**
  Construct the response by reading the schema.sql file
  for the given dbms, specified by get['db']. It will
  be located at lib/<dbms>/<version>/schema.sql. This should be
  a standard sqlite compatible file without any spaces after
  the semicolons. For now, the last character of a line being a 
  semicolon constitutes the end of a statement as returned by
  the json array.
  */
  protected function build()
  {
    //set the right version of this schema in the client response.
    $this->getResponse()->setVersion($this->getConf()->getVersion());
    $request = $this->getRequest();

    $handle = fopen($this->getSchemaFile($request),"r");
    
    if(!$handle)
    {
      Error::sendError($this->response,
        Error::SERVER,
        "Could not find schema on server; possibly unsupported DBMS.");
    }

    
    $q = "";
    while(!feof($handle))
    {
      $line = fgets($handle,4096);
      $q = $q.$line;
      if(strlen($q) > 2 &&  $q[strlen($q)-2] === ";")
      {
        $this->response->addSql($q);
        $q = "";
      }
    }
    fclose($handle);
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
