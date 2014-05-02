<?

function char_make_5F00() {
  // generate this packet
  $cfg=$GLOBALS["config"];
  $buf=writeW(0x5F00).ToLen($cfg["map"]["login"],24).ToLen($cfg["map"]["pass"],24);
  $buf.=writeB(MAP_PROTOCOL);
  return $buf;
}

function parse_char_v2_5F01(&$nfo,$buf) {
  $code=readB($buf,2);
  if ($code==2) {
    // we're using too high protocol (char-server won't support it T_T )
    echo "Error connecting to char-server : protocol incompatible.\n";
    @socket_close($nfo["socket"]);
    $nfo=false;
    return;
  } elseif ($code==1) {
    // login/pass incorrect
    echo "Error connecting to char-server : login/pass mismatch.\n";
    @socket_close($nfo["socket"]);
    $nfo=false;
    return;
  } elseif ($code==0) {
    echo "Accepted by char-server. Sending map-server's informations...\n";
  }
}

function update_user_count() { // send new user-info to char-server
  $num=$GLOBALS["server_main_connexion"];
  if ((!is_null($num)) and (!isset($GLOBALS["clients"][$num]))) return false; // don't update if not connected
  $char=&$GLOBALS["clients"][$num]; // get it
  if (!$char) return false;
  // count clients...
  $clients=0;
  $tmp=$GLOBALS["clients"];
  reset($tmp);
  while(list($num,$dat)=each($tmp)) if ($dat["version"]=="client"]) $clients++;
  // send data packet to char server
  $buf=writeW(0x5F07).writeL($clients);
  net_write($char,$buf);
}
