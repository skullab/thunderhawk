<?php

require_once '../core/API/Engine.php';

use Thunderhawk\API\Engine;

//error_reporting(E_ALL & ~E_NOTICE);
enableDump(false);

$e = new Engine();
$e->debugMode(true);
$e->run();



