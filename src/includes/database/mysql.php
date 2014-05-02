<?
/* MySQL support for Nezumi v2.x
 * Check function - the server wil *not* die if we loose SQL connexion
 */
function db_check() {
  if (!ENABLE_DB) return; // do not enable this
  $cnx=$GLOBALS["sql_connexion_handler"];
  if ($cnx) {
    $tmp=$GLOBALS["sql_connexion_check"];
    if (time()<$tmp) return;
    $GLOBALS["sql_connexion_check"]=time()+300; // check MySQL connexion every 5 minutes
    $res=@mysql_ping($cnx); // ping query
    if (!$res) {
      // argghhh !
      @mysql_close($cnx);
      $GLOBALS["sql_connexion_handler"]=false;
      $cnx=false;
    } else {
      return; // everything ok
    }
  }
  // no connexion or dead connexion ? 
  $tmp=$GLOBALS["sql_connexion_retry"];
  if (time()<$tmp) return;
  $GLOBALS["sql_connexion_retry"]=time()+120; // retry SQL link etablishement every 2 min
  $cfg=$GLOBALS["config"];
  $host=$cfg["mysql"]["host"];
  $port=$cfg["mysql"]["port"];
  if (($port) and ($port!=3306)) $host.=":".$port;
  $flags=0;
  if ($status["mysql"]["compress"]) $flags=MYSQL_CLIENT_COMPRESS;
  $cnx=@mysql_connect($host,$cfg["mysql"]["user"],$cfg["mysql"]["pass"],$false,$flags);
  if (!$cnx) {
    echo "Error : couldn't connect to MySQL database server!\n";
    return;
  }
  $svers=mysql_get_server_info();
  $s_version=explode("-",$svers);
  $s_version=explode(".",$s_version[0]);
  if ($s_version[0]==3) {
    if ($s_version[1]<23) {
      @mysql_close($cnx);
      echo "You need at least MySQL 3.23.x ! You are using $svers !\n";
      return;
    }
  } elseif($s_version[0]<3) {
    @mysql_close($cnx);
    echo "You need at least MySQL 3.23.x ! You are using $svers !\n";
    return;
  }
  if (!@mysql_select_db($cfg["mysql"]["base"])) {
    @mysql_close($cnx);
    echo "Couldn't select database ! Please check privileges.\n";
    return;
  }
  echo "MySQL connexion etablished !\n";
  $GLOBALS["sql_connexion_handler"]=$cnx;
}

function db_find($table,$fields,$condition=array()) {
  if (!ENABLE_DB) return; // do not enable this
  // get data from database
  $a=""; $b="";
  reset($fields);
  while(list($num,$nam)=each($fields)) {
    if ($a!="") $a.=", ";
    $a.="`".$nam."`";
  }
  if ($a=="") $a="*";
  reset($condition);
  while(list($f_,$v_)=each($condition)) {
    if ($b!="") $b.=" AND ";
    $b.="`".$f_."` = ";
    if (is_string($v_)) {
      $b.="'".mysql_escape_string ($v_)."'";
    } else {
      $b.=$v_;
    }
  }
  if ($b!="") $b=" WHERE ".$b;
  $req="SELECT ".$a." FROM `".$table."`".$b;
  $res=@mysql_query($req);
  if (!$res) return false;
  $r=array();
  if (@mysql_num_rows($res)<1) return array();
  while($resu=@mysql_fetch_array($res)) {
    $r[]=$resu;
  }
  @mysql_free_result($res);  // free memory used to get info
  return $r;
}

function db_insert($table,$fields) {
  if (!ENABLE_DB) return; // do not enable this
  $a=""; $b="";
  reset($fields);
  while(list($var,$val)=each($fields)) {
    if ($a!="") $a.=", ";
    if ($b!="") $b.=", ";
    $b.="`".$var."`";
    if (is_string($val)) {
      $a.="'".mysql_escape_string($val)."'";
    } else {
      $a.=$val;
    }
  }
  $req="INSERT INTO $table (".$b.") VALUES (".$a.")";
  $res=@mysql_query($req);
  if (!$res) return false;
  $req="SELECT LAST_INSERT_ID() AS a";
  $res=@mysql_query($req);
  $res=@mysql_fetch_array($res);
  if (!$res) return true;
  $res=$res["a"];
  if (!$res) return true;
  return $res;
}

function db_update_login($accid) {
  @mysql_query("UPDATE account_list SET last_login = NOW() WHERE id = $accid");
}
?>