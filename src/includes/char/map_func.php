<?
/* Map functions
 * (array) find_map((string) map_name) - returns server where the map is or false
 *       array : longip port
 */
function find_map($map) {
  if (DEBUG) echo "Find request for map : $map \n";
  $tmp=$GLOBALS["clients"];
  reset($tmp);
  while (list($num,$nfo)=each($tmp)) {
    if (substr($nfo["version"],0,3)=="map") {
      $mps=$nfo["details"]["maps"];
      reset($mps);
      $found=false;
      while(list($id,$mp)=each($mps)) {
        if ($map == $mp) $found=true;
      }
      if ($found) {
        $ans=array();
        $ans["longip"]=$nfo["details"]["ip"];
        $ans["port"]=$nfo["details"]["port"];
        $ans["id"]=$num;
        return $ans;
      }
    }
  }
  if (DEBUG) echo "Map $map missing on servers\n";
  return false;
}