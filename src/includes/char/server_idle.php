<?
/* This script will connect to the login server
 * USING V2.1 connect protocol
 */

function server_idle() {
  if (!isset($GLOBALS["server_main_connexion"])) {
    // state not registered
    $tmp=$GLOBALS["clients"];
    $res=null;
    while(list($num,$val)=each($tmp)) {
      if ($val["version"]=="login_v2") {
        if (!is_null($res)) {
          // already got one !
          @socket_close($val["socket"]);
          unset($GLOBALS["clients"][$num]);
        } else {
          $res=$num;
        }
      }
    }
    $GLOBALS["server_main_connexion"]=$res;
  }
  $num=$GLOBALS["server_main_connexion"];
  if ((!is_null($num)) and (!isset($GLOBALS["clients"][$num]))) {
    unset($GLOBALS["server_main_connexion"]);
    return; // we're going to re-etablish on next loop
  }
  $cfg=$GLOBALS["config"];
  if (is_null($num)) {
    // no available connexion !
    $tmp=$GLOBALS["idle_cnx_time"];
    if (time()<$tmp) return; // don't retry now
    $GLOBALS["idle_cnx_time"]=time()+120; // retry every 2 minutes, if fail
    if (DEBUG) echo "Connecting to login server...\n";
    $sock=net_tcp_connect($cfg["char"]["login_ip"],$cfg["char"]["login_port"],3); // 3 secs timeout
    if ($sock) {
      if (DEBUG) echo "Connected ! Sending info packet!\n";
      $client=net_client_struct();
      $client["socket"]=$sock;
      $client["version"]="login_v2";
      $client["buf_out_"]=writeW(0x1010).writeW(4).login_make_3708(); // code for checking compression
      $client["compress"]=true;
      $GLOBALS["clients"][]=$client;
      return;
    } else {
      if (DEBUG) echo "Couldn't connect to login server !\n";
    }
  }
  $tmp=$GLOBALS["idle_clientinfo_time"];
  if (time()<$tmp) return;
  $GLOBALS["idle_clientinfo_time"]=time()+20; // send client info each 20 seconds
  update_user_count();
}
