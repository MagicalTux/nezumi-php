<?
/* input parser for from web requests
 */

function parse_none_1800(&$nfo,$buf) {
  // 1800 means "web_php" connexion
  $servers=array();
  $tmp=$GLOBALS["clients"];
  reset($tmp);
  while (list($id,$dat)=each($tmp)) if (substr($dat["version"],0,4)=="char") $servers[]=$dat["details"];
  
  $tmp=array();
  reset($servers);
  while (list($id,$dat)=each($servers)) {
    $dat["uptime"]=time()-$dat["logon"];
    $tmp[]=$dat;
  }
  $servers=serialize($tmp);
  $buf=writeW(0x1801).writeW(4+strlen($servers)).$servers;
  net_write($nfo,$buf);
}

function parse_none_1802(&$nfo,$buf) {
  // 1802 packet : server 'core' informations.
  // it is used by the public sserver list. You can diable this function if you want
  $cfg=$GLOBALS["config"];
  if (!$cfg["login"]["serverinfo"]) return; // just ignore this packet if isn't allowed
  $buf=writeW(0x1803); // answer
  $dat=array();
  $dat["os"]=0; // 0 means linux
  if (OS_WINDOWS) $dat["os"]=1; // 1 is for windows
  $dat["name"]=$cfg["dir"]["name"];
  $dat["desc"]=$cfg["dir"]["desc"];
  $dat["uptime"]=time()-GLOB_STARTUP;
  
  $dat=serialize($dat);
  $buf.=writeW(strlen($dat)+4).$dat;
  net_write($nfo,$buf);
}
?>