<?php
require_once("autoload.php");


class CrudResponder extends Responder
{

  private $response;


  public static function getCrudResponder($get)
  {
    $r = new CrudResponder($get);
    $r->validate();
    $r->build();
  
    return $r;
  }


  protected function __construct($get)
  {
    parent::__construct($get);
    $this->response = new CrudResponse();
  }


  protected function getExpectedParams()
  {
    return array();
  }




  protected function build()
  {
    //set the right version of this schema in the client response.
    $this->getResponse()->setVersion($this->getConf()->getVersion());
    $this->getResponse()->setCreate(json_decode($this->getConf()->getCreate()));
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
