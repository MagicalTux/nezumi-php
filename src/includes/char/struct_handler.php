<?
/* Struct handler for >_< C structs
 * 
 */

$GLOBALS["char_struct_handler"]=new structhandler("includes/mmo/".$GLOBALS["config"]["char"]["map_server_prot"]);
if (!$GLOBALS["char_struct_handler"]) die("Couldn't initialize struct_handler !\n");

class structhandler {
  var $struct;
  var $size;
  var $buf;
  
  function structhandler($file) {
    $fil=@fopen($file,"rb");
    if (!$fil) return false;
    $data=fread($fil,filesize($file));
    fclose($fil);
    // decompress data
    $data=@gzuncompress($data);
    if (!$data) return false;
    // decode data
    $data=@unserialize($data);
    if (!$data) return false;
    if (!is_array($data)) return false;
    // store data
    $this->struct=$data["data"];
    $this->size=$data["size"];
    $this->buf=str_repeat("\x00",$data["size"]);
  }
  
  function set_value($var,$val) {
    $var=strtolower($var);
    if (!isset($this->struct[$var])) return false; // doesn't exists
    $data=$this->struct[$var];
    $d_s=$data["size"];
    $d_o=$data["offset"];
    if (($d_s+$d_o)>$this->size) return false; // corrupted
    if ($d_s==1) {
      // byte value
      $this->buf{$d_o}=writeB($val);
    } elseif ($d_s==2) {
      $tmp=writeW($val);
      $this->buf{$d_o}=$tmp{0};
      $this->buf{$d_o+1}=$tmp{1};
    } elseif ($d_s==4) {
      $tmp=writeL($val);
      $this->buf{$d_o}=$tmp{0};
      $this->buf{$d_o+1}=$tmp{1};
      $this->buf{$d_o+2}=$tmp{2};
      $this->buf{$d_o+3}=$tmp{3};
    } else {
      $tmp=ToLen($val,$d_s);
      $i=0;
      while($i<$d_s) {
        $this->buf{$d_o+$i}=$tmp{$i};
        $i++;
      }
    }
    return true;
  }
  
  function get_value($var,&$val) {
    $var=strtolower($var);
    if (!isset($this->struct[$var])) return false; // doesn't exists
    $data=$this->struct[$var];
    $d_s=$data["size"];
    $d_o=$data["offset"];
    if (($d_s+$d_o)>$this->size) return false; // corrupted
    if ($d_s==1) {
      // byte value
      $val=readB($this->buf{$d_o},0);
    } elseif ($d_s==2) {
      $tmp="";
      $tmp.=$this->buf{$d_o};
      $tmp.=$this->buf{$d_o+1};
      $val=readW($tmp,0);
    } elseif ($d_s==4) {
      $tmp="";
      $tmp.=$this->buf{$d_o};
      $tmp.=$this->buf{$d_o+1};
      $tmp.=$this->buf{$d_o+2};
      $tmp.=$this->buf{$d_o+3};
      $val=readL($tmp,0);
    } else {
      $tmp="";
      $i=0;
      while($i<$d_s) {
        $tmp.=$this->buf{$d_o+$i};
        $i++;
      }
      $val=readP($tmp,0,$d_s);
    }
    return true;
  }
  
  function set_buffer(&$buf) {
    $this->buf=ToLen($buf,$this->size);
  }
  
  function get_buffer() {
    return $this->buf;
  }
  
  function buf_reset() {
    $this->buf=str_repeat("\x00",$this->size);
  }
}