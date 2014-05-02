<?
// transform seconds to easy to read uptime
function do_uptime($time) {
  $res="";
  if ($time>86400) { if ($res!="") $res.=", "; $n=floor($time/86400); $time=$time-($n*86400); $t=($n!=1)?"s":""; $res.=$n." day".$t; }
  if ($time>3600) { if ($res!="") $res.=", "; $n=floor($time/3600); $time=$time-($n*3600); $res.=$n." hour".(($n!=1)?"s":""); }
  if ($time>60) { if ($res!="") $res.=", "; $n=floor($time/60); $time=$time-($n*60); $res.=$n." minute".(($n!=1)?"s":""); }
  if ($res!="") $res.=" and "; 
  $res.=$time." second".(($time!=1)?"s":"");
  return $res;
}

?>
