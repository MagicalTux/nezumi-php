<?
/* Parser for NPC scripts
 * Coded by MagicalTux
 *
 * NOTE: NPC Code will not be avalable now
 *  it's here only for developpement purpose
 */

function npc_parse($filename) {
  $stringtable=array(); // needed for correct parsing
  // parse the "filename" script
  if (DEBUG) echo "Starting parsing of file '".$filename."' ...\n";
  $fil=@fopen($filename,"r");
  if (!$fil) return parse_error($filename,0,101);
  $lines=array();
  $num=0;
  if (DEBUG) echo "Now parsing lines of file...\n";
  while(!feof($fil)) {
    $lin=@fgets($fil,8192);
    $num++;
    $lin=parse_line($filename,$lin,$num,$stringtable);
    if ((!is_array($lin)) and (!is_null($lin))) return $lin;
    if (!is_null($lin)) $lines[]=$lin;
  }
  fclose($fil);
  if (DEBUG) echo "Lines parsed. Now initialising debug vars...\n";
  // now our lines are parsed
  // we have to create lines-array for debbuging purposes
  $dbug_larr=array(); $p=0;
  $f_cont="";
  reset($lines);
  while(list($num,$dat)=each($lines)) {
    $dbug_larr[$p]=$dat["line"];
    $p+=strlen($dat["cont"]);
    $f_cont.=$dat["cont"];
  }
  unset($lines);
  // remove /* */ comments
  while (($pos=strpos($f_cont,"/*"))!==false) {
    $pos2=strpos($f_cont,"*/",$pos);
    if (!$pos2) {
      $pos2=strlen($f_cont);
      parse_error($filename,parse_find_line($dbug_larr,$pos),54);
    }
    $len=$pos2-$pos;
    $f_cont=substr($f_cont,0,$pos).substr($f_cont,$pos2+2);
    $dbug_larr=parse_remove_char($dbug_larr,$pos,$len);
  }
  if (DEBUG) echo "Reducting file into tokens...\n";
  // now let's parse f_cont in tokens
  $toklist=array();
  $tok=strtok($f_cont,";"); // default instruction separator is ";"
  $instruct=array();
  $p=0;
  while($tok!==false) {
    $p+=1+strlen($tok);
    $tok=trim($tok);
    if (strlen(trim($tok))>3) {
      $l=parse_find_line($dbug_larr,$p);
      $tok=str_replace(" ","",$tok); // spaces aren't needed anymore since strings are already extracted. It's safe to remove them
      echo $l.": ".$tok."\n";
      $res=parse_one_func($filename,$l,$tok,$stringtable); // now, parse this instruction
      $instruct=array("line"=>$l,"str"=>$tok);
    }
    $tok=strtok(";");
  }
  return $lines;
}

function parse_one_func($filename,$l,$tok,&$stringtable) {
  // possibilities :
  // - $var=EXPR
  // - EXPR
  // Note that calls to functions ARE expressions
  // Also, note that it's not possible to assign a value to a var within function parameters
  //
  // Expressions are evaluated two times :
  //  - Parsetime : const values are directly evaluated (eg. : 4*8)
  //  - Runtime : dynamical or function result (eg : toto() or $var+7
  //
  // SPECIAL CASES :
  // For the moment, the code don't accept blocks
  // some functions are executed on runtime :
  //  CreateNPC(id,map,x,y,dir,name) : create a NPC somewhere XD
  //           if this NPC is clicked, the code following this function is runned
  //  Label(name) : defines a jump point in the code (use GoTo to go to a label)

function parse_remove_char($arr,$start,$len) {
  reset($arr);
  $arr2=array();
  while(list($p,$l)=each($arr)) {
    if ($p>=$start) {
      $p=$p-$len;
      if ($p>$start) $arr2[$p]=$l;
    } else {
      $arr2[$p]=$l;
    }
  }
  return $arr2;
}

function parse_find_line($arr,$pos) {
  reset($arr);
  $ll=0;
  while(list($p,$l)=each($arr)) {
    if ($p>$pos) return $ll;
    $ll=$l;
  }
  return $ll;
}

function parse_error($filename,$line,$errorcode) {
  $errstr="Unknown error";
  switch($errorcode) {
    case 0:$errstr="Success"; break;
    case 54:$errstr="Multi-lines comment not closed before EOF"; break;
    case 101:$errstr="File not found or not readable"; break;
    case 102:$errstr="String not terminated at end of line"; break;
    case 116:$errstr="Escaped string marker outside of a string"; break;
    case 117:$errstr="String escaped before its start [impossible error]"; break;
    case 121:$errstr="Forbidden keychar 'ÿ' outside a string"; break;
  }
  $type="Error";
  if ($errorcode<100) $type="Warning";
  echo $type." on $filename:".$line." : 0x".dechex($errorcode)." - ".$errstr."\n";
  
  return $errorcode;
}

function parse_line($filename,$lin,$num,&$stringtable) {
  // this function will parse each lines
  // this consist of wiping out comments
  $i=0;
  while($i<32) {
    $lin=str_replace(chr($i),"",$lin);
    $i++;
  }
  if (trim($lin)=="") return null;
  $p=0; $sidn=0;
  $cc=""; $pj=false; $check=false;
  while($p<strlen($lin)) {
//    if (DEBUG) echo "\$p = $p - cc = $cc line = $num \n";
    if ($cc=="") {
      $p1=strpos($lin,"\"",$p);
      $p2=strpos($lin,"'",$p);
      $p3=strpos($lin,"#",$p);
      $p4=strpos($lin,"//",$p);
      $pf=strpos($lin,"ÿ",$p); // ÿ is a forbidden char, used to make STRINGTABLE
      $pj=$p1;
      if ((($p2<$pj) or ($pj===false)) and ($p2!==false)) $pj=$p2;
      if ((($p3<$pj) or ($pj===false)) and ($p3!==false)) $pj=$p3;
      if ((($p4<$pj) or ($pj===false)) and ($p4!==false)) $pj=$p4;
      if ((($pf<$pj) or ($pj===false)) and ($pf!==false)) {
        // FATAL ERROR - reserved char
        return parse_error($filename,$num,121);
      }
//      if (DEBUG) echo "p1 = $p1 - p2 = $p2 - p3 = $p3 - p4 = $p4 - pj = $pj \n";
      $cc="\"";
      if ($pj===$p2) $cc="'";
      if (($pj!==false) and (($pj===$p3) or ($pj===$p4))) {
        // found comment, stripping
        $cc="comment";
        $lin=substr($lin,0,$pj);
      } elseif ($pj!==false) {
        // " or ' - check if it is escaped (shouldn't)
        $pm=$pj-1;
        // if a \ is present outside of a string, even if the \ is also escaped, it's a fatal syntax error
        if($lin{$pm}=="\\") return parse_error($filename,$num,116);
        $ps=$pj;
      }
//      if (DEBUG) echo "Found $cc \n";
      if ($pj===false) {
        $cc="";
        $p=strlen($lin);
      } else {
        $p=$pj+1;
      }
    } else {
      $p1=strpos($lin,$cc,$p);
      if (!$p1) return parse_error($filename,$num,102); // we aren't allowing multi-line string parameters
      $concider=1; $pm=$p1-1;
      while($lin{$pm}=="\\") { // is $cc escaped ? is the escape of $cc escaped ? is the ...... XD
        $concider=1-$concider; // toggle it
        $pm-=1; // remove 1 in PM
        if ($pm<1) return parse_error($filename,$num,117); // weird... shouldn't happen
      }
      $p=$p1+1;
      if ($concider==1) {
        $cc="";
        // extract the parsed string and replace it 
        $str=substr($lin,$ps,$p-$ps);
        $sid=$num."-".$sidn; // string identifier
        $sidn++;
        $stringtable[$sid]=$str;
        // strip string
        $lin=substr($lin,0,$ps)."ÿ".$sid."ÿ".substr($lin,$p);
        $p=($p-strlen($str))+strlen($sid)+2; // change value of $p according to new position
        $str=stripslashes(substr($str,1,strlen($str)-2));
      }
    }
  }
  if (trim($lin)=="") return null;
  return array("line"=>$num,"cont"=>$lin);
}