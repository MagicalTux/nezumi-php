<?
/* input parser for "unknown" packets (non identified clients)
 * functions :p
 */

function parse_none_0064(&$nfo,$buf) {
  // 0064 means "client" connexion
  // shouldn't be translated to something else =)
  $servers=array();
  $version=readW($buf,2);
  if ($version<18) {
    // client too old
    $buf=writeW(0x6A).writeB(5);
    $buf=ToLen($buf,23);
    net_write($nfo,$buf);
    return;
  }
  $userid=readP($buf,6);
  $password=readP($buf,30);
  $client=array();
  $client["version"]=$version;
  $res=check_login($userid,$password,false,$client);
  if (is_array($res)) {
    $tmp=$GLOBALS["clients"];
    reset($tmp);
    while (list($id,$dat)=each($tmp)) if (substr($dat["version"],0,4)=="char") $servers[]=$dat;
    $client=$res;
    $buf=writeW(0x69);
    $buf.=writeW(47+32*count($servers));
    $buf.=writeL($client["seed1"]);
    $buf.=writeL($client["accountid"]);
    $buf.=writeL($client["seed2"]);
    $buf.=writeL(0);
    $buf.=ToLen($client["date"],26);
    $buf.=writeB($client["sex"]);
    $rip=get_rip($nfo["socket"]);
    $lip=get_lip($nfo["socket"]);
    reset($servers);
    while (list($num,$dat)=each($servers)) {
      $server=$dat["details"];
      $sip=$server["ip"];
      if ($sip==0x7F000001) { // local
        if ($sip!=$rip) continue; // don't display local servers if not connected from localhost
      } elseif ($sip==0) {
        // auto-detection of IP
        $t=get_rip($dat["socket"]);
        if (($t==0) or ($t==0x7F000001)) {
          // local server
          $sip=$lip;
        } else {
          // remote server
          $sip=$t;
        }
      }
      $buf.=writeIP($sip);
      $buf.=writeW($server["port"]);
      $buf.=ToLen($server["name"],20);
      $buf.=writeW($server["users"]);
      $buf.=writeW(0); // maintenance ?
      $buf.=writeW(0); // unknown
    }
    // send buffer :D
    net_write($nfo,$buf);
  } else {
    // bad login
    $buf=writeW(0x6A).writeB($res);
    $buf=ToLen($buf,23);
    net_write($nfo,$buf);
    return; // this socket can be used again ^^
  }
}
?>