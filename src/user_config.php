<?
/* Configuration file
 */

$status["login"]["ip"]="0.0.0.0"; // IP for listening
$status["login"]["port"]=6900;  // port for login server
$status["login"]["serverinfo"]=true; // server information system (OS, etc.) enable it if you want to use the public directory
$status["login"]["pass_method"]="plain"; // good values : plain, md5, sha1 - used only to *read* accounts. 
// Nots about password storage method : You shouldn't change it and your subscription page *must* do the same encryption
$status["login"]["auto_registration"]=false; // enable users to register to the server adding a _M or _F ?

// Web directory informations !
$status["dir"]["enabled"]=true;  // will we enable registration on the public servers directory ?
$status["dir"]["name"]="UnNamed";   // choose the name of your server. Ex. yRO
$status["dir"]["desc"]="I didn't set the desctription of my server"; // description

$status["char"]["ip"]="0.0.0.0"; // IP for listening
$status["char"]["port"]=6121;   // char server port
$status["char"]["name"]="Nezumi"; // char server name
$status["char"]["login"]="s1"; // char server's login
$status["char"]["pass"]="pass"; // char server's pass
$status["char"]["login_ip"]="127.0.0.1"; // IP to use to contact login server
$status["char"]["login_port"]=$status["login"]["port"]; // port of login server
$status["char"]["own_ip"]="127.0.0.1"; // Public IP (used by clients to connect to the char server)
$status["char"]["map_server_prot"]="PvPGN_v1.1.0.dat"; // protocol for user profile transmission

// Nezumi map-server discontinued
$status["map"]["ip"]="0.0.0.0"; // IP of map server
$status["map"]["port"]=0; // MAP servers shouldn't have a port
$status["map"]["own_ip"]="127.0.0.1"; // Public IP (used by clients to connect to the map server)
$status["map"]["login"]="s1"; // map server's login
$status["map"]["pass"]="pass"; // map server's pass

$status["global"]["compress_link"]=false; // should we enable GZip compression for links ?
$status["global"]["database"]="mysql";   // choose which type of database to use (only mysql supported)

// MySQL specific data
$status["mysql"]["host"]="localhost"; // host 4 MySQL connextion
$status["mysql"]["port"]=0;  // mysql server's port. Default : 3306
$status["mysql"]["user"]="nezumi"; // login
$status["mysql"]["pass"]="server_pass";  // pass
$status["mysql"]["base"]="nezumi"; // database
$status["mysql"]["compress"]=false; // true to enable MySQL link compression

?>