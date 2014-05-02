<?
/* Mini funcs
 * Some little useful functions 
 * Very useful when translating C++ code to PHP
 * Bytes order : little endian byte order
 */
function readP($buf,$pos,$maxlen=24) {
  $tmp=substr($buf,$pos);
  $pos=strpos($tmp,"\x00");
  if (!$pos) $pos=strlen($tmp);
  if ($pos>$maxlen) $pos=$maxlen;
  $tmp=substr($tmp,0,$pos);
  return $tmp;
}

function ToLen($str,$len,$chr="\x00") {
  if (strlen($str)>$len) $str=substr($str,0,$len);
  if (strlen($str)==$len) return $str;
  $str.=str_repeat("\x00",$len-strlen($str));
  return $str;
}

function readW($buf,$pos) {
  $tmp=substr($buf,$pos,2);
  $res=unpack("sint",$tmp);
  $res=$res["int"];
  return $res;
}

function readSW($fp) { // read Stream Word
  $tmp=fread($fp,2);
  $res=unpack("sint",$tmp);
  $res=$res["int"];
  return $res;
}

function readIP($buf,$pos) {
  $tmp=substr($buf,$pos,4);
  $tmp=unpack("Nlong",$tmp);
  return $tmp["long"];
}

function readL($buf,$pos) {
  $tmp=substr($buf,$pos,4);
  $tmp=unpack("llong",$tmp);
  return $tmp["long"];
}

function readSL($fp) { // read Streal Long
  $tmp=fread($fp,4);
  $tmp=unpack("llong",$tmp);
  return $tmp["long"];
}

function readSB($fp) { // read Stream Byte
  $tmp=fread($fp,1);
  $tmp=unpack("cshort",$tmp);
  return $tmp["short"];
}

function readB($buf,$pos) {
  $tmp=substr($buf,$pos,1);
  $tmp=unpack("cshort",$tmp);
  return $tmp["short"];
}

function writeW($byte) {
  return pack("s",$byte);
}

function writeIP($byte) {
  return pack("N",$byte);
}

function writeL($byte) {
  return pack("l",$byte);
}

function writeB($byte) {
  return pack("c",$byte);
}

function mk_str_date() {
  return date("Y-m-d H:i:s").".000";
}
?>