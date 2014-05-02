<?
// this function was found as an example in the PHP documentation.
// See microtime() doc. page
function getmicrotime(){ 
  list($usec, $sec) = explode(" ",microtime()); 
  return ((float)$usec + (float)$sec); 
} 
?>