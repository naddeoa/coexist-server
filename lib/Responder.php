<?php
require_once("autoload.php");

/**
Base abstract class for all Responders. Responders are responsible
for building Resopnses (SchemaResponse, SyncResponse...). Every
Responder will have to validate its request parameters (passed in 
as the $_GET associative array from the http request) and parse
the appropriate schema.sql file for the client. All children
should provide a static builder function that calls the appropriate
methods so that Responders can just be created and then used to get
JSON resopnses. Check /api/schema for an example of how this should 
look. 

*/
abstract class Responder
{

  private $conf;
  private $request;

  /**
  Returns the response object that this Responder is building
  @return Response object being built.
  */
  abstract protected function getResponse();

  /**
  This function should return an array of string values
  that represent the keys that it expects to be present
  in the request's associative array (e.g. $_GET). This
  will be used in the validate function.
  */
  abstract protected function getExpectedParams();

  /**
  This function should do whatever the Responder has to do
  to fill out the fields of the Response it is building. See
  SchemaResponder for a typical implementation.
  */
  abstract protected function build();
 
  /**
  Constructor for Schemaresponder. Objects are attaiend through
  the getSchemaResponder() method, rather than using new.
  @param $get The $_GET array from a request.
  */
  protected function __construct($get)
  {
    $this->conf = new Config();
    $this->request = $get;

  }

  /**
  Returns the Config object that can be used to get some 
  configuration info, such as the current version and doc root.
  This is a .ini file in the lib/ root.
  */
  protected function getConf()
  {
    return $this->conf;
  }

  /**
  Get a the request from the client. It is probably the
  $_GET variable.
  */
  protected function &getRequest()
  {
    return $this->request;
  }

  /**
  Getter method for the request.
  */
  protected function getRequestParam($key)
  {
    return $this->request[$key];
  }

  /**
  Setter method for the request.
  */
  protected function setRequestParam($key,$val)
  {
    $this->request[$key] = $val;
  }

  /**
  Validates the request. It will search through the array
  that is returned from getExpectedParams(). If any of the strings
  are not present in the request, an error will be thrown.
  */
  protected function validate()
  {
    $request = &$this->getRequest();
    //decode url encodings first, then json encodings 
    foreach(array_keys($request) as $key){
    	$request[$key] = json_decode(urldecode($request[$key]));
    }
    
    
    //make sure the expected parameters are present
    foreach($this->getExpectedParams() as $param)
    {
      if(!isset($request[$param]))
      {
        Error::missingParameter($param);
      }
    }
    
  }

  /**
  Returns the JSON encoded version of the response that has been
  built. If there is an error then the build process will halt
  and the error will be sent to the client, followed by a call to
  exit(1).
  @return JSON encoded version of the Response.
  */
  public function getResponseJSON()
  {
    return $this->getResponse()->getJSON();
  }

}
?>
