<?
/* functions to parse client input */

function parse_none_0065(&$nfo,$buf) {
  $accid=readL($buf,2);
  $seed1=readL($buf,6);
  $seed2=readL($buf,10);
  $cnx=$GLOBALS["server_main_connexion"];
  if ((!isset($cnx)) or (is_null($cnx)) or (!isset($GLOBALS["clients"][$cnx]))) {
    if (DEBUG) echo "Not connected to login server. Can't auth client !!\n";
    $buf=writeW(0x6C).writeB(0x42);
    net_write($nfo,$buf);
    return;
  }
  $login=&$GLOBALS["clients"][$cnx];
  // prepare sending data to login server
  // and prepare the 'waiting' data ^^
  $authdata=array();
  $authdata["nfo"]=$nfo["client_id"];
  $authdata["accid"]=$accid;
  $authdata["seed1"]=$seed1;
  $authdata["seed2"]=$seed2;
  $buf=writeW(0x3712).writeL($accid).writeL($seed1).writeL($seed2); // check access
  net_write($login,$buf);
  $GLOBALS["auth1"][$accid]=$authdata;
  $nfo["version"]="pending"; // pending for login server answer
}

function parse_client_0066(&$nfo,$buf) {
  $cfg=$GLOBALS["config"];
  $charnum=readB($buf,2);
  if ($charnum<0) $charnum=0;
  $sql_prefix=strtolower($cfg["char"]["name"]);
  $req="SELECT char_id,last_point FROM ".$sql_prefix."_char WHERE account_id = ".$nfo["details"]["id"]." AND char_num = '".$charnum."'";
  $res=@mysql_query($req);
  $res=@mysql_fetch_array($res);
  if (!$res) {
    return;
  }
  $buf=writeW(0x71);
  $buf.=writeL($res["char_id"]);
  $buf.=ToLen($res["last_point"],16);
  $map=find_map($res["last_point"]);
  if (!$map) {
    if (DEBUG) echo "Error : client has unhosted respawn point {$res['last_point']} !\n";
    $buf.=writeIP(0).writeW(0);
  } else {
    $buf.=writeIP($map["longip"]).writeW($map["port"]);
    $d=$nfo["details"];
    $d["map"]=$res["last_point"];
    $GLOBALS["auth2"][$d["accid"]]=$d;
  }
  net_write($nfo,$buf);
}

function parse_client_0067(&$nfo,$buf) {
  // make new char
  $char=array();
  $char["char_num"]=(string)readB($buf,32);
  $char["name"]=readP($buf,2);
  $char["str"]=readB($buf,26);
  $char["agi"]=readB($buf,27);
  $char["vit"]=readB($buf,28);
  $char["int"]=readB($buf,29);
  $char["dex"]=readB($buf,30);
  $char["luk"]=readB($buf,31);
  $char["hair"]=readB($buf,35);
  $char["hair_color"]=readB($buf,33);
  $char=save_char($nfo["details"],$char);
  if (!is_array($char)) {
    $buf=writeW(0x6E).writeB($char);
    net_write($nfo,$buf);
  } else {
    $buf=writeW(0x6D);
    $buf.=make_006B_part($char);
    net_write($nfo,$buf);
  }
}

function parse_client_0068(&$nfo,$buf) {
  // delete char packet
  $charid=readL($buf,2);
  $email=readP($buf,6,40);
  $cfg=$GLOBALS["config"];
  // check if charid is owned by this account id before checking email
  $req="SELECT char_id,account_id FROM ".strtolower($cfg["char"]["name"])."_char WHERE char_id = ".$charid;
  $res=@mysql_query($req);
  $res=@mysql_fetch_array($res);
  if (!$res) {
    if (DEBUG) echo "req: ".$req."\nRes: ".mysql_error()."\n";
    if (DEBUG) echo "Delete rejected due to bad char_id (probably)\n";
    $buf=writeW(0x70).writeB(0x00);
    net_write($nfo,$buf);
    return;
  }
  if ($nfo["details"]["id"]!=$res["account_id"]) {  // we weren't using virtual account id (Rasqual)
    $buf=writeW(0x70).writeB(0x00);
    net_write($nfo,$buf);
    return;
  }
  $charid=$res["char_id"]; // fix for some weird clients
  // tester l'email
  $id=$nfo["details"]["id"]; // virtual id
  $buf=writeW(0x3715).writeL($id).ToLen($email,40);
  // auth3 - find login
  $cnx=$GLOBALS["server_main_connexion"];
  if ((!isset($cnx)) or (is_null($cnx)) or (!isset($GLOBALS["clients"][$cnx]))) {
    if (DEBUG) echo "Not connected to login server. Can't verify email address !!\n";
    $buf=writeW(0x70).writeB(0x00);
    net_write($nfo,$buf);
    return;
  }
  $login=&$GLOBALS["clients"][$cnx];
  net_write($login,$buf); // query sent
  $data["nfo"]=$nfo["client_id"]; // internal id
  $data["charid"]=$charid;
  $GLOBALS["auth3"][$id]=$data;
}






function parse_client_0187(&$nfo,$buf) {
  // noop.... nothing to do :p ( sent by iRO clients )
}



?>