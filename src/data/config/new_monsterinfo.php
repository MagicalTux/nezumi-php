<?
// new monsterinfo will have all monsters data in only one file
// The file will be compiled to gzipped serialized array

// now convert yare files to our file ^^


// part one : load monster2.txt

$fil=fopen("monster2.txt","r");
if (!$fil) die("Couldn't open monster2.txt \n");
$m2=array();
while($lin=fread($fil,2040)) {
  $res=sscanf($lin,"%d,%[^,],%[^,],%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%[^\t]",
    $class,$name,$JName,$lv,$max_hp,$base_exp,$job_exp,
    $range,$atk1,$atk2,$def1,$def2,$mdef1,$mdef2,$hit,$flee,
    $scale,$race,$ele,$mode,$speed,$adelay,$amotion,$dmotion,
    $dropitem);
  if ($res==25) {
    echo $lin;
    $m=array();
    $m["class"]=$class;
    $m["name"]=$name;
    $m["JName"]=$JName;
    $m["lv"]=$lv;
    $m["max_hp"]=$max_hp;
    $m["base_exp"]=$base_exp;
    $m["job_exp"]=$job_exp;
    $m["range"]=$range;
    $m["atk1"]=$atk1;
    $m["atk2"]=$atk2;
    $m["def1"]=$del1;
    $m["def2"]=$def2;
    $m["mdef1"]=$mdef1;
    $m["mdef2"]=$mdef2;
    $m["hit"]=$hit;
    $m["flee"]=$flee;
    $m["scale"]=$scale;
    $m["race"]=$race;
    $m["ele"]=$ele;
    $m["mode"]=$mode;
    $m["speed"]=$speed;
    $m["adelay"]=$adelay;
    $m["amotion"]=$amotion;
    $m["dmotion"]=$dmotion;
    $m2[$class]=$m;
  }
}
fclose($fil);
$fil=fopen("output.log","w");
fputs($fil,print_r($m2,true));
fclose($fil);
exit;