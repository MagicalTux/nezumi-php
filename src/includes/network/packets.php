<?
/* packets.php v1.0
 * Informations about packet len
 */

$GLOBALS["packets"]=packet_len_array();

function packet_len_array() {
  $res=array();
  $res[0x0064]=55; // client login (client to login server)
  $res[0x0065]=17; // client connected to char server
  $res[0x0066]=3;  // client selecting a char
  $res[0x0067]=37; // client creating a new char
  $res[0x0068]=46; // client deleting a char
  $res[0x0069]=-2; // client logged in (size stored at offset 2, as word)
  $res[0x006A]=23; // client logon refused.
  $res[0x006B]=-2; // char info packet
  $res[0x006C]=3;  // error from char-server
  $res[0x0071]=28; // char server sending respawn info to client
  $res[0x0187]=6;  // client noops
  $res[0x1010]=-2; // compressed data following
  $res[0x1011]=2;  // compressed tunnel forbidden
  $res[0x1800]=2;  // Fron_web query
  $res[0x1801]=-2; // answer
  $res[0x1802]=2;  // server coredata query
  $res[0x1803]=-2; // answer
  $res[0x2710]=76; // char_v1 server v1 login
  $res[0x2711]=3;  // answer to char_v1 login request
  $res[0x2712]=14; // char_v1 server asking for auth.
  $res[0x2713]=7;  // answer to auth. request from char_v1
  $res[0x2714]=6;  // char_v1 server v1 sending info about his number of clients
  $res[0x2716]=6;  // client has exited the char_v1 server (not used)
  $res[0x2AF8]=60; // map_v1 server connected
  $res[0x2AF9]=3;  // answer v1 to map_v1
  $res[0x2AFA]=-2; // map server sending its map list
  $res[0x2AFB]=3;  // map list received
  $res[0x2AFC]=14; // map server got client - checking access
  $res[0x2AFD]=-2; // answer to map server : client ok - here's client nfo
  $res[0x2AFE]=7;  // answer to map server : client rejected
  $res[0x2AFF]=6;  // map server sending user count
  $res[0x2B00]=6;  // char server informing map server of number of players
  $res[0x2B01]=-2; // player is saving data :)
  $res[0x2B02]=10; // player going back to select char screen
  $res[0x2B03]=-2; // update party data
  $res[0x2B04]=-2; // save new party data
  $res[0x2B05]=6;  // save new party data
  $res[0x3000]=2;  // deprecated packet
  $res[0x3001]=2;  // deprecated packet
  $res[0x3002]=2;  // deprecated packet
  $res[0x3708]=78; // char server v2.1 login
  $res[0x3709]=4;  // couldn't login : protocol unsupported
  $res[0x3710]=76; // char server v2 login
  $res[0x3711]=3;  // answer to char_v2 login query
  $res[0x3712]=14; // char_v2 asking for client auth
  $res[0x3713]=9;  // answer to query (contains version)
  $res[0x3714]=6;  // char_v2 sending info about client num
  $res[0x3715]=46; // char_v2 server asking for email-check
  $res[0x3716]=3;  // answer from login server (email valid or not ?) - DEPRECATED
  $res[0x3717]=7;  // answer from login server (email valid or not ?) - GOOD ONE
  $res[0x5F00]=51; // map_v2 server connected
  $res[0x5F01]=3;  // answer to map_v2 server
  $res[0x5F02]=-2; // map server sending info about his state.
  $res[0x5F03]=-2; // char server setting char_data mod
  $res[0x5F04]=-2; // map server sending char_data mod
  $res[0x5F05]=18; // map-server asking for ip/port of map-server hosting map x
  $res[0x5F06]=8;  // answer from char-server
  $res[0x5F07]=6;  // packet about client info (map to char)  
  return $res;
}