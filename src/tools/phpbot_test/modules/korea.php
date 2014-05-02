<?

// Translator by MagicalTux, edited by Mra

// using WorldLingo functions

$triggers["B:!translate"]="do_translate";
$triggers["a:!bothelp"]="do_bothelp"; // use A if no parameter required

function do_bothelp($nick,$chan,$msg,$user) { // Help needed? XD
	global $cnx;
	    fputs($cnx,"NOTICE $nick :You can find help at http://www.ro-galaxy.de/bothelp.htm\r\n");
    return;
}

function trans_do_code($lang) {
  $lang=trim(strtolower($lang));
  if (strlen($lang)>3) $lang=substr($lang,0,3);
  switch($lang) {
    case "eng":return "en";
    case "ger":return "de";
    case "dut":return "nl";
    case "chi":return "zh_tw"; // traditional
    case "fre":return "fr";
    case "ita":return "it";
    case "jap":return "jp";
    case "kor":return "ko";
    case "por":return "pt";
    case "gre":return "el";
    case "rus":return "ru";
  }
  return "en"; // default one
}

function do_translate($nick,$chan,$msg,$user) {
	global $cnx;
	if (!($pos=strpos($msg," "))) return do_bothelp($nick,$chan,$msg,$user);
	$msg=trim(substr($msg,$pos+1)); // remove command prefix
	if (!($pos=strpos($msg," "))) return do_bothelp($nick,$chan,$msg,$user);
	$lang_from=substr($msg,0,$pos); // translate from this language
	$msg=trim(substr($msg,$pos));
	if (!($pos=strpos($msg," "))) return do_bothelp($nick,$chan,$msg,$user);
	$lang_to=substr($msg,0,$pos); // translate to this one
	$msg=trim(substr($msg,$pos));
	if ($msg=="") return do_bothelp($nick,$chan,$msg,$user);
	
	// translate msg codes
	$lang_from=strtoupper(trans_do_code($lang_from));
	$lang_to=strtolower(trans_do_code($lang_to));
	
	echo "From : $lang_from to $lang_to \n";
	
	// translate $mes 
	$post=array();
	$post["wl_url"]="";
	$post["wl_text"]=trans_preprocess($msg,$lang_from);
	$post["wl_glossary"]="gl1";
	$post["wl_srclang"]=$lang_from;
	$post["wl_trglang"]=$lang_to;
	fputs($cnx,"NOTICE $nick :Translating...\r\n");
	$res=do_trans($post);
	if (!$res) {
    fputs($cnx,"NOTICE $nick :Sorry I couldn't get your text translated.\r\n");
    return;
  }
  $res=trans_postprocess($msg,$lang_to);
  fputs($cnx,"PRIVMSG $chan :".$res."\r\n");
  return;
}

function trans_preprocess($mes,$lang) {
  $lang=strtolower($lang);
  switch($lang) {
    case "ko":return @iconv("ISO-8859-1","EUC-KR",$mes);
    case "ja":return @iconv("ISO-2022-JP","EUC-JP",$mes);
//    case "ZH_TW":$post["wl_text"]=@iconv("BIG5","BIG5",$post["wl_text"]); break;
    case "ru":return @iconv("UTF-8","UTF-8",$mes);
    default:return $mes;
  }
}

function trans_postprocess($mes,$lang) {
  $lang=strtolower($lang);
  switch($lang) {
    case "ko":return @iconv("UTF-8","EUC-KR",$mes);
    case "ja":return @iconv("UTF-8","ISO-2022-JP",$mes);
    case "zh_tw":return @iconv("UTF-8","BIG5",$res);
//    case "ru":$res=@iconv("UTF-8","UTF-8",$res); break;
    case "el":return @iconv("UTF-8","ISO-8859-7",$mes);
    default:return @iconv("UTF-8","ISO-8859-15",$mes);
  }
}

function do_trans($post) {
  // connect & send form
  $fil=fopen("log.log","w");
  $sock=@fsockopen("www.worldlingo.com",80,$errno,$errstr,3);
  reset($post);
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
  return $res;
}

?>