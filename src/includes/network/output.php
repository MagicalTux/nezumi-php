<?
/* output functions
 * this will enable some extra freatures, such as compression
 */

function net_write(&$nfo,$data) {
  $nfo["buf_out"].=$data;
  return true;
}

function net_send(&$nfo) {
  // process output buffer
  $sock=$nfo["socket"];
  if (!$sock) return;
  if (strlen($nfo["buf_out"])>0) {
    // new data
    $dat=$nfo["buf_out"];
    $nfo["buf_out"]="";
    if (($nfo["compress"]) and (strlen($dat)>64)) { // do not compress if buffer < 64 bytes
      $datb=gzcompress($dat,4);
      if ($datb) $dat=writeW(0x1010).writeW(strlen($datb)+4).$datb; // check if compression was successful
      unset($datb);
    }
    $nfo["buf_out_"].=$dat;
  }
  if (strlen($nfo["buf_out_"])<1) return; // no data to send
  socket_set_nonblock($sock);
  while (socket_select($r=null,$w=array($sock),$e,0)>0) {
    $dat=@socket_write($sock,$nfo["buf_out_"],4096);
    if ($dat == 0) return; // no data written... seems that this socket is dead :p
    $nfo["buf_out_"]=substr($nfo["buf_out_"],$dat); // remove written data
  }
}