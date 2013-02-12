<?php
require_once("autoload.php");

/**
This class is used to respond to requests to /api/schema.
It will allow the client to update its database schema
to the current one. The creation of this class is handled by
the SchemaResponder.
*/
class CrudResponse extends Response
{

  private $version;
  private $create;

  public function __construct()
  {
    $this->version = 0;
  }

  /**
  Get the current version of the database schema. This will
  be stored and the client side and used to send in other
  requests.
  @return An integer version
  */
  public function getVersion()
  {
    return $version;
  }

  /**
  Set the version of this response. This ultimately comes from
  the Config class.
  @param $version The current version of database schema.
  */
  public function setVersion($version)
  {
    $this->version = $version;
  }

  public function setCreate($create)
  {
  	$this->create = $create;
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
