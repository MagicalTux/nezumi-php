<?
/* Nezumi Project
 * VERSION 2.2
 * 
 * This script is the one who will initialise everything
 */

// Function : load_ext(ext_type)
function load_ext($ext_type) {
  // this function will load includes for ext_type
  $dir=@opendir("includes/".$ext_type);
  if (!$dir) return false; // error
  while ($fil=@readdir($dir)) {
    if (substr($fil,-4)==".php") include_once("includes/".$ext_type."/".$fil);
  }
  return true;
}

function daemon_init($daemon_type) {
  // functions about daemon init
  $daemon_type=strtolower($daemon_type);
  echo "Welcome to Nezumi engine v2.2\nDevelopped, coded, debugged, etc.... by MagicalTux <w@ff.st>\n\n";
  echo "Loading the ".strtoupper($daemon_type)."-server daemon...\n";
  if (!load_ext("common")) {
    echo "Error : couldn't load common extensions !\n";
    exit(1);
  }
  if (!load_cfg()) {
    echo "Error : couldn't load configuration files.\n";
    exit(1);
  }
  // enable database system
  $cfg=$GLOBALS["config"];
  if (!file_exists("includes/database/".strtolower($cfg["global"]["database"]).".php")) {
    echo "Your config file is broken : database type '".$cfg["global"]["database"]."' unknown.\n";
    exit(2);
  }
  include_once("includes/database/".strtolower($cfg["global"]["database"]).".php");
  if (!load_ext($daemon_type)) {
    echo "Error : couldn't load daemon specific extentions.\n";
    exit(1);
  }
  if (!load_ext("network")) {
    echo "Error : couldn't load network extensions !\n";
    exit(1);
  }
  define("DAEMON",$daemon_type);
  define("GLOB_STARTUP",time());
  if (eregi("Win",PHP_OS)) {
    define("OS_WINDOWS",true);
  } else {
    define("OS_WINDOWS",false);
  }
  $GLOBALS["clients"]=array(); // empty client list
  init_network();
  db_check();
}


function main_loop() {
  usleep(100000); // 1/10 sec
  // Main daemon loop
  if (!defined("DAEMON")) {
    echo "Error : illegal call to main_loop()\n";
    exit(1);
  }
  while (true) {
    net_idle(5); // 5 sec idle
    db_check(); // check MySQL liaison state
    net_check(); // cleck data from clients
    if (function_exists("server_idle")) server_idle(); // function may be used to etablish connexion to upper server
    usleep(10000); // 1/100 sec
  }
}