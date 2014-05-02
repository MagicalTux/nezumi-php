<?
/* function for parsing data from login v2 server
 *
 */

function login_make_3708() {
  // make the 3708 packet
  $buf=writeW(0x3708);
  $cfg=$GLOBALS["config"];
  $buf.=writeW(CHAR_PROTOCOL); // protocol version
  $buf.=ToLen($cfg["char"]["login"],24);
  $buf.=ToLen($cfg["char"]["pass"],24);
  $buf.=WriteL(0); // maybe initial number of players?
  $buf.=WriteIP(ip2long($cfg["char"]["own_ip"]));
  $buf.=WriteW($cfg["char"]["port"]);
  $buf.=ToLen($cfg["char"]["name"],16);
  return $buf;
}

function parse_login_v2_3709(&$nfo,$buf) {
  echo "Connexion refused by login server due to protocol version mismatch.\n";
  @socket_close($nfo["socket"]);
  $nfo=false;
  return;
}

function parse_login_v2_3711(&$nfo,$buf) {
  // anser to login
  $answer=readB($buf,2);
  if ($answer!=0) {
    @socket_close($nfo["socket"]);
    echo "Error : connexion rejected by login server!\n";
    $nfo=false;
    return;
  } else {
    if (DEBUG) echo "Connexion accepted by login-server.\n";
    $nfo["SendQ_size"]=256000; // max len of sendQ
  }
}

function parse_login_v2_3713(&$nfo,$buf) {
  $id=readL($buf,2);
  $ok=readB($buf,6);
  $version=readW($buf,7);
  if (!isset($GLOBALS["auth1"][$id])) return;
  $data=$GLOBALS["auth1"][$id];
  unset($GLOBALS["auth1"][$id]);
  if (!isset($GLOBALS["clients"][$data["nfo"]])) return;
  $client=&$GLOBALS["clients"][$data["nfo"]];
  // $ok flags : 0x01 : ok 0x02 : sex 0x04 : gm
  if (($ok and 0x01)==0) {
    // client rejected
    $buf=writeW(0x6C).writeB(0x42);
    $GLOBALS["clients"][$data["nfo"]]["version"]="none"; // returning to normal
    net_write($client,$buf);
    return;
  }
  $c=array();
  $c["gm"]=(($ok & 0x04)==0x04)?1:0;;
  $c["sex"]=(($ok & 0x02)==0x02)?1:0;
  $n=$id;
  if ($c["gm"]) $n-=100000;
  $c["id"]=$n;
  $c["accid"]=$id;
  $c["seed1"]=$data["seed1"];
  $c["seed2"]=$data["seed2"];
  $c["version"]=$data["version"];
  // now, make the 006B
  $buf=make_006B($c);
  if (!$buf) {
    // some exception
    $buf=writeW(0x6C).writeB(0x42);
    net_write($client,$buf);
    $client["version"]="none";
  }
  $buf=writeL($id).$buf;
  $client["version"]="client";
  $client["details"]=$c;
  net_write($client,$buf);  // non standard packet :'(
}

function parse_login_v2_3717(&$nfo,$buf) {
  $cfg=$GLOBALS["config"];
  $id=readL($buf,2);
  $ok=readB($buf,6);
  if (!isset($GLOBALS["auth3"][$id])) return;
  $data=$GLOBALS["auth3"][$id];
  unset($GLOBALS["auth3"][$id]);
  if (!isset($GLOBALS["clients"][$data["nfo"]])) return;
  $client=&$GLOBALS["clients"][$data["nfo"]];
  $charid=$data["charid"];
  if ($ok) {
    $req="DELETE FROM ".strtolower($cfg["char"]["name"])."_char WHERE char_id = ".$charid;
    $res=@mysql_query($req);
    if ($res) {
      $buf=writeW(0x6F);
      net_write($client,$buf);
      return;
    }
  }
  $buf=writeW(0x70).writeB(0x00);
  net_write($client,$buf);
}

?>