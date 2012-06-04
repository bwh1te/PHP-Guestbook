<?php
function __autoload($class_name) {
    include 'controllers/' . strtolower($class_name) . '.php';
}
?>