<?
/* This file will contain functions regarding chars : save, load, setprop, getprop etc...
 */

function new_char_struct() {
  // create an empty char structure with all values to default ones
  $char=array();
  $char["name"]="Un-named";
  $char["str"]=0;
  $char["agi"]=0;
  $char["vit"]=0;
  $char["int"]=0;
  $char["dex"]=0;
  $char["luk"]=0;
  $char["char_num"]=0;
  $char["hair_color"]=1;
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
  $char["party_id"]=0; // used as-is
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
  $char["account_id"]=0;
  $char["char_id"]=0;
  return $char;
}