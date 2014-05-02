<?
/* this script will check for PHP functions...
 * Is this PHP complete, with all needed functions ?
 */

if (substr(PHP_VERSION,0,1) < 4) {
  echo "Error : this daemon requires at least PHP4.3.0.\n";
  exit(1);
}

if (version_compare(phpversion(),"4.3.0")<0) {
  echo "Error : PHP version 4.3.0 or later is required !\n";
  exit(1);
}

if (!function_exists("socket_create")) {
  echo "Error : this daemon requires php's socket support.\n";
  exit(1);
}
if (!function_exists("gzcompress")) {
  echo "Error : this daemon requires php's GZip support.\n";
  exit(1);
}
if ((function_exists("posix_getuid")) and (posix_getuid()==0)) {
  echo "Warning : you shouldn't run that daemon as root.\n";
}

// in some cases, we'll need it (if PHP not runned from CLI) :
set_time_limit(0); // 0 = unlimited

?>