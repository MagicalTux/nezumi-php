<?
// Eggdrop PHP ? // :)
// Version 0.0 ....
if (!file_exists("config.php")) {
  echo "Error : please create the config.php file ....";
  exit;
}
include("config.php");
if (!$runserv) {
  echo "Error : please edit the 'config.php' file before running the Eggdrop ...";
  exit;
}
function fatal($message) {
  echo $message."\r\n";
  fputs(LOG_OUT,$message."\r\n");
  fclose(LOG_OUT);
  exit;
}
function putlog($message) {
  fputs(LOG_OUT,$message."\r\n");
  fflush(LOG_OUT);
}
set_time_limit(0);
$core_ext=array();
$curserv=0;
Define("VERSION","PHP/Eggdrop ( PHP/NC ) v0.1 by MagicalTux <w@ff.st>");
$logfile=ereg_replace("[^a-zA-Z0-9]","",$botnicko);
Define(LOG_OUT,fopen($logfile.".log","a"));
echo "Welcome to ".VERSION." ...\r\n";
putlog("CORE: ".date("Y/m/d G:m:s")." : Loaded core system version ".VERSION);
$triggers = array();
echo "\r\nLoading core extensions ...\r\n";
$dir=@opendir("core");
if (!$dir) {
  fatal("ERROR: Core dir not found. Please download original version of the eggdrop.");
  exit;
}
while ($fil=readdir($dir)) if (substr($fil,-4)==".php") include("core/".$fil);
closedir($dir);
echo "\r\nLoading user defined extensions ...\r\n";
$dir=@opendir("modules");
if (!$dir) {
  echo "WARN: Modules dir not found.\r\n";
  putlog("CORE WARNING: Modules directory not found");
} else {
  while ($fil=readdir($dir)) if (substr($fil,-4)==".php") include("modules/".$fil);
}
closedir($dir);
echo "I have ".count($ircserv)." IRC servers to connect to.\r\n";
echo "I'm running ".count($triggers)." user-defined triggers.\r\n\r\n";
$curserv=-1; // 
while (1) {
  $curserv++;
  if ($curserv > (count($ircserv)-1) ) $curserv=0;
  if (!isset($ircserv[$curserv])) {
    echo "ERROR: No server to connect !!\r\n";
    exit;
  }
  $curirc=$ircserv[$curserv];
  $port=6667;
  if ($pos=strrpos($curirc,":")) {
    $port=substr($curirc,$pos+1);
    $curirc=substr($curirc,0,$pos);
  }
  if (connect($curirc,$port)) {
    doautojoin();
    $ans=true;
    while ($ans!==false) { // Fixed by MagicalTux : disconnextion wasn't detected
      $ans=readline();
      if (is_array($ans)) {
        $tmp=$triggers;
        reset($tmp);
        while(list($trig,$func)=each($tmp)) {
          $dat=$ans["server"];
          $pos=strpos($dat,"!");
          $nick=substr($dat,0,$pos);
          $dat=substr($dat,$pos+1);
          $type=substr($trig,0,1);
          $tdat=substr($trig,2);
          if (($type=="A") and ($ans["message"]==$tdat)) {
            $func($nick,$ans["dest"],$ans["message"],$dat);
          } elseif (($type=="a") and (strtolower($ans["message"])==strtolower($tdat))) {
            $func($nick,$ans["dest"],$ans["message"],$dat);
          } elseif (($type=="B") and (strtolower(substr($ans["message"],0,strlen($tdat)))==strtolower($tdat))) {
            $func($nick,$ans["dest"],$ans["message"],$dat);
          }
        }
      }
    }
  }
  if (defined("QUIT")) {
    fclose($cnx);
    fatal("CORE: QUIT received. Exitting ... ( ".QUIT." )");
  }
}
?>
