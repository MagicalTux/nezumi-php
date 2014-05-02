<?
/* Base parameters for char 
 * ...
 */

define("CHAR_PROTOCOL",3); // protocol mode 
define("ENABLE_DB",true);

function update_user_count() {
  $cnx=$GLOBALS["server_main_connexion"];
  if ((!isset($cnx)) or (is_null($cnx)) or (!isset($GLOBALS["clients"][$cnx]))) return;
  $login=&$GLOBALS["clients"][$cnx];
  $tmp=$GLOBALS["clients"];
  $count=0;
  reset($tmp);
  while(list($id,$dat)=each($tmp)) {
    if ($dat=="client") {
      // client
      $count++;
    } elseif (substr($dat["version"],0,3)=="map") {
      // map server
      $count+=$dat["details"]["users"];
    }
  }
  $buf=writeW(0x3714).writeL($count);
  net_write($login,$buf);
  // broadcast info to map servers
  reset($tmp);
  while(list($id,$dat)=each($tmp)) {
    if ($dat["version"]=="map_v1") {
      // map server
      net_write($dat,writeW(0x2B00).writeL($count));
      $GLOBALS["clients"][$id]=$dat;
    }
  }
}

function lost_client() {
  update_user_count();
}