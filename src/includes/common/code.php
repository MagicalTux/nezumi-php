<?
  srand((double)microtime()*1000000);
  function code($nc) {
    // on génère un code aléatoire à nc caractères ...
    $code="";
    do {
      $nc=$nc-1;
      $al = rand(1, 2);
      $al = floor ($al); 
      if ($al==2) {
        $num = rand(0, 9);
        $num = floor ($num); 
        $code.=$num;
      } else {
        $num = rand(1, 26);
        $maj = rand(0, 1);
        $num = floor ($num); 
        $maj = floor ($maj);
        $num=$num + 64 + (32 * $maj);
        $code.=Chr($num);
      }
    } while ($nc>0);
    return $code;
  }
?>