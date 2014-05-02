#!/bin/php -q
<?
/* MAP SERVER
 * Looking for the map-server sources ? look in includes/map ^^
 */
define("DEBUG",true);
include("includes/base.php");
daemon_init("map");
while (1)
  main_loop();

?>