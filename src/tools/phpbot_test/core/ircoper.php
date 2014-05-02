<?
  if (defined("CORE_IRCOPER")) {
    fatal("ERROR: Core module IRCOPER already loaded");
  }
  define("CORE_IRCOPER","Core_IRCOper v0.1");
  $core_ext[]="CORE_IRCOPER";
  $triggers["R:381"]="nowop";
  // uncomment the following line to enable IRCOper mode
//  $triggers["C:do_oper"]="do_oper";
  function nowop($dest,$msg) {
    putlog("CORE_IRCOPER: Now IRCOp");
    // put here things to do when acquiring ircop privileges
  }
  function do_oper() {
    global $cnx;
    $oper_login="login";  // set up these vars
    $oper_pass="pass";
    fputs($cnx,"OPER ".$oper_login." ".$oper_pass."\r\n");
  }
  putlog("CORE_IRCOPER: Loaded ".CORE_IRCOPER);
  echo "CORE: Loaded ".CORE_IRCOPER." ...\r\n";
?>
