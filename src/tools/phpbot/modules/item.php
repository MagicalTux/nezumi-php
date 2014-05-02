<?
// Item search engine
// Requires res dir (will not check for it)
$item_table=array();
$item_val=array();

$fil=fopen("res/idnum2itemdisplaynametable.txt","rb");
while (!feof($fil)) {
  $lin=fgets($fil,4096);
  $lin=explode("#",$lin);
  $c=(int)$lin[0];
  $item_table[$c]=$lin[1];
}
fclose($fil);

$fil=fopen("res/item_db2.txt","rb");
while(!feof($fil)) {
  $lin=fgets($fil,4096);
  if (trim($lin)=="") continue;
  $lin=explode(",",$lin);
  $c=(int)$lin[0];
  if (!isset($item_table[$c])) continue;
  $s=trim(str_replace("_"," ",$lin[1]));
  $t=$item_table[$c];
  $item_table[$t]=$s;
  $item_table[$s]=$t;
  $item_val[$s]=$c;
  $item_val[$t]=$c;
}

$res=var_export($item_table,true);
$fil=fopen("test.log","w");
fputs($fil,$res);
fclose($fil);

$triggers["B:!item"]="appr_item";

function appr_item($nick,$chan,$msg,$user) {
  global $cnx,$item_table,$item_val;
  $item=substr($msg,5);
  $item=trim($item);
  $test=(int)$item;
  if (isset($item_table[$test])) {
    fputs($cnx,"NOTICE $nick :Item #".$test." : ".$item_table[$test].((isset($item_table[$item_table[$test]]))?" (".$item_table[$item_table[$test]].")":"")."\r\n");
    return;
  }
  if (strlen($item)<2) {
    fputs($cnx,"NOTICE $nick :You must specify at least two chars.\r\n");
    return;
  }
  reset($item_table);
  $f=false;
  $item=strtolower($item);
  $res=array();
  while(list($var,$val)=each($item_table)) {
//    if (substr($var,0,strlen($item))==$item) {
    if (strpos(strtolower($var),$item)!==false) {
      $f=true;
      $next="";
      if (isset($item_val[$val])) $next=" (".$item_val[$val].")";
      $res[]="NOTICE $nick :".$var." = ".$val.$next."\r\n";
    }
  }
  if (!$f) {
    fputs($cnx,"NOTICE $nick :No match found.\r\n");
    return;
  }
  if (count($res)>16) {
    fputs($cnx,"NOTICE $nick :Your search was too broad (got ".count($res)." results)\r\n");
    return;
  }
  reset($res);
  $f=false;
  while(list($var,$val)=each($res)) {
    if ($f) sleep(1);
    $f=true;
    fputs($cnx,$val);
  }
}