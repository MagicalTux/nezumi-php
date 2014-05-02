#!/bin/php -q
<?
/* The Nezumi map-server is using a new file format for .gat files
 * Here's a little software which will convert every maps from directory "maps"
 */
echo "GGZ to GGA....\n";
set_time_limit(0);
if (!function_exists("gzcompress")) {
  echo "Error : This script requires GZip to be enabled in PHP\n";
  exit;
}
include("../includes/common/mini_funcs.php"); // lots of useful functions

$dir=opendir("maps");
while($fil=readdir($dir)) {
  if (substr($fil,-4)==".ggz") convert("maps/".$fil);
}

function convert($source) {
  echo $source." read...";
  $res=substr($source,0,strlen($source)-4).".gga"; // Gat without GZip
  $fil=@gzopen($source,"rb");
  if (!$fil) return false;
  $dat=gzread($fil,999999999);
  gzclose($fil);
  // now write
  $fil=fopen($res,"wb");
  fputs($fil,$dat);
  fclose($fil);
}