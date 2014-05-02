#!/bin/php -q
<?
/* CHAR SERVER
 * Looking for the char-server sources ? look in includes/char ^^
 */
define("DEBUG",true);
include("includes/base.php");
daemon_init("char");
while (1)
  main_loop();

?>
