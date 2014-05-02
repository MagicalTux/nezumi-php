<?
  // UDP class
  class udpsocket {
    var $socket;
    var $last_packet;
    
    function udpsocket() {
      $this->socket=false;
      $this->last_packet=array();
    }
    
    function open_socket($ip="0.0.0.0",$port=0) {
      if ($this->socket) {
        socket_close($this->socket);
        $this->socket=false;
      }
      $this->socket=socket_create(AF_INET,SOCK_DGRAM,SOL_UDP);
      if (!$this->socket) {
        return false;
      }
      if ($port != 0) {
        $res=socket_bind($this->socket,$ip,$port);
      } else {
        $res=socket_bind($this->socket,$ip);
      }
      if (!$res) {
        socket_close($this->socket);
        $this->socket=false;
        return false;
      }
      socket_set_nonblock($this->socket);
      return true;
    }
    
    function wait_socket($timeout=null) {
      if (!$this->socket) {
        return false;
      }
      $res=@socket_select($r=array($this->socket),$w=null,$e=null,$timeout);
      if ($res === false) {
        // error
        return false;
      } elseif ($res==0) {
        // nothing happened
        return false;
      } else {
        // data arrived
        return true;
      }
    }
    
    function read_packet($len=4096) {
      // lecture d'un packet depuis le socket
      if (!$this->wait_socket(0)) {
        // no data
        return false;
      }
      socket_clear_error();
      @socket_recvfrom($this->socket,$buf="",$len,$flags=0,$addr=null,$port=null);
      $res=socket_last_error($this->socket);
      if ($res == 11) {
        // no packet available
        return false;
      } elseif ($res) {
        // une erreur est survenue
        return false;
      }
      $packet=array();
      $packet["buffer"]=$buf;
      $packet["src_ip"]=$addr;
      $packet["src_port"]=$port;
      $this->last_packet=$packet;
      return true;
    }
    
    function self_test() {
      if (!$this->socket) return false;
      // drops all unknown packets
      $res=@socket_getsockname($this->socket,$addr,$port); // get informations
      if (!$res) return false;
      if ($addr == "0.0.0.0") $addr="127.0.0.1"; // loopback on open-minded sockets
      $test=code(512); // 512bytes packet. Should not altered or dropped since this is a local test
      @socket_sendto($this->socket,$test,strlen($test),0,$addr,$port);
      $start=time();
      while(1) {
        $this->wait_socket(1);
        if ($this->read_packet()) {
          $nfo=$this->last_packet;
          if ( ($nfo["src_ip"]==$addr) and ($nfo["src_port"]==$port) and ($nfo["buffer"]==$test) ) {
            // socket ok
            return true;
          }
        }
        if ((time()-$start)>2) {
          return false; // test timeout
        }
      }
    }
    
    function packet_buffer() {
      if (isset($this->last_packet["buffer"])) {
        return $this->last_packet["buffer"];
      } else {
        return false;
      }
    }
    
    function packet_addr() {
      if (isset($this->last_packet["src_ip"])) {
        return $this->last_packet["src_ip"];
      } else {
        return false;
      }
    }
    
    function packet_port() {
      if (isset($this->last_packet["src_port"])) {
        return $this->last_packet["src_port"];
      } else {
        return false;
      }
    }
  }
?>