<?
/* a little extrn funciton
 */

function net_client_struct() {
  $client=array();
  $client["buf_in"]="";
  $client["buf_out"]="";
  $client["buf_out_"]="";
  $client["compress"]=false;
  $client["version"]="none";
  $client["SendQ_size"]=8192; // default max sendQ
  return $client;
}

?>