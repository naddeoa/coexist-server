<?php
require_once("autoload.php");

/**
This class is used to respond to requests to /api/schema.
It will allow the client to update its database schema
to the current one. The creation of this class is handled by
the SchemaResponder.
*/
class SchemaResponse extends Response
{

  private $version;
  private $sql;

  public function __construct()
  {
    $this->version = 0;
    $this->sql = Array();
    $conf = new Config();
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


  /**
  Add sql statements to the array that will be used to build the 
  database on the client. This is probably inserted by the 
  SchemaResponder class as it parses the appropriate schema.sql
  file in the lib/ folder, based on the requested DBMS type and
  version number.
  @param $statement A statement to add to the return array for 
  the client to use.
  */
  public function addSql($statement)
  {
    array_push($this->sql,$statement);
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
