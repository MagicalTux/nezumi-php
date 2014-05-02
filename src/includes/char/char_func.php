<?
/* Fonctions 4 chars
 * save_char($client,$char) : sauvegarde d'un nouveau char dans la db
 */
function save_char($client,$char) {
  $cfg=$GLOBALS["config"];
  $sql_prefix=strtolower($cfg["char"]["name"]);
  if( !$char["str"] || !$char["agi"] || !$char["vit"] || !$char["int"] || !$char["dex"] || !$char["luk"] ||    // No 0 allowed
      ($char["str"]+$char["int"]!=10) ||    // STR + INT
      ($char["agi"]+$char["luk"]!=10) ||    // AGI + LUK
      ($char["vit"]+$char["dex"]!=10) ) return 0x02; // cheat ?  -> creation denied  // VIT + DEX
  if ($char["char_num"]>2) return 0x02;
  if ($char["char_num"]<0) return 0x02;
  if ($char["hair"]==0) return 0x02;
  if ($char["hair"]>=20) return 0x02;
  if ($char["hair_color"]>=9) return 0x02;
  $char["class"]=0;
  $char["base_level"]=1;
  $char["job_level"]=1;
  $char["base_exp"]=0;
  $char["job_exp"]=0;
  $char["zeny"]=50;
  $char["hp"]=40;
  $char["max_hp"]=40;
  $char["sp"]=2;
  $char["max_sp"]=2;
  $char["status_point"]=0;
  $char["skill_point"]=0;
  $char["option"]=0;
  $char["karma"]=0;
  $char["manner"]=0;
  $char["party_id"]=0;
  $char["guild_id"]=0;
  $char["clothes_color"]=0;
  $char["weapon"]=0;
  $char["shield"]=0;
  $char["head_top"]=0;
  $char["head_mid"]=0;
  $char["head_bot"]=0;
  $char["last_point"]="new_5-1.gat";
  $char["last_point_x"]=53;
  $char["last_point_y"]=111;
  $char["save_point"]=$char["last_point"];
  $char["save_point_x"]=$char["last_point_x"];
  $char["save_point_y"]=$char["last_point_y"];
  if ($client["gm"]) {
    // game master :)
  }
  $char["account_id"]=$client["id"]; // was missing (big bug, but not thief bug ^^ )
  $val1="";
  $val2="";
  reset($char);
  while (list($col,$val)=each($char)) {
    if ($val1!="") { $val1.=", "; $val2.=", "; }
    $val1.="`".$col."`";
    if (is_string($val)) {
      $val2.="'".addslashes($val)."'";
    } else {
      $val2.=$val;
    }
  }
  $req="INSERT INTO ".$sql_prefix."_char (".$val1.") VALUES (".$val2.")";
  $res=@mysql_query($req);
  if (!$res) {
    if (DEBUG) echo $req."\n";
    if (DEBUG) echo mysql_error()."\n";
    return 0x00;
  }
  $req="SELECT LAST_INSERT_ID() AS id"; // missing character ID e.g. table primary key. (Rasqual)
  $res=@mysql_query($req);
  $res=@mysql_fetch_object($res);
  if (!$res) {
    if (DEBUG) echo $req."\n";
    if (DEBUG) echo mysql_error()."\n";
    return 0x00;
  }
  $char["char_id"]=$res->id;
  return $char;
}

function make_charstatus($charid) {
  $cfg=$GLOBALS["config"];
  $sql_prefix=strtolower($cfg["char"]["name"]);
  $req="SELECT * FROM ".$sql_prefix."_char WHERE char_id = ".$charid;
  $res=@mysql_query($req);
  if (!$res) return false;
  $res=@mysql_fetch_array($res);
  if (!$res) return false;
  // convert $res -> $dat !
  sql_to_mmo_charstatus($res);
  mmo_load_skills($charid);
  mmo_load_items($charid);
  // convert $dat -> $buf
  $buf=$GLOBALS["char_struct_handler"]->get_buffer();
  if (DEBUG) echo "CharStatus tomap packet is : ".strlen($buf)." bytes long\n";
  return $buf;
}

function make_item($item) {
  $buf=writeL($item["id"]);
  $buf.=writeW($item["nameid"]);
  $buf.=writeW($item["amount"]);
  $buf.=writeW($item["equip"]);
  $buf.=writeB($item["identify"]);
  $buf.=writeB($item["refine"]);
  $buf.=writeB($item["attribute"]);
  $buf.=WriteW($item["card1"]);
  $buf.=WriteW($item["card2"]);
  $buf.=WriteW($item["card3"]);
  $buf.=WriteW($item["card4"]);
  return $buf;
}

function sql_to_mmo_charstatus($dat) {
  $conv=get_sql_conv_table();
  $GLOBALS["char_struct_handler"]->buf_reset();
  reset($conv);
  while(list($mmoside,$sqlside)=each($conv)) {
    $GLOBALS["char_struct_handler"]->set_value($mmoside,$dat[$sqlside]);
  }
}

function mmo_charstatus_to_sql() {
  $res=array();
  $conv=get_sql_conv_table();
  reset($conv);
  while(list($mmoside,$sqlside)=each($conv)) {
    if ($GLOBALS["char_struct_handler"]->get_value($mmoside,$val)) {
      $res[$sqlside]=$val;
    }
  }
  return $res;
}

function mmo_save_skills($charid) {
  // will save skills to database
  $cfg=$GLOBALS["config"];
  $table=strtolower($cfg["char"]["name"])."_skill";
  // get skills values : mmo_charstatus.skill[x].id | lv
  $r="";
  $i=1;
  $d=array();
  while (($GLOBALS["char_struct_handler"]->get_value("mmo_charstatus.skill[".$i."].id",$sid)) and ($GLOBALS["char_struct_handler"]->get_value("mmo_charstatus.skill[".$i."].lv",$slv))) {
    if ((!isset($d[$sid])) and ($sid>0) and ($slv>0)) {
      if ($r!="") $r.=", ";
      $r.="($charid, $sid, $slv)";
      $d[$sid]=true;
    }
    $i++;
  }
  $req="DELETE FROM ".$table." WHERE char_id = ".$charid;
  @mysql_query($req);
  if ($r=="") return false;
  // insert new skills using new MySQL syntax
  $req="INSERT INTO ".$table." (char_id, skill, level) VALUES ".$r;
  $res=@mysql_query($req);
  return true;
}

function mmo_load_skills($charid) {
  $cfg=$GLOBALS["config"];
  $table=strtolower($cfg["char"]["name"])."_skill";
  // get skills values from SQL
  $req="SELECT skill,level AS lvl FROM $table WHERE char_id = $charid";
  $res=@mysql_query($req);
  if (!$res) {
    echo "MySQL failed : ".mysql_error()."\n";
    return;
  }
  while($resu=@mysql_fetch_array($res)) {
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.skill[".$resu["skill"]."].id",$resu["skill"]);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.skill[".$resu["skill"]."].lv",$resu["lvl"]);
  }
  return;
}

function mmo_load_items($charid) {
  // load ALL items ^^
  $i=array();
  $cfg=$GLOBALS["config"];
  $table=strtolower($cfg["char"]["name"])."_item";
  $req="SELECT * FROM $table WHERE char_id = $charid";
  $res=@mysql_query($req);
  if (!$res) return;
  while($resu=@mysql_fetch_object($res)) {
    $i[$resu->type]++;
    $c=$i[$resu->type];
    $d="inventory";
    if ($resu->type=="C") $d="cart";
    if ($resu->type=="S") $d="storage";
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].nameid",$resu->obj_id);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].amount",$resu->amount);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].equip",$resu->equip);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].identify",$resu->identify);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].refine",$resu->refine);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].attribute",$resu->attribute);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].card[1]",$resu->card_0);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].card[2]",$resu->card_1);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].card[3]",$resu->card_2);
    $GLOBALS["char_struct_handler"]->set_value("mmo_charstatus.".$d."[".$c."].card[4]",$resu->card_3);
  }
}

function mmo_save_items($charid) {
  // save ALL items !
  $cfg=$GLOBALS["config"];
  $table=strtolower($cfg["char"]["name"])."_item";
  // mmo_charstatus.inventory[x] (type I)
  $i=0;
  $r="";
  while(1) {
    $i++;
    $p="mmo_charstatus.inventory[".$i."]";
    if ($GLOBALS["char_struct_handler"]->get_value($p.".nameid",$item_nameid)) {
      if ($item_nameid!=0) {
        $GLOBALS["char_struct_handler"]->get_value($p.".id",$item_id);
        $GLOBALS["char_struct_handler"]->get_value($p.".amount",$item_amount);
        $GLOBALS["char_struct_handler"]->get_value($p.".equip",$item_equip);
        $GLOBALS["char_struct_handler"]->get_value($p.".identify",$item_identify);
        $GLOBALS["char_struct_handler"]->get_value($p.".refine",$item_refine);
        $GLOBALS["char_struct_handler"]->get_value($p.".attribute",$item_attribute);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[1]",$item_card_1);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[2]",$item_card_2);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[3]",$item_card_3);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[4]",$item_card_4);
        if ($item_amount>0) {
          if ($r!="") $r.=", ";
          $item_id=(int)$item_id; $item_amount=(int)$item_amount; $item_equip=(int)$item_equip;
          $item_identify=(int)$item_identify; $item_refine=(int)$item_refine; $item_attribute=(int)$item_attribute;
          $item_card_1=(int)$item_card_1; $item_card_2=(int)$item_card_2; 
          $item_card_3=(int)$item_card_3; $item_card_4=(int)$item_card_4;
          $r.="($charid, 'I', $item_nameid, $item_amount, $item_equip, $item_identify, $item_refine, $item_attribute, $item_card_1, $item_card_2, $item_card_3, $item_card_4)";
        }
      }
    } else {
      break; // break loop
    }
  }
  // mmo_charstatus.cart[x] (type C)
  $i=0;
  while(1) {
    $i++;
    $p="mmo_charstatus.cart[".$i."]";
    if ($GLOBALS["char_struct_handler"]->get_value($p.".nameid",$item_nameid)) {
      if ($item_nameid!=0) {
        $GLOBALS["char_struct_handler"]->get_value($p.".id",$item_id);
        $GLOBALS["char_struct_handler"]->get_value($p.".amount",$item_amount);
        $GLOBALS["char_struct_handler"]->get_value($p.".equip",$item_equip);
        $GLOBALS["char_struct_handler"]->get_value($p.".identify",$item_identify);
        $GLOBALS["char_struct_handler"]->get_value($p.".refine",$item_refine);
        $GLOBALS["char_struct_handler"]->get_value($p.".attribute",$item_attribute);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[1]",$item_card_1);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[2]",$item_card_2);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[3]",$item_card_3);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[4]",$item_card_4);
        if ($r!="") $r.=", ";
        $item_id=(int)$item_id; $item_amount=(int)$item_amount; $item_equip=(int)$item_equip;
        $item_identify=(int)$item_identify; $item_refine=(int)$item_refine; $item_attribute=(int)$item_attribute;
        $item_card_1=(int)$item_card_1; $item_card_2=(int)$item_card_2; 
        $item_card_3=(int)$item_card_3; $item_card_4=(int)$item_card_4;
        $r.="($charid, 'C', $item_nameid, $item_amount, $item_equip, $item_identify, $item_refine, $item_attribute, $item_card_1, $item_card_2, $item_card_3, $item_card_4)";
      }
    } else {
      break; // break loop
    }
  }
  // mmo_charstatus.storage[x] (type S)
  $i=0;
  while(1) {
    $i++;
    $p="mmo_charstatus.storage[".$i."]";
    if ($GLOBALS["char_struct_handler"]->get_value($p.".nameid",$item_nameid)) {
      if ($item_nameid!=0) {
        $GLOBALS["char_struct_handler"]->get_value($p.".id",$item_id);
        $GLOBALS["char_struct_handler"]->get_value($p.".amount",$item_amount);
        $GLOBALS["char_struct_handler"]->get_value($p.".equip",$item_equip);
        $GLOBALS["char_struct_handler"]->get_value($p.".identify",$item_identify);
        $GLOBALS["char_struct_handler"]->get_value($p.".refine",$item_refine);
        $GLOBALS["char_struct_handler"]->get_value($p.".attribute",$item_attribute);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[1]",$item_card_1);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[2]",$item_card_2);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[3]",$item_card_3);
        $GLOBALS["char_struct_handler"]->get_value($p.".card[4]",$item_card_4);
        if ($r!="") $r.=", ";
        $item_id=(int)$item_id; $item_amount=(int)$item_amount; $item_equip=(int)$item_equip;
        $item_identify=(int)$item_identify; $item_refine=(int)$item_refine; $item_attribute=(int)$item_attribute;
        $item_card_1=(int)$item_card_1; $item_card_2=(int)$item_card_2; 
        $item_card_3=(int)$item_card_3; $item_card_4=(int)$item_card_4;
        $r.="($charid, 'S', $item_nameid, $item_amount, $item_equip, $item_identify, $item_refine, $item_attribute, $item_card_1, $item_card_2, $item_card_3, $item_card_4)";
      }
    } else {
      break; // break loop
    }
  }
  
  $req="DELETE FROM ".$table." WHERE char_id = $charid";
  @mysql_query($req);
  if ($r=="") return;
  $req="INSERT INTO $table (char_id, type, obj_id, amount, equip, identify, refine, attribute, card_0, card_1, card_2, card_3) VALUES ".$r;
  $res=@mysql_query($req);
  if (!$res) {
    $fil=fopen("sql_log.log","a");
    fputs($fil,"On : Saving items to database\n  $req\n  ".mysql_error()."\n");
    fclose($fil);
  }
}

function get_sql_conv_table() {
  $dat=array();
  $dat['mmo_charstatus.char_id']="char_id";
  $dat['mmo_charstatus.account_id']="account_id";
  $dat['mmo_charstatus.base_exp']="base_exp";
  $dat['mmo_charstatus.job_exp']="job_exp";
  $dat['mmo_charstatus.zeny']="zeny";
  $dat['mmo_charstatus.class']="class";
  $dat['mmo_charstatus.status_point']="status_point";
  $dat['mmo_charstatus.skill_point']="skill_point";
  $dat['mmo_charstatus.hp']="hp";
  $dat['mmo_charstatus.max_hp']="max_hp"; // not really needed
  $dat['mmo_charstatus.sp']="sp";
  $dat['mmo_charstatus.max_sp']="max_sp"; // not really needed
  $dat['mmo_charstatus.option']="option";
  $dat['mmo_charstatus.karma']="karma";
  $dat['mmo_charstatus.manner']="manner";
  $dat['mmo_charstatus.party_num']="party_num";
  $dat['mmo_charstatus.party_status']="party_status";
  $dat['mmo_charstatus.party_name']="party_name";
  $dat['mmo_charstatus.guild_name']="guild_name";
  $dat['mmo_charstatus.class_name']="class_name";
  $dat['mmo_charstatus.hair']="hair";
  $dat['mmo_charstatus.hair_color']="hair_color";
  $dat['mmo_charstatus.clothes_color']="clothes_color";
  $dat['mmo_charstatus.weapon']="weapon";
  $dat['mmo_charstatus.sheild']="shield";
  $dat['mmo_charstatus.head_top']="head_top";
  $dat['mmo_charstatus.head_mid']="head_mid";
  $dat['mmo_charstatus.head_bottom']="head_bot";
  $dat['mmo_charstatus.name']="name";
  $dat['mmo_charstatus.base_level']="base_level";
  $dat['mmo_charstatus.job_level']="job_level";
  $dat['mmo_charstatus.str']="str";
  $dat['mmo_charstatus.agi']="agi";
  $dat['mmo_charstatus.vit']="vit";
  $dat['mmo_charstatus.int_']="int";
  $dat['mmo_charstatus.dex']="dex";
  $dat['mmo_charstatus.luk']="luk";
  $dat['mmo_charstatus.char_num']="char_num";
  $dat['mmo_charstatus.party_num']="party_num";
  $dat['mmo_charstatus.party_status']="party_status";
  
  $dat['mmo_charstatus.last_point.map']="last_point";
  $dat['mmo_charstatus.last_point.x']="last_point_x";
  $dat['mmo_charstatus.last_point.y']="last_point_y";
  
  $dat['mmo_charstatus.save_point.map']="save_point";
  $dat['mmo_charstatus.save_point.x']="save_point_x";
  $dat['mmo_charstatus.save_point.y']="save_point_y";
  
  $dat['mmo_charstatus.memo_point[1].map']="warp_1";
  $dat['mmo_charstatus.memo_point[1].x']="warp_1_x";
  $dat['mmo_charstatus.memo_point[1].y']="warp_1_y";
  $dat['mmo_charstatus.memo_point[2].map']="warp_2";
  $dat['mmo_charstatus.memo_point[2].x']="warp_2_x";
  $dat['mmo_charstatus.memo_point[2].y']="warp_2_y";
  $dat['mmo_charstatus.memo_point[3].map']="warp_3";
  $dat['mmo_charstatus.memo_point[3].x']="warp_3_x";
  $dat['mmo_charstatus.memo_point[3].y']="warp_3_y";
  return $dat;
}

?>