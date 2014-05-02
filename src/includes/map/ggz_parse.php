<?
/* .GGZ parser
 * 
 * Will parse data from .ggz files
 */

map_parse_ggz(); // call this on load

function map_parse_ggz() {
  $GLOBALS["server_maps"]=array();
  echo "Loading maps...\n";
  $dir=@opendir("data/maps");
  if (!$dir) {
    echo "Error : couldn't load dir !\n";
    return;
  }
  $tmp=array();
  while($fil=readdir($dir)) {
    if (strtolower(substr($fil,-4))==".ggz") {
      if (strlen($fil)>16) {
        echo "Warning : map ignored due to filename too long : ".$fil."\n";
        continue;
      }
      $tmp[]=$fil;
    }
  }
  echo "Found ".count($tmp)." maps ... loading...\n";
  $t=getmicrotime();
  reset($tmp);
  while(list($nule,$fil)=each($tmp)) {
    $fnam=$fil;
    $fil="data/maps/".$fil;
    $fil=@fopen($fil,"rb");
    if ($fil) {
      // load data & parse to array
      echo "Loading $fnam ...\n";
      fseek($fil,6);
      $mwidth=readSL($fil);
      $mheight=readSL($fil);
      $y=$mheight+1;
      $map=array();
      while($y>1) {
        $y-=1;
        $x=0;
        while($x<$mwidth) {
          $x++;
          $p=false;
          $p1=readSL($fil);
          $p2=readSL($fil);
          $p3=readSL($fil);
          $p4=readSL($fil);
          $p5=readSL($fil);
          if ($p5) {
            $p=true;
          } else {
            if (($p1<0) or ($p2<0) or ($p3<0) or ($p4<0)) $p=true;
          }
          $map[$x][$y]=$p;
        }
      }
      $f=str_replace(".ggz",".gat",strtolower($fil));
      $GLOBALS["server_maps"][$f]=$map;
      fclose($fil);
    }
  }
}

