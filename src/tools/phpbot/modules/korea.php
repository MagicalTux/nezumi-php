<?

// Korean to english translator

// using WebLingo functions



// <input type="hidden" name="wl_ucp" value="1">

/*
<select name="wl_glossary">;
<!-- Glossaries -->
<option value="gl1" SELECTED >General</option>
<option value="gl2">Automotive</option>
<option value="gl3">Aviation/Space</option>
<option value="gl4">Chemistry</option>
<option value="gl5">Colloquial</option>
<option value="gl6">Computers/IT</option>
<option value="gl7">Earth Sciences</option>
<option value="gl8">Economics/Business</option>
<option value="gl9">Electronics</option>
<option value="gl10">Food Science</option>
<option value="gl11">Legal</option>
<option value="gl12">Life Sciences</option>
<option value="gl13">Mathematics</option>
<option value="gl14">Mechanical Engineering</option>
<option value="gl15">Medicine</option>
<option value="gl16">Metallurgy</option>
<option value="gl17">Military Science</option>
<option value="gl18">Naval/Maritime</option>
<option value="gl19">Photography/Optics</option>
<option value="gl20">Physics/Atomic Energy</option>
<option value="gl21">Political Science</option>                  </select>

wl_srclang = KO
wl_trglang = en

*/

$user_lang="en"; // user language

$triggers["B:!korean"]="do_korean";
$triggers["B:!tokorean"]="do_tokorean";
$triggers["B:!french"]="do_french";
$triggers["B:!tofrench"]="do_tofrench";
$triggers["B:!german"]="do_german";
$triggers["B:!togerman"]="do_togerman";
$triggers["B:!english"]="do_english";
$triggers["B:!toenglish"]="do_toenglish";
$triggers["B:!japanese"]="do_japanese";
$triggers["B:!tojapanese"]="do_tojapanese";
$triggers["B:!italian"]="do_italian";
$triggers["B:!toitalian"]="do_toitalian";
$triggers["B:!portuguese"]="do_portuguese";
$triggers["B:!toportuguese"]="do_toportuguese";
$triggers["B:!spanish"]="do_spanish";
$triggers["B:!tospanish"]="do_tospanish";

function do_korean($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,7));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]="KO";
  $post["wl_trglang"]=strtolower($user_lang);
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_tokorean($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,9));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]=strtoupper($user_lang);
  $post["wl_trglang"]="ko";
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_german($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,7));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]="DE";
  $post["wl_trglang"]=strtolower($user_lang);
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_togerman($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,9));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]=strtoupper($user_lang);
  $post["wl_trglang"]="de";
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_spanish($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,8));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]="ES";
  $post["wl_trglang"]=strtolower($user_lang);
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_tospanish($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,10));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]=strtoupper($user_lang);
  $post["wl_trglang"]="es";
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_italian($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,8));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]="IT";
  $post["wl_trglang"]=strtolower($user_lang);
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_toitalian($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,10));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]=strtoupper($user_lang);
  $post["wl_trglang"]="it";
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_french($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,7));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]="FR";
  $post["wl_trglang"]=strtolower($user_lang);
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_tofrench($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,9));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]=strtoupper($user_lang);
  $post["wl_trglang"]="fr";
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_portuguese($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,11));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]="PT";
  $post["wl_trglang"]=strtolower($user_lang);
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_toportuguese($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,13));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]=strtoupper($user_lang);
  $post["wl_trglang"]="pt";
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}


function do_english($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,8));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]="EN";
  $post["wl_trglang"]=strtolower($user_lang);
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_toenglish($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,10));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]=strtoupper($user_lang);
  $post["wl_trglang"]="en";
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_japanese($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,9));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]="JA";
  $post["wl_trglang"]=strtolower($user_lang);
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function do_tojapanese($nick,$chan,$msg,$user) {
  global $cnx,$user_lang;
  $msg=trim(substr($msg,11));
  if ($msg=="") {
    fputs($cnx,"NOTICE $nick :Hello ! I can translate Korean to English ! Just type !kiream <text> :)\r\n");
    return;
  }
  // translate $msg
  $post=array();
  $post["wl_url"]="";
  $post["wl_text"]=$msg;
//  $post["wl_ucp"]=1;
  $post["wl_glossary"]="gl1"; // gl6 for Computers/IT, etc...
  $post["wl_srclang"]=strtoupper($user_lang);
  $post["wl_trglang"]="ja";
  fputs($cnx,"NOTICE $nick :Translating...\r\n");
  $res=do_trans($post);
  if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

// /wl/Translate
// http://www.worldlingo.com/wl/Translate
function do_trans($post) {
  // connect & send form
  $fil=fopen("log.log","w");
  $sock=@fsockopen("www.worldlingo.com",80,$errno,$errstr,3);
  reset($post);
  switch($post["wl_srclang"]) {
    case "KO":$post["wl_text"]=@iconv("EUC-KR","EUC-KR",$post["wl_text"]); break;
    case "JA":if (!strpos($post["wl_text"],"\x82")) { $post["wl_text"]=@iconv("EUC-JP","Shift_JIS",$post["wl_text"]); } break;
    default:$post["wl_text"]=@iconv("ISO-8859-15","ISO-8859-1",$post["wl_text"]); break;
  }
  $data="";
  while(list($var,$val)=each($post)) {
    if ($data!="") $data.="&";
    $data.=$var."=".rawurlencode($val);
  }
  // wl_url=&wl_text=%BE%CB+%B7%C1+%B5%E5+%B8%B3+%B4%CF+%B4%D9+&wl_glossary=gl1&wl_srclang=KO&wl_trglang=fr
  if (!$sock) return false;
  $req="POST /wl/Translate HTTP/1.1\r\n";
  $req.="Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, ";
  $req.="application/x-shockwave-flash, application/vnd.ms-excel, ";
  $req.="application/vnd.ms-powerpoint, application/msword, */*\r\n"; // formats MIME acceptés
  $req.="Accept-Language: fr\r\n"; // langue pour IE5.0 FR :o)
  $req.="Content-Type: application/x-www-form-urlencoded\r\n";
  $req.="Accept-Encoding: gzip, deflate\r\n";  // normalement lycos n'en fait pas usage ...
  $req.="User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)\r\n"; // MSIE 6.0 WinXP and .net framework
  $req.="Host: www.worldlingo.com\r\n";
  $req.="Content-Length: ".strlen($data)."\r\n";  // longueur des données POST
  $req.="Connection: Close\r\n";  // pas de Keep-Alive
  $req.="Referer: http://www.worldlingo.com/products_services/worldlingo_translator.html\r\n";
  $req.="Cache-Control: no-cache\r\n\r\n".$data;
  fputs($fil,$req);
  fputs($sock,$req);  // send query
  //               <td colspan="2">             <textarea name="wl_result" rows="7" wrap="VIRTUAL" class="textbox" cols="46" readonly onKeyDown='wl_text=""; wl_text.focus(); return true;'> Actuellement je Ni et le programme où l'article est copié n'existe pas réalité bien pour familiariser le contenu d'une dignité de, comparé à pour ne pas mettre dessus des dommages ci-dessus, lui confiant donne à chaque étoile une attention de ceux tout le monde de Yoo.
  $res=false;
  while(!feof($sock)) {
    $lin=fgets($sock,4096);
    fwrite($fil,$lin);
    if (strpos($lin,"name=\"wl_result\"")) {
//      fclose($sock);
      // clean up
      $lin=ereg_replace("<([^>]*)>","",$lin);
      $lin=trim($lin);
      $lin=ereg_replace("( +)"," ",$lin);
      $res=$lin;
//      return $lin;
    }
  }
  fclose($fil);
  fclose($sock);
  switch($post["wl_trglang"]) {
    case "ko":$res=@iconv("UTF-8","EUC-KR",$res); break;
    case "ja":$res=@iconv("UTF-8","EUC-JP",$res); break;
    default:$res=@iconv("UTF-8","ISO-8859-15",$res); break;
  }
  return $res;
}

?>