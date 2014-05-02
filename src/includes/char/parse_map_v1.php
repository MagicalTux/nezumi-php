<?
/* Support for map server using protocol v1 not included
 * It will be coded when Yare will be called "STABLE" ...
 */

function parse_none_2AF8(&$nfo,$buf) {
  // map server v1 connected
  // refuse access
  $buf=writeW(0x2AF9).writeB(3);
  net_write($nfo,$buf);
  echo "ALERT: Got connexion attempt from char_v1 server. Rejected connexion.\n";
}
?>