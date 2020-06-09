<?php

$magic="MAGIC_" . rand(0,4792479234729);
define($magic,$magic);

define("APP_DEBUG",true);

error_reporting(E_ERROR); 

define("_LOCAL_APP",in_array($_SERVER["REMOTE_ADDR"],array("127.0.0.1","::1"))); 

require_once "src/config/config.php";
require_once "src/lib/functions.php";
require_once "src/classes/class.db.php";
require_once "src/classes/class.route.php";


require_once "src/routes/routes.php";

if (!_LOCAL_APP) {
  Route::run('/',0,0,1);
} else {
  Route::run('/php/007.rest/api/v4.oop/',0,0,1);
}