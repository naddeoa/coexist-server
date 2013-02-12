<?php
require_once("autoload.php");

abstract class Response
{

  private $status = 200;
  private $message = "OK";

  /**
  Return the http response code status for this response.
  This defaults to 200 and getes changed in the Error
  class if there is an error.
  */
  public function getStatus()
  {
    return $status;
  }

  /**
  Sets the http response code status for this response.
  @param $status The status to set it to.
  */
  public function setStatus($status)
  {
    $this->status = $status;
  }

  /**
  Gets the message for this response. This defaults to "OK", as
  the status code defaults to 200.
  @return The message for this response. It should be something
  useful it there is an error.
  */
  public function getMessage()
  {
    return $message;
  }

  /**
  Sets the message of this response.
  @param The message to set to. Make sure its useful if there is
  an error.
  */
  public function setMessage($message)
  {
    $this->message = $message;
  }

  /**
  Intended for use by children. This will allow us to
  call getJSON() and serialize the private fields, while
  still being able to keep them private and use standard OO
  practices. 

  note: see SchemaResponse for a typical reimplementation of 
  this method. You should always override this method this way.
  @return the get_object_vars() of this Response class.
  */
  protected function getVars()
  {
    return get_object_vars($this);
  }

  /**
  Returns a json encoded version of this object.
  Look at SchemaResponse.php for a typical implementation
  */
  abstract public function getJSON();

  /**
  This function exists for the Error class. It will serialize
  only the status and message fields into JSON in the event 
  of errors, when it only makes sense to send those fields back.
  Without this, then the fields like version and sql would be
  included in the response as null values.
  @return the json encoded version of just the Response classe's 
  variables.
  */
  final public function minJSON()
  {
    return json_encode(get_object_vars($this));
  }

}

?>
