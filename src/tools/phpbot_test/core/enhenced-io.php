<?
  if (defined("CORE_IO")) {
    fatal("ERROR: Core module IO already loaded");
  }
  define("CORE_IO","Core_Enhenced-I/O v0.3");
  $core_ext[]="CORE_IO";
  function doautojoin () {
    global $autojoin,$cnx,$j;
    if (!isset($autojoin)) return true;
    reset($autojoin);
    while (list($chan,$val)=each($autojoin)) {
      putlog("CORE_IO: Sending join request to $chan ...");
      fputs($cnx,"JOIN ".$chan."\r\n");
      if ($pos=strpos($chan," ")) $chan=substr($chan,0,$pos);
      $j[strtolower($chan)]=1;
    }
  }
  
  function readline() {
    set_time_limit(0);
    global $cnx,$botnick,$j,$triggers,$botquitpass;
    $line=@fgets($cnx,8192);
    if (!$line) {
      putlog("CORE_IO: Read error : Connecion reset by peer");
      return false;
    }
    $line=str_replace("\n","",str_replace("\r","",$line));
    if (ereg("^(:?)([^ ]+)( +)([^ ]+)( +)([^ ]+)( *)(:?)(.*)$",$line,$arr_line)) {
//      $line=ereg_replace("^(:?)([^ ]+)( +)([^ ]+)( +)([^ ]+)( *)(:?)(.*)$","\\2|||\\4|||\\6|||\\9",$line);
//      $line=explode("|||",$line);
      unset($answer);
      $answer["server"]=$arr_line[2];
      $answer["code"]=$arr_line[4];
      $answer["dest"]=$arr_line[6];
      $answer["message"]=$arr_line[9];
      $answer["type"]="unknown";
      if (isset($triggers["R:".$answer["type"]])) {
        $call_function=$triggers["R:".$answer["type"]];
        $call_function($answer["dest"],$answer["message"]);
      } elseif (substr($answer["message"],0,1) == chr(1)) {
        // CTCP ...
        if ($answer["dest"] == $botnick) {
          if ($answer["code"] == "PRIVMSG") {
            // CTCP ...
            // server = nick!user@host
            $answer["type"]="CTCP";
            $nick=strpos($answer["server"],"!");
            $nick=substr($answer["server"],0,$nick);
            $answer["message"]=str_replace(chr(1),"",$answer["message"]);
            $pos=strpos($answer["message"]," ");
            if (!$pos) $pos=strlen($answer["message"]);
            putlog("CORE_IO: Received CTCP ".$answer["message"]);
            switch (strtoupper(substr($answer["message"],0,$pos))) {
              case "VERSION": fputs($cnx,"NOTICE $nick :".chr(1)."VERSION ".VERSION.chr(1)."\r\n"); break;
              case "PING": fputs($cnx,"NOTICE $nick :".chr(1)."PING ".substr($answer["message"],5).chr(1)."\r\n"); break;
              case "TIME": fputs($cnx,"NOTICE $nick :".chr(1)."TIME ".date("d / m / Y  G:m:s").chr(1)."\r\n"); break;
              case "QUIT":
                if (trim(substr($answer["message"],5))==$botquitpass) {
                  putlog("CORE_IO: Quit message received from $nick .");
                  $ttrig=$triggers;
                  reset($ttrig);
                  while (list($trig,$func)=each($ttrig)) {
                    if (substr($trig,0,2)=="Q:") $func();
                  }
                  fputs($cnx,"QUIT :Exiting (requested by $nick)\r\n");
                  exit;
                } else {
                  putlog("CORE_IO: Quit message from $nick with wrong password.");
                  fputs($cnx,"NOTICE $nick :This password is incorrect !!\r\n");
                }
            }
          }
        }
      } elseif (substr($answer["server"],0,strlen($botnick))==$botnick) {
        switch($answer["code"]) {
          case "JOIN":
            $chan=$answer["dest"];
            if (substr($chan,0,1)==":") $chan=substr($chan,1);
            $joined[$chan]=1;
            if ($j[strtolower($chan)]!= 1) {
              putlog("CORE_IO: Joined $chan but didn't want. Sending PART request");
              fputs($cnx,"PART $chan\r\n");
            } else {
              putlog("CORE_IO: Joined $chan");
              unset($joining[$chan]);
            }
            // check for joining triggers...
            if (isset($triggers["J:".strtolower($chan)])) {
              // join trigger ...
              putlog("CORE_IO: Calling ".$triggers["J:".strtolower($chan)]." function on joining $chan");
              $triggers["J:".strtolower($chan)]($chan);
            }
            break;
          case "PART":
            $chan=$answer["dest"];
            if (substr($chan,0,1)==":")
            $chan=substr($chan,1);
            unset($joined[$chan]);
            if (isset($autojoin[strtolower($chan)])) {
              $j[strtolower($chan)]=1;
              fputs($cnx,"JOIN ".$chan."\r\n");
              putlog("CORE_IO: Parted from AUTOJOIN chan $chan");
            } else {
              putlog("CORE_IO: Parted from chan $chan");
            }
            break;
          case "NICK":
            // :Ttest!~username@FF-ST-2AA3CAFC.abo.wanadoo.fr NICK :TotoTest
            $newnick=$answer["dest"];
            if (substr($newnick,0,1)==":") $newnick=substr($newnick,1);
            if (substr($answer["server"],0,strpos($answer["server"],"!"))==$botnick) {
              echo "BOT CHNICK: $botnick to $newnick \r\n";
              $botnick=$newnick;
            }
            break;
        }
      }
      switch ($answer["code"]) {
        case "KICK":
          // :Bourreau!~admin@ASt-Lambert-102-1-2-253.abo.wanadoo.fr KICK #Test Bourreau|TELNET :Bourreau
          if (substr($answer["message"],0,strlen($botnick))==$botnick) {
            $j[strtolower($answer["dest"])]=1;
            putlog("CORE_IO: Kicked from ".$answer["dest"].". Trying to rejoin ...");
            fputs($cnx,"JOIN ".$answer["dest"]."\r\n"); // autojoin on kick .....
          }
      }
      return $answer;
    } else {
      // PING ?
      if (substr($line,0,5)=="PING ") {
        // Do a pong to that ping ..
        fputs($cnx,"PONG ".substr($line,5)."\r\n");
      }
    }
    return true;
  }
  
  function waitfor($code) {
    $cod="";
    while ($cod!=$code) {
      $recv=readline();
      if (is_array($recv)) {
        $cod=$recv["code"];
      } else {
        if (!$recv) return false;
      }
    }
  }
  putlog("CORE_IO: Loaded ".CORE_IO);
  echo "CORE: Loaded ".CORE_IO." ...\r\n";
?>