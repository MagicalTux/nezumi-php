<?
/* Config option
 * function : load_cfg()
 */

function load_cfg() {
  if (isset($status)) unset($status);
  $status=array();
  @include("config.php");
  if (count($status)<1) return false;
  $GLOBALS["config"]=$status; // save config to mem
  return true;
}