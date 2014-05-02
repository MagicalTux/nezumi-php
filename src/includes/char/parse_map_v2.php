<?
/* Support for NEW protocol v2
 * Used by Nezumi advanced map-server 
 *
 * Protocol cap :
 *  - Multi-map-server support 100% native
 */

function parse_none_5F00(&$nfo,$buf) {
  // map server connected
  $cfg=$GLOBALS["config"];
  $userid=readP($buf,2);
  $passid=readP($buf,26);
  $prot=readB($buf,50);
  if ($prot>CHAR_PROTOCOL) {
    // refuse due to too high prot
    $buf=writeW(0x5F01).writeB(2);
    net_write($nfo,$buf);
    return;
  } elseif (($userid!=$cfg["char"]["login"]) or ($passid!=$cfg["char"]["pass"])) {
    if (DEBUG) echo "New map-server v2 rejected (login&pass mispatch)\n";
    // refuse access
    $buf=writeW(0x5F01).writeB(0);
    net_write($nfo,$buf);
    return;
  } else {
    $buf=writeW(0x5F00).writeB(1);
    net_write($nfo,$buf); // accept access
    $map["users"]=0;
    $map["maps"]=array();
    $map["ip"]=0;
    $map["port"]=0;
    $map["version"]=$prot;
    $nfo["version"]="map_v2"; // protocol v2
    $nfo["SendQ_size"]=256000; // incecrase sendQ size
  }
}

function char_to_map(&$nfo,$char) {
  // sends char info to map server
  // all vars transmitted as long, empty string vars not transmitted
  reset($char);
  while(list($var,$val)=each($char)) {
    if (is_string($val)) {
      // type string
      $buf=ToLen($var,26).writeW(strlen($val)).$val;
      $buf=writeW(0x5F03).writeW(strlen($buf)+4).$buf;
      net_write($nfo,$buf);
    } else {
      $buf=ToLen($var,26).writeW(-1).writeL($val);
      $buf=writeW(0x5F03).writeW(strlen($buf)+4).$buf;
    }
  }
  // let the data go :p
}

function parse_map_v2_5F05(&$nfo,$buf) {
  // looking for map - will allow usage of multi-map
  $map=readP($buf,2,16);
  $map=find_map($map);
  $buf=writeW(0x5F06);
  if (!$map) {
    $buf.=writeIP(0).writeW(0);
  } else {
    $buf.=writeIP($map["longip"]).writeW($map["port"]);
  }
  net_write($nfo,$buf); // send answer
}
