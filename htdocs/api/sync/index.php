<?php
$root = $_SERVER["DOCUMENT_ROOT"];
require_once("$root/../lib/autoload.php");
header("Content-Type: application/json");

$sr = SyncResponder::getSyncResponder($_GET);
echo $sr->getResponseJSON();
?>
