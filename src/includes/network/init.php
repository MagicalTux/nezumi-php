<?
/* Init the NETWORK
 * VERSION 2.0
 */

function init_network() {
  $cfg=$GLOBALS["config"][DAEMON];
  if (isset($GLOBALS["socket"])) {
    $sock=&$GLOBALS["socket"];
    $sock->close();
    unset($sock);
    unset($GLOBALS["socket"]);
  }
  $GLOBALS["socket"]=new tcpsocket;
  if (!$GLOBALS["socket"]->open_socket($cfg["ip"],$cfg["port"])) {
    $GLOBALS["socket"]->socket=false;
    $tmp=$GLOBALS["network_init_error"];
    if ($tmp<time()) {
      $GLOBALS["network_init_error"]=time()+300;
  	$disp_warn=true;
    }
  	if ($disp_warn) echo "Warning : Couldn't open listening socket on ".$cfg["ip"].":".$cfg["port"]." !\n";
      return false;
  }
  return true;
}

?>
