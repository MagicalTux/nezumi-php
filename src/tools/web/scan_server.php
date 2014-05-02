<?
/* scan_server(ip,port,enable_compression)
 *  THIS FUNCTION WILL ALLOW YOU TO GET INFORMATIONS ABOUT YOUR SERVER
 * 
 */
function scan_server($ip,$port,$comp=true) {
  $sock=@fsockopen($ip,$port,$errno,$errstr,10);
  if (!$sock) return false;
  if (!function_exists("gzuncompress")) $comp=false;
  if ($comp) {
    @fwrite($sock,writeW(0x1010).writeW(4));
  }
  $buf=writeW(0x1800);
  @fwrite($sock,$buf);
  $dat=@fread($sock,2);
  $dat=readW($dat,0);
  if (($comp) and ($dat == 0x1011)) { // compression refused ?
    $dat=@fread($sock,2);  // just ignore it
    $dat=readW($dat,0);
  }
  if ($dat == 0x1010) {
    // compressed data
    $dat=@fread($sock,2);
    $dat=readW($dat,0);
    $dat=@fread($sock,$dat-4); // read entiere packet
    @fclose($sock);
    $dat=@gzuncompress($dat); // uncompress it
    if (!$dat) return false;
    if (readW($dat,0)!=0x1801) return false;
    $dat=substr($dat,4);
  } elseif ($dat == 0x1801) {
    $dat=@fread($sock,2); // read packet len
    $dat=readW($dat,0);
    $dat=@fread($sock,$dat); // read data
    @fclose($sock);
  } else { // unexpected answer code
    @fclose($sock);
    return false;
  }
  $dat=@unserialize($dat);
  return $dat;
}

function readW($buf,$pos) {
  $tmp=substr($buf,$pos,2);
  $res=unpack("vint",$tmp);
  $res=$res["int"];
  return $res;
}
function writeW($byte) {
  return pack("v",$byte);
}

?>