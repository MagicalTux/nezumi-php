#!/bin/php -q
<?
include("config.php");
$cfg=$status["mysql"];
$host=$cfg["host"];
$port=$cfg["port"];
$in=fopen("php://stdin","r");
if (($port) and ($port!=3306)) $host.=":".$port;
// This script will configure your databases :)

echo "SQL Configurator system for Nezumi v2.1 !\n";
echo "-----------------------------------------\n\n";
echo "Conf : ".$cfg["user"]."@".$host." - base: ".$cfg["base"]."\n";
echo "Trying to connect to MySQL as user specified...";
$cnx=@mysql_connect($host,$cfg["user"],$cfg["pass"]);
if (!$cnx) {
  echo "failed!\n";
  need_root();
  $cnx=@mysql_connect($host,$cfg["user"],$cfg["pass"]);
  if (!$cnx) die("FATAL: User process cration seems to have failed !\n");
}
echo "OK\n";
echo "Selecting database ...";
$res=@mysql_select_db($cfg["base"]);
if (!$res) {
  echo "failed!\n";
  echo "Please create a database named ".$cfg["base"]." and grant access on it to user ".$cfg["user"]." please.\n";
  exit;
}
echo "OK\n";
echo "Checking tables and creating needed tables...\n";
$req="SELECT 1 FROM account_list";
$res=@mysql_query($req);
if (!$res) {
  echo "Creating account_list ...";
  $req="CREATE TABLE `account_list` (";
  $req.="`id` bigint(20) NOT NULL auto_increment,";
  $req.="`username` varchar(24) NOT NULL default '',";
  $req.="`password` varchar(40) NOT NULL default '',";  // 40 = len of sha1 - 32 = len of md5 - 24 = max len of plain pass
  $req.="`sex` enum('F','M','S') NOT NULL default 'M',";
  $req.="`gm` enum('Y','N') NOT NULL default 'N',";
  $req.="`banned` enum('Y','N') NOT NULL default 'N',";
  $req.="`email` varchar(40) NOT NULL default '',";  // 40 = max len for email
  $req.="`email_valid` enum('Y','N') NOT NULL default 'Y',";
  // $req.="`email_code` varchar(32) NOT NULL default '',";  // only for advanced servers with advanced interface
  $req.="`last_login` datetime default NULL,";
  $req.="PRIMARY KEY  (`id`),";
  $req.="UNIQUE KEY `id` (`id`),";
  $req.="UNIQUE KEY `username` (`username`)";
  // $req.=",UNIQUE KEY `email` (`email`)";  // only for advanced servers with advanced interface
  $req.=")";
  $res=@mysql_query($req);
  if (!$res) {
    echo mysql_error()."failed!\n";
  } else {
    echo "OK\n";
  }
}
$tn=strtolower($status["char"]["name"]);
$req="SELECT 1 FROM ".$tn."_char";
$res=@mysql_query($req);
if (!$res) {
  echo "Creating ".$tn."_char table...";
  $req="CREATE TABLE `".$tn."_char` (";
  $req.="`char_id` bigint(20) NOT NULL auto_increment,";
  $req.="`account_id` bigint(20) NOT NULL default '0',";
  $req.="`char_num` enum('0','1','2') NOT NULL default '0',";
  $req.="`name` varchar(24) NOT NULL default '',";
  $req.="`class` tinyint(4) NOT NULL default '0',";
  $req.="`base_level` tinyint(4) NOT NULL default '0',";
  $req.="`job_level` tinyint(4) NOT NULL default '0',";
  $req.="`base_exp` int(11) NOT NULL default '0',";
  $req.="`job_exp` int(11) NOT NULL default '0',";
  $req.="`zeny` bigint(20) NOT NULL default '0',";
  $req.="`hp` int(11) NOT NULL default '0',";
  $req.="`max_hp` int(11) NOT NULL default '0',";
  $req.="`sp` int(11) NOT NULL default '0',";
  $req.="`max_sp` int(11) NOT NULL default '0',";
  $req.="`str` tinyint(4) NOT NULL default '0',";
  $req.="`agi` tinyint(4) NOT NULL default '0',";
  $req.="`vit` tinyint(4) NOT NULL default '0',";
  $req.="`int` tinyint(4) NOT NULL default '0',";
  $req.="`dex` tinyint(4) NOT NULL default '0',";
  $req.="`luk` tinyint(4) NOT NULL default '0',";
  $req.="`status_point` int(11) NOT NULL default '0',";
  $req.="`skill_point` tinyint(4) NOT NULL default '0',";
  $req.="`option` tinyint(4) NOT NULL default '0',";
  $req.="`karma` enum('0') NOT NULL default '0',";
  $req.="`manner` enum('0') NOT NULL default '0',";
  $req.="`party_id` bigint(20) NOT NULL default '0',";
  $req.="`guild_id` bigint(20) NOT NULL default '0',";
  $req.="`party_name` varchar(24) NOT NULL default '',";
  $req.="`guild_name` varchar(24) NOT NULL default '',";
  $req.="`class_name` varchar(24) NOT NULL default '',";
  $req.="`hair` tinyint(4) NOT NULL default '0',";
  $req.="`hair_color` tinyint(4) NOT NULL default '0',";
  $req.="`clothes_color` tinyint(4) NOT NULL default '0',";
  $req.="`weapon` int(11) NOT NULL default '0',";
  $req.="`shield` int(11) NOT NULL default '0',";
  $req.="`head_top` int(11) NOT NULL default '0',";
  $req.="`head_mid` int(11) NOT NULL default '0',";
  $req.="`head_bot` int(11) NOT NULL default '0',";
  $req.="`last_point` varchar(48) NOT NULL default '',";
  $req.="`last_point_x` mediumint(9) NOT NULL default '0',";
  $req.="`last_point_y` mediumint(9) NOT NULL default '0',";
  $req.="`save_point` varchar(48) NOT NULL default '',";
  $req.="`save_point_x` mediumint(9) NOT NULL default '0',";
  $req.="`save_point_y` mediumint(9) NOT NULL default '0',";
  $req.="`warp_1` varchar(48) NOT NULL default '',";
  $req.="`warp_1_x` mediumint(9) NOT NULL default '0',";
  $req.="`warp_1_y` mediumint(9) NOT NULL default '0',";
  $req.="`warp_2` varchar(48) NOT NULL default '',";
  $req.="`warp_2_x` mediumint(9) NOT NULL default '0',";
  $req.="`warp_2_y` mediumint(9) NOT NULL default '0',";
  $req.="`warp_3` varchar(48) NOT NULL default '',";
  $req.="`warp_3_x` mediumint(9) NOT NULL default '0',";
  $req.="`warp_3_y` mediumint(9) NOT NULL default '0',";
  $req.="PRIMARY KEY  (`char_id`),";
  $req.="UNIQUE KEY `charident` (`account_id`,`char_num`),";
  $req.="UNIQUE KEY `char_id` (`char_id`),";
  $req.="UNIQUE KEY `name` (`name`)";
  $req.=")";
  $res=@mysql_query($req);
  if (!$res) {
    echo "failed!\n";
  } else {
    echo "OK\n";
  }
}

$tn=strtolower($status["char"]["name"]);
$req="SELECT 1 FROM ".$tn."_skill";
$res=@mysql_query($req);
if (!$res) {
  echo "Creating ".$tn."_skill table...";
  $req="CREATE TABLE `".$tn."_skill` (";
  $req.="`char_id` bigint(20) NOT NULL default '0',";
  $req.="`skill` int(11) NOT NULL default '0',";
  $req.="`level` tinyint(4) NOT NULL default '0',";
  $req.="PRIMARY KEY  (`char_id`,`skill`)";
  $req.=")";
  $res=@mysql_query($req);
  if (!$res) {
    echo "failed!\n";
  } else {
    echo "OK\n";
  }
}

$tn=strtolower($status["char"]["name"]);
$req="SELECT 1 FROM ".$tn."_item";
$res=@mysql_query($req);
if (!$res) {
  echo "Creating ".$tn."_item table...";
  $req="CREATE TABLE `".$tn."_item` (";
  $req.="`char_id` bigint(20) NOT NULL default '0',";
  $req.="`type` enum('I','C','S') NOT NULL default 'I',";
  $req.="`obj_id` mediumint(9) NOT NULL default '0',";
  $req.="`amount` mediumint(9) NOT NULL default '0',";
  $req.="`equip` mediumint(9) NOT NULL default '0',";
  $req.="`identify` tinyint(4) NOT NULL default '0',";
  $req.="`refine` tinyint(4) NOT NULL default '0',";
  $req.="`attribute` tinyint(4) NOT NULL default '0',";
  $req.="`card_0` int(11) NOT NULL default '0',";
  $req.="`card_1` int(11) NOT NULL default '0',";
  $req.="`card_2` int(11) NOT NULL default '0',";
  $req.="`card_3` int(11) NOT NULL default '0'";
  $req.=")";
  $res=@mysql_query($req);
  if (!$res) {
    echo "failed!\n";
  } else {
    echo "OK\n";
  }
}


echo "Creation process finished !\n\n";
exit;

function need_root() {
  global $in,$cfg,$host;
  echo "We need root access to set-up your SQL server.\n";
  echo "All is automatic. You just have to give root login & pass.\n\n";
  echo "Root login >";
  $rootuser=trim(fgets($in,200));
  if ($rootuser=="") $rootuser="root";
  echo "Root pass  >";
  $rootpass=trim(fgets($in,100));
  echo "\nTrying to connect...\n";
  $cnx=mysql_connect($host,$rootuser,$rootpass);
  if (!$cnx) die("The root informations you gave were refused by MySQL server.\n".mysql_error()."\n");
  echo "Access granted. Creating user & privileges ...\n";
  $req="INSERT INTO mysql.user (Host, User, password) VALUES ('%', '".$cfg["user"]."', PASSWORD('".$cfg["pass"]."'))";
  $res=@mysql_query($req);
  if (!$res) echo "Warning : couldn't insert user : ".mysql_error()."\n";
  $req="INSERT INTO mysql.db (Host, Db, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Alter_priv) VALUES ('%', '".$cfg["base"]."', '".$cfg["user"]."', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y')";
  $res=@mysql_query($req);
  if (!$res) echo "Warning : couldn't set up privileges : ".mysql_error()."\n";
  $req="CREATE DATABASE `".$cfg["base"]."`";
  $res=@mysql_query($req);
  if (!$res) echo "Warning : couldn't create a database : ".mysql_error()."\n";
  if (($rootuser=="root") and ($rootpass=="")) {
    echo "\nYou didn't choose your root password. If you want I can change it for you.\n";
    echo "If you want a new password, enter it here. Else just press return.\n";
    echo "New root password >";
    $lin=trim(fgets($in,200));
    if ($lin!="") {
      $req="UPDATE mysql.user SET Password = PASSWORD('".$lin."') WHERE User = 'root'";
      $res=@mysql_query($req);
      if (!$res) echo "Warning : couldn't update root password : ".mysql_error()."\n";
    }
  }
  $req="FLUSH PRIVILEGES";
  $res=@mysql_query($req);
  if (!$res) echo "Warning : couldn't reload privileges : ".mysql_error()."\n";
}