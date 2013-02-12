<?php
require_once("autoload.php");

/**
This class is used to respond to requests to /api/schema.
It will allow the client to update its database schema
to the current one. The creation of this class is handled by
the SchemaResponder.
*/
class CreateResponse extends Response
{


  public function __construct()
  {
   
  }


  /**
  Allows child classes to serialize into JSON and include all 
  private fields from their parents, which is the behavior we
  want for this response class heirarchy.
  @return get_object_vars of this object and the parents, in a 
  single array. This is used in getJSON().
  */
  protected function getVars()
  {
    return array_merge(parent::getVars(),get_object_vars($this));
  }

  /**
  Returns a JSON serialization of this object. It will include
  all fields from parents as well.
  @return json serialized version of this response.
  */
  public function getJSON()
  {
    return json_encode($this->getVars());
  }

}
?>
