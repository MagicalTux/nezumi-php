<?
// google bot
$triggers["B:!google"]="google_bot";

$google_antiflood=array();

function google_bot($nick,$chan,$msg,$user) {
  // some config here
  
  $spam_timer=30; // number of searches between each user query
  $max_results=5; // limit max number of results (limited alos by google)
  
  global $cnx,$google_antiflood;
  if (isset($google_antiflood[$nick])) {
    if ($google_antiflood[$nick]>time()) {
      $t=$google_antiflood[$nick]-time();
      fputs($cnx,"NOTICE $nick :You must wait ".$t." secs before using this bot again.\r\n");
      return;
    }
  }
  $google_antiflood[$nick]=time()+$spam_timer;
  $numres=1;
  fputs($cnx,"NOTICE $nick :Searching...\r\n");
  $msg=trim(substr($msg,8));
  $pos=strpos($msg," ");
  if ($pos) {
    $test=trim(substr($msg,0,$pos));
    $test2=(int)$test;
    if ($test2>0) {
      $numres=$test2;
      $msg=trim(substr($msg,$pos));
    }
  }
  if (($numres>$max_results) and ($max_results>0)) $numres=$max_results;
  $msg=iconv("ISO-8859-15","UTF-8",$msg);
  $msg=rawurlencode($msg);
  
  $fil=fopen("http://www.google.com/search?hl=en&ie=UTF-8&oe=UTF-8&q=".$msg,"r");
  if (!$fil) {
    fputs($cnx,"NOTICE $nick :Seems that google is down...\r\n");
    return;
  }
  
  $res="";
  while(!feof($fil)) $res.=fread($fil,8192);
  fclose($fil);
  
  $results=array();
  
  while(ereg("<font([ \r\n\t]+)color=#008000>([^ ]+)( +)-(.+)</font>",$res,$regs)) {
    $url=$regs[2];
    $res=str_replace($regs[2]." ","",$res); // exclude result
    $results[]=$url;
  }
  unset($res); // free memory
  
  if (count($results)<1) {
    fputs($cnx,"NOTICE $nick :Your search was unsuccessful.\r\n");
    return;
  }
  
  reset($results);
  
  $nres=0;
  
  while (list($n,$url)=each($results)) {
    $nres++;
    if ($nres>$numres) return; // only display 3 first results
    fputs($cnx,"PRIVMSG $chan :Google : http://".$url."\r\n");
  }
}
?>