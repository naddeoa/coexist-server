<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require_once("$root/../lib/autoload.php");
header("Content-Type: application/json");

$r = CreateResponder::getCreateResponder($_GET);
echo $r->getResponseJSON();

?>
