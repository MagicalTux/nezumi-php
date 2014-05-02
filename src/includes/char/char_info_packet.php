<?
/* Char Info Packet
 * This function will make the 'char info' packet : a packet containing informations about every owned char
 */
function make_006B($client) {
  $cfg=$GLOBALS["config"];
  $sql_prefix=strtolower($cfg["char"]["name"]);
  $req="SELECT * FROM ".$sql_prefix."_char WHERE account_id = {$client['id']}"; // removed 3 char limit for compatibility with some korean clients
  $res=@mysql_query($req);
  if (!$res) {
    if (DEBUG) echo "Verify access to the char table (and if it exists !!) !\n";
    return false;
  }
  $abuf=array();
  while($resu=@mysql_fetch_array($res)) {
    $abuf[$resu["char_num"]]=make_006B_part($resu);
  }
  $i=count($abuf);
  $buf=writeW(0x6B).writeW(4+$i*106);
  ksort($abuf);
  while(list($num,$dat)=each($abuf)) $buf.=$dat;
  return $buf;
}

function make_006B_part($resu) {
  $pbuf =writeL($resu["char_id"]);
  $pbuf.=writeL($resu["base_exp"]);
  $pbuf.=writeL($resu["zeny"]);
  $pbuf.=writeL($resu["job_exp"]);
  $pbuf.=writeL($resu["job_level"]);
  
  $pbuf.=writeL(0); // unknown
  $pbuf.=writeL(0); // also unknwown
  $pbuf.=writeL($resu["option"]);
  
  $pbuf.=writeL($resu["karma"]);
  $pbuf.=writeL($resu["manner"]);
  
  $pbuf.=writeW($resu["status_point"]);
  $pbuf.=writeW($resu["hp"]);
  $pbuf.=writeW($resu["max_hp"]);
  $pbuf.=writeW($resu["sp"]);
  $pbuf.=writeW($resu["max_sp"]);
  $pbuf.=writeW(140); // DEFAULT_WALK_SPEED
  $pbuf.=writeW($resu["class"]);
  $pbuf.=writeW($resu["hair"]);
  $pbuf.=writeW($resu["weapon"]);
  $pbuf.=writeW($resu["base_level"]);
  $pbuf.=writeW($resu["skill_points"]);
  $pbuf.=writeW($resu["head_bot"]);
  $pbuf.=writeW($resu["shield"]);
  $pbuf.=writeW($resu["head_top"]);
  $pbuf.=writeW($resu["head_mid"]);
  $pbuf.=writeW($resu["hair_color"]);
  $pbuf.=writeW($resu["clothes_color"]);
  
  $pbuf.=ToLen($resu["name"],24);
  
  $pbuf.=writeB($resu["str"]);
  $pbuf.=writeB($resu["agi"]);
  $pbuf.=writeB($resu["vit"]);
  $pbuf.=writeB($resu["int"]);
  $pbuf.=writeB($resu["dex"]);
  $pbuf.=writeB($resu["luk"]);
  $pbuf.=writeB($resu["char_num"]);
  $pbuf=ToLen($pbuf,106);  // check pbuf size (should add 01 byte)
  return $pbuf;
}