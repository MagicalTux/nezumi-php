<?
/* Net_idle
 * Network function using select to detect socket activity
 */

function net_idle($timeout=null) {
  $socks=array();
  if (is_object($GLOBALS["socket"])) {
    if ($GLOBALS["socket"]->socket!=false) $socks[]=$GLOBALS["socket"]->socket;
  }
  $tmp=$GLOBALS["clients"];
  reset($tmp);
  while (list($id,$nfo)=each($tmp)) {
    if ($nfo["socket"]) $socks[]=$nfo["socket"];
  }
  if (count($socks)<1) { // special case where socket_select will fail
    sleep($timeout);
    return false;
  }
  if (OS_WINDOWS) {
    $res=socket_select($socks,$w=null,$e=null,0,24000); // windows' select seems to be broken
  } else {
    $res=socket_select($socks,$w=null,$e=null,$timeout);
  }
  if ($res>0) return true; // activity !!!!
  return false; // nothing :/
}

?>