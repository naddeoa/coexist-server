<?php

function __autoload($classname) {
    $root = $_SERVER["DOCUMENT_ROOT"];
    include_once( "$root/../lib/".$classname.".php" );
}

?>
