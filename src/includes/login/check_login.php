<?
/* Check Login
 * This script will check login from database server
 * No rehash is needed since a request is mae each time a loggin atemp is made
 * This could slow down the server but allows instant account creation
 *
 * Return code :
 * 0 = Unregistered ID
 * 1 = Bad password
 * 2 = ID has expired
 * 3 = Rejected from Server
 * 4 = Blocked by GM Team
 * 5 = Game file EXE is not the lastest version (protocol mismatch)
 * 6 = You are prohibed to login til ..
 * 7 = Server overpopulated
 * 8 = No MSG
 */
function check_login($userid,$pass,$serv=false,$client=array()) {
  $cfg=$GLOBALS["config"];
  $res=db_find("account_list",array('username','password','sex','id','gm','banned','email_valid','email'),array('username' => $userid));
    if (!is_array($res)) {
    // check for registration
    if ($serv) return false; // servers arent allowed to register :)
    $t=substr($userid,-2);
    if (!(($t=="_M") or ($t=="_F")) ) return 0; // not appended _M or _F
    if (!$cfg["login"]["auto_registration"]) return 3; // 3=Rejected
    $sex=substr($userid,-1);
    $userid=substr($userid,0,strlen($userid)-2);
    $userpass=$pass;
    // encrypt the password for it to work the next time^^
    switch(strtolower($cfg["login"]["pass_method"])) {
      case "md5":$pass=md5($userpass); break;
      case "sha1":$pass=sha1($userpass); break;
      case "plain":break;
      // if configuration is broken, let's just use plain passwords
    }
    $res=array();
    $res["username"]=$userid;
    $res["password"]=$pass;
    $res["sex"]=$sex;
    $res["gm"]="N";
    $res["email"]="";
    $id=db_insert("account_list",$res); // insert new account
    if (!$id) return 3; // didn't work
    // now the account *should* be inserted, we have to check the login again... let's call ourself again ^^
    return check_login($userid,$userpass,$serv,$client);
  }
  $res=$res[0]; // get only first row if many
  $userpass=$pass;
  switch(strtolower($cfg["login"]["pass_method"])) {
    case "md5":$pass=md5($userpass); break;
    case "sha1":$pass=sha1($userpass); break;
    case "plain":break;
    // if configuration is broken, let's just use plain passwords
  }
  if ($res['password'] != $pass) return ($serv)?false:1;
  $userid=$res['username'];
  $tmp=$GLOBALS["auth"];
  if (isset($tmp[$userid])) {
    // seems to be already logged in
      return ($serv)?false:10; // already logged in
  }
  if ($serv) {
    if ($res['sex'] != "S") {
      return false;
    } else {
      return true;
    }
  }
  if ($res['sex'] == "S") return 3;
  if ($res['banned'] == "Y") return 4; // banned by GM team
  if ($res['email_valid'] != "Y") return 3; // email not validated !
  $client["server"]=null;
  $accid=$res['id'];
  db_update_login($accid);
  if ($res["gm"] == "Y") {
    $client["gm"]=1;
      $accid+=100000; // enable GM abilities on v1 char server ;)
  } else {
    $client["gm"]=0;
  }
  $client["sex"]=0; // sex
  if ($res["sex"] == "M") $client["sex"]=1;
  $client["seed1"]=rand(); // login session phase 1
  $client["seed2"]=rand(); // login session phase 2
  $client["accountid"]=$accid; // account ID
  $client["userid"]=$userid;
  $client["email"]=($res["email"])?$res["email"]:$userpass;  // if email isn't set, let's use user's password
  $client["date"]=mk_str_date();
  $client["playing"]=false;
  $GLOBALS["auth"][$client["accountid"]]=$client; // store informations
  return $client;
}
?>