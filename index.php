<?php
// performs environment detection
$environment = getenv("ENVIRONMENT");
if(!$environment) die("Value of environment variable 'ENVIRONMENT' could not be detected!");

// takes control of STDERR
require_once("vendor/lucinda/errors-mvc/src/FrontController.php");
require_once("application/models/errors/EmergencyHandler.php");
new Lucinda\MVC\STDERR\FrontController("stderr.xml", $environment, __DIR__, new EmergencyHandler());

// takes control of STDOUT
require_once("vendor/lucinda/mvc/loader.php");
new Lucinda\MVC\STDOUT\FrontController("stdout.xml");