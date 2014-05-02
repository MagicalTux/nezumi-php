<?
/* Net_check()
 * will check data from clients
 */

function net_check() {
  $sock=$GLOBALS["socket"];
  if ((!is_object($sock)) or (!$sock->socket)) {
    // socket isn't listening ...
    init_network(); // this function will try to open the socket
    // we'll check this socket on next loop
  } else {
    // listening socket online
    while($res=$sock->accept(0)) {
      $client=net_client_struct();
      $client["socket"]=$res;
      $GLOBALS["clients"][]=$client;
    }
  }
  $tmp=$GLOBALS["clients"];
  reset($tmp);
  while (list($id,$nfo)=each($tmp)) {
    $nfo=$GLOBALS["clients"][$id];
    $nfo["client_id"]=$id;
    net_process($nfo);
    if ($nfo) {
      if (strlen($nfo["buf_out_"])>$nfo["SendQ_size"]) {
        // SendQ exceed
        unset($GLOBALS["clients"][$id]);
        if (function_exists("lost_client")) lost_client();
      } else {
        $GLOBALS["clients"][$id]=$nfo;
      }
    } else {
      unset($GLOBALS["clients"][$id]);
      if (function_exists("lost_client")) lost_client();
    }
  }
  $tmp=$GLOBALS["clients"];
  reset($tmp);
  while(list($id,$nfo)=each($tmp)) {
    net_send($nfo);
    if ($nfo) {
      $GLOBALS["clients"][$id]=$nfo;
    } else {
      unset($GLOBALS["clients"][$id]);
      if (function_exists("lost_client")) lost_client();
    }
  }
}

function net_process(&$nfo) {
  // process data from a socket
  $packets=$GLOBALS["packets"];
  $config=$GLOBALS["config"];
  if (!$nfo["socket"]) return;
  $dat=read_packet($nfo["socket"]);
  if ($dat === false) {
    // this socket was remotely closed (or has an error)
    $nfo=false;
    return;
  }
  $nfo["buf_in"].=$dat;
  while (strlen($nfo["buf_in"])>=2) {
    $type=readW($nfo["buf_in"],0);
    if (!isset($packets[$type])) {
      // unknown packet ...
      // in this case : empty buffer and return
      if (DEBUG) echo "Got unknown packet 0x".dechex($type)." from ".$nfo["version"]." of probable len : ".strlen($nfo["buf_in"])." !\n";
      $nfo["buf_in"]="";
      return;
    }
    $len=$packets[$type];
    if ($len<0) {
      if (strlen($nfo["buf_in"])<(abs($len)+2)) return;
      $len=readW($nfo["buf_in"],abs($len)); // real len
      if (strlen($nfo["buf_in"])<$len) return;
      $packet=substr($nfo["buf_in"],0,$len); // extract packet
      $nfo["buf_in"]=substr($nfo["buf_in"],$len); // and remove it
    } elseif ($len>0) {
      if (strlen($nfo["buf_in"])<$len) return;
      $packet=substr($nfo["buf_in"],0,$len); // extract packet
      $nfo["buf_in"]=substr($nfo["buf_in"],$len); // and remove it
    } else {
      // zero case !
      if (DEBUG) echo "Got bugged packet 0x".dechex($type)." from ".$nfo["version"]." !\n";
      $nfo["buf_in"]="";
      return;
    }
    if ($type == 0x1010) {
      // special case : gzipped data
      if (!$config["global"]["compress_link"]) {
        // this link shouldn't be compressed, packet will be ignored
        $nfo["compress"]=false;
        net_write($nfo,writeW(0x1011));
      } else {
        $nfo["compress"]=true; // enable gzip compression
        $packet=substr($packet,4); // remove header
        if ($packet != "") { // case : to check if compression is available, send an empty packet (4 bytes long)
          $packet=gzuncompress($packet);
          if ($packet) {
            $nfo["buf_in"]=$packet.$nfo["buf_in"]; // put data in front of buffer
          }
        }
      }
    } elseif ($type == 0x1011) {
      // Compression denied
      $nfo["compress"]=false;
    } else {
      // find a function for it
      $str_type=dechex($type);
      while(strlen($str_type)<4) $str_type="0".$str_type;
      $func="parse_".$nfo["version"]."_".$str_type; // eg : parse_none_0064() : will parse data from client
      if (function_exists($func)) {
        $func($nfo,$packet);
      } else {
        if (DEBUG) echo "Unparsed packet : type:".$nfo["version"]." code:0x".$str_type."\n";
        if (DEBUG) {
          // dump this packet
          $fil=fopen("dump.".$nfo["version"].".".$str_type."-".getmicrotime().".log","w"); // unique filename
          fwrite($fil,$packet);
          fclose($fil);
        }
        return;
      }
    }
  }
}