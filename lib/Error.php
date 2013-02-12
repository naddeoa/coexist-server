<?php
require_once("autoload.php");

/**
Simple convenience class to handle error reporting. All classes
that plan on sending errors should be making calls to 
Error:sendError, passing in an object that inherits from Response.
*/
class Error
{

  const BAD          = 400;
  const UNAUTHORIZED = 401;
  const FORBIDDEN    = 403;
  const CONFLICT     = 409; 

  const SERVER       = 500;

  /**
  Send an error to the client. The http status will be changed
  and exit(1) will be called. Responses are sent as JSON objects
  with status and message properties.
  @param $code One of the const codes of the Error class.
  @param $msg An explanation of the error.
  */
  public static function sendError($response,$code,$msg)
  {
    $response->setStatus($code);
    $response->setMessage($msg);
    header(" ", true, $code);
    echo($response->minJSON());
    exit(1);
  }

  public static function tableMismatch()
  {
    Error::sendError(new BasicResponse(),
      Error::BAD,
      "Client and server tables do not match");
  }

  public static function updateRequired()
  {
    Error::sendError(new BasicResponse(),
      Error::CONFLICT,
      "The client requires a schema update");
  }

  public static function missingParameter($param)
  {
    Error::sendError(new BasicResponse(),
      Error::BAD,
      "Missing the ".$param." parameter.");
  }

  public static function unknown()
  {
    Error::sendError(new BasicResponse(),
      Error::BAD,
      "An unkown error occured.");
  }
  
  public static function rowAlreadyExists()
  {
    Error::sendError(new BasicResponse(),
      Error::BAD,
      "That row already exists.");
  }

  /**
  Just to make sure this is not instaniated.
  */
  private function __construct(){}
}

?>
