<?
/* Parse for char server V2
 * 
 */


function parse_none_3708(&$nfo,$buf) {
  $userid=readP($buf,4);
  $password=readP($buf,28);
  $protocol=readW($buf,2);
  if ($protocol>LOGIN_PROTOCOL) {
    // I can't handle this server !
    $buf=writeW(0x3709).writeW(LOGIN_PROTOCOL);
    net_write($nfo,$buf);
    return;
  }
  if (check_login($userid,$password,true)) {
    $server=array();
    $server["name"]=readP($buf,62);
    $server["ip"]=readIP($buf,56);
    $server["port"]=readW($buf,60);
    $server["users"]=readL($buf,52);
    $server["version"]=2.2;
    $server["protocol"]=$protocol;
    $server["logon"]=time();
    if (DEBUG) echo "New v2.1 server '".$server["name"]."' IP : ".long2ip($server["ip"]).":".$server["port"]." - protocol : $protocol \n";
    $nfo["details"]=$server;
    $nfo["version"]="char_v2";
    $nfo["SendQ_size"]=256000;
    // make packet
    $buf=writeW(0x3711);
    $buf.=writeB(0);
    net_write($nfo,$buf);
    return;
  } else {
    $buf=writeW(0x3711).writeB(3);
    net_write($nfo,$buf);
    return;
  }
}

function parse_none_3710(&$nfo,$buf) {
  $userid=readP($buf,2);
  $password=readP($buf,26);
  if (check_login($userid,$password,true)) {
    $server=array();
    $server["name"]=readP($buf,60);
    $server["ip"]=readIP($buf,54);
    $server["port"]=readW($buf,58);
    $server["users"]=0;
    $server["version"]=2;
    if (DEBUG) echo "New v2 server '".$server["name"]."' IP : ".long2ip($server["ip"]).":".$server["port"]."\n";
    $nfo["details"]=$server;
    $nfo["version"]="char_v2";
    // make packet
    $buf=writeW(0x3711);
    $buf.=writeB(0);
    net_write($nfo,$buf);
    return;
  } else {
    $buf=writeW(0x3711).writeB(3);
    net_write($nfo,$buf);
    return;
  }
}

function parse_char_v2_3712(&$nfo,$buf) {
  $id=readL($buf,2);
  $seed1=readL($buf,6);
  $seed2=readL($buf,10);
  $ok=0;
  $version=0x00;
  if (isset($GLOBALS["auth"][$id])) {
    $client=$GLOBALS["auth"][$id];
    if ( ($client["seed1"]==$seed1) and ($client["seed2"]==$seed2)) {
      $ok+=1;
      $ok+=($client["sex"]*2);
      $ok+=($client["gm"]*4);
      $GLOBALS["auth"][$id]["playing"]=$nfo["details"]["ip"].":".$nfo["details"]["port"];
      $nfo["details"]["users"]+=1;
      $version=$client["version"];
    }
  }
  $buf=writeW(0x3713).writeL($id).writeB($ok).writeW($version);
  net_write($nfo,$buf);
}

function parse_char_v2_3714(&$nfo,$buf) {
  $nfo["details"]["users"]=readL($buf,2);
}

function parse_char_v2_3715(&$nfo,$buf) {
  $accid=readL($buf,2);
  $email=readP($buf,6,40);
  $ok=0;
  if (isset($GLOBALS["auth"][$accid])) {
    $emailv=$GLOBALS["auth"][$accid]["email"];
    $email=trim(strtolower($email));
    $emailv=trim(strtolower($emailv));
    if ($email == $emailv) $ok=1;
  }
  $buf=writeW(0x3717).writeL($accid).writeB($ok);
  net_write($nfo,$buf);
}

?>