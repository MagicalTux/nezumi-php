#!/bin/php -q
<?
/* LOGIN SERVER
 * Seems to be empty ? look in includes/login ^^
 */
define("DEBUG",true);
include("includes/base.php");
daemon_init("login");
while (1)
  main_loop();

?>