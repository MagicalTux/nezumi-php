<?
  $triggers["a:!status"]="nezumi_status";

  
function nezumi_status($nick,$chan,$msg,$user) {
  global $cnx;
	$res=scan_server("62.93.233.204",6900);   // change this to your server's informations
	if (!$res) {
	  fputs($cnx,"PRIVMSG $chan :Sorry but the server seems to be currently down.\r\n");
	  return;
	}
	reset($res);
	while (list($nm,$dat)=each($res)) {
	  fputs($cnx,"PRIVMSG $chan :".$dat["name"]." : ".$dat["users"]." players - version ".$dat["version"]);
	  if ($dat["version"]>=2) fputs($cnx," (protocol v".$dat["protocol"].")");
	  fputs($cnx," - uptime : ".do_uptime($dat["uptime"]));
	  fputs($cnx,"\r\n");
	}
}

?>
