<?
/* Parse char for v1 char servers
 * 
 */

function parse_none_2710(&$nfo,$buf) {
  $userid=readP($buf,2);
  $password=readP($buf,26);
  if (check_login($userid,$password,true)) {
    $server=array();
    $server["name"]=readP($buf,60);
    $server["ip"]=readIP($buf,54);
    $server["port"]=readW($buf,58);
    $server["users"]=0;
    $server["version"]=1;
    $server["logon"]=time();
    if (DEBUG) echo "New v1 server '".$server["name"]."' IP : ".long2ip($server["ip"]).":".$server["port"]."\n";
    $nfo["details"]=$server;
    $nfo["version"]="char_v1";
    $nfo["SendQ_size"]=256000; // new SendQ
    // make packet
    $buf=writeW(0x2711);
    $buf.=writeB(0);
    net_write($nfo,$buf);
    return;
  } else {
    $buf=writeW(0x2711).writeB(3);
    net_write($nfo,$buf);
    return;
  }
}

function parse_char_v1_2712(&$nfo,$buf) {
  // verify client
  $id=readL($buf,2);
  $seed1=readL($buf,6);
  $seed2=readL($buf,10);
  $ok=1;
  
  if (isset($GLOBALS["auth"][$id])) {
    $client=$GLOBALS["auth"][$id];
    if ( ($client["seed1"]==$seed1) and ($client["seed2"]==$seed2)) {
      $ok=0;
      unset($GLOBALS["auth"][$id]); // informations...
      $nfo["details"]["users"]+=1;
    }
  }
  $buf=writeW(0x2713).writeL($id).writeB($ok);
  net_write($nfo,$buf);
  return;
}

function parse_char_v1_2714(&$nfo,$buf) {
  $nfo["details"]["users"]=readL($buf,2);
}

function parse_char_v1_2716(&$nfo,$buf) {
  // this packet isn't generated
}

function parse_char_v1_3002(&$nfo,$buf) {
  // rehash packet - deprecated and should be ignored
}
?>