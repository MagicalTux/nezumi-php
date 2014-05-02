#!/bin/php -q
<?
/* The Nezumi map-server is using a new file format for .gat files
 * Here's a little software which will convert every maps from directory "maps"
 */
echo "GAT to GGZ....\n";
set_time_limit(0);
if (!function_exists("gzcompress")) {
  echo "Error : This script requires GZip to be enabled in PHP\n";
  exit;
}
include("../includes/common/mini_funcs.php"); // lots of useful functions

$dir=opendir("gat");
while($fil=readdir($dir)) {
  if (substr($fil,-4)==".gat") convert($fil);
}

function convert($source) {
  $res="maps/".substr($source,0,strlen($source)-4).".ggz"; // Gat with GZip
  $source="gat/".$source;
  if (file_exists($res)) return; // skip
  echo $source." read...";
  $fil=@fopen($source,"rb");
  if (!$fil) return false;
  fseek($fil,6);
  $mwidth=readSL($fil);
  $mheight=readSL($fil);
  $y=$mheight+1;
  $map=array();
  while($y>1) {
    $y-=1;
    $m="";
    $x=0;
    $t=0;
    while($x<$mwidth) {
      $x++;
      $p=0;
      $p1=readSL($fil);
      $p2=readSL($fil);
      $p3=readSL($fil);
      $p4=readSL($fil);
      $p5=readSL($fil);
      if ($p5!=0) {
        $p=$p5;
      } else {
        if (($p1<0) or ($p2<0) or ($p3<0) or ($p4<0)) $p=3;
      }
      $k=($x-1) % 8;
      if (($k==0) and ($x>1)) {
        $m.=chr($t);
        $t=0;
      }
      if (($p!=1) and ($p!=5)) $t=($t | pow(2,$k));
    }
    if ($k!=0) $m.=chr($t);
    $map[$y]=$m;
  }
  fclose($fil);
  echo " write...";
  $fil=gzopen($res,"wb9");
  if (!$fil) return false;
  gzwrite($fil,"GGZ\x00".writeL($mwidth).writeL($mheight));
  $y=0;
  while($y<$mheight) { // reorder to descending order
    $y++;
    gzwrite($fil,$map[$y]);
  }
  gzclose($fil);
  echo "\n";
}