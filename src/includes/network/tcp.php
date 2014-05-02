<?
  // TCP socket
  class tcpsocket {
    var $socket;
    
    function tcpsocket() {
      $this->socket=false;
    }
    
    function open_socket($ip="0.0.0.0",$port=0) {
      if ($this->socket) {
        socket_close($this->socket);
        $this->socket=false;
      }
      $this->socket=@socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
      if (!$this->socket) {
        return false;
      }
      if ($port != 0) {
        $res=@socket_bind($this->socket,$ip,$port);
      } else {
        $res=@socket_bind($this->socket,$ip);
      }
      if (!$res) {
        @socket_close($this->socket);
        $this->socket=false;
        return false;
      }
      $res=@socket_listen($this->socket);
      if (!$res) {
        @socket_close($this->socket);
        $this->socket=false;
        return false;
      }
      @socket_set_nonblock($this->socket);
      return true;
    }
    
    function get_port() {
      if (!$this->socket) {
        return false;
      }
      $addr="";
      $port=0;
      $res=socket_getsockname($this->socket,$addr,$port);
      if (!$res) return false;
      return $port;
    }
    
    function accept($timeout=null,$simulat=false) {
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
      }
      if ($simulat) return true; // simulate accept for internal use
      // maybe a new connexion
      $res=socket_accept($this->socket);
      if (!$res) return false;
      return $res; // socket .... or false in case of failure
    }
    
    function close() {
      if (!$this->socket) {
        return false;
      }
      socket_close($this->socket);
      $this->socket=false;
    }
  }
  
  class tcpdatasocket {
    var $socket;
    var $buffer;
    var $sendb;
    
    function tcpdatasocket() {
      $this->socket=false;
    }
    
    function init($sock) {
      $this->socket=$sock;
    }
    
    function wait_socket($timeout=null) {
      if (!$this->socket) {
        return false;
      }
      $res=@socket_select($r=array($this->socket),$w=null,$e=null,$timeout);
      if ($res === false) {
        // error
        socket_close($this->socket);
        $this->socket=false;
        return null;
      } elseif ($res==0) {
        // nothing happened
        return false;
      } else {
        // data arrived
        return true;
      }
    }
    
    function read_packet($len=4096) {
      if (!$this->socket) return false;
      socket_set_nonblock($this->socket);
      // lecture d'un packet depuis le socket
      $change=null;
      while ( ($this->wait_socket(0)) and (strlen($this->buffer)<$len)) {
        $char=@socket_read($this->socket,1,PHP_BINARY_READ);
        if ($char===false) {
          return false;
        } elseif ($char=="") {
          return false;
        }
        $this->buffer.=$char;
        $change=true;
      }
      return $change;
    }
    
    function write_packet($data) {
      if (!$this->socket) return false;
      // envoi d'un block de données
      while (strlen($data)>0) {
        $res=socket_write($this->socket,$data);
        if ($res === false) {
          socket_close($this->socket);
          return false;
        }
        $data=substr($data,$res); // remove writen data
      }
      return true;
    }
    
    function close() {
      if (!$this->socket) {
        return false;
      }
      socket_close($this->socket);
      $this->socket=false;
    }
  }
  
function wait_socket($sock,$timeout=null) {
  if (!$sock) {
    return false;
  }
  $res=@socket_select($r=array($sock),$w=null,$e=null,$timeout);
  if ($res === false) {
    // error
    @socket_close($sock);
    return null;
  } elseif ($res==0) {
    // nothing happened
    return false;
  } else {
    // data arrived
    return true;
  }
}

function get_rip($sock) { // get Remote IP
  if (!$sock) {
    return false;
  }
  $addr="";
  $port=0;
  $res=@socket_getpeername($sock,$addr,$port);
  if (!$res) return false;
  return ip2long($addr);
}

function get_lip($sock) { // get local IP
  if (!$sock) {
    return false;
  }
  $addr="";
  $port=0;
  $res=@socket_getsockname($sock,$addr,$port);
  if (!$res) return false;
  return ip2long($addr);
}

function read_packet($sock,$len=4096) {
  if (!$sock) return false;
  @socket_set_nonblock($sock);
  // lecture d'un packet depuis le socket
  if (!wait_socket($sock,0)) return null;
  $change="";
  while ( (wait_socket($sock,0)) and (strlen($change)<$len)) {
    $char=@socket_read($sock,1,PHP_BINARY_READ);
    if ($char===false) {
      return false;
    } elseif ($char=="") {
      return false;
    }
    $change.=$char;
  }
  return $change;
}

function write_packet($sock,$data) {
  if (!$sock) return false;
  // envoi d'un block de données
  while (strlen($data)>0) {
    $res=socket_write($sock,$data);
    if ($res === false) {
      socket_close($sock);
      return false;
    }
    $data=substr($data,$res); // remove writen data
  }
  return true;
}


function net_tcp_connect($destip,$destport,$timeout=15,$ip="0.0.0.0",$port=0) {
  $sock=@socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
  if (!$sock) {
    return false;
  }
  if ($port != 0) {
    $res=@socket_bind($sock,$ip,$port);
  } else {
    if ($ip!="0.0.0.0") $res=@socket_bind($sock,$ip); else $res=true;
  }
  if (!$res) {
    @socket_close($sock);
    return false;
  }
  $non_error=115;
  if (OS_WINDOWS) $non_error=10035; // windows code
  @socket_set_nonblock($sock);
  $res=@socket_connect($sock,$destip,$destport);
  if (!$res) {
    if (socket_last_error($sock)!=$non_error) {
      $num=socket_last_error($sock);
      @socket_close($sock);
      return false;
    }
  }
  $t=0;
  while($t<$timeout) {
    $t+=.25;
    usleep(250000); // 25/100th wait 
    $res=socket_get_option($sock,SOL_SOCKET,SO_ERROR);
    if ($res==0) return $sock; // etablished !
    if ($res!=$non_error) { // couldn't connect
      @socket_close($sock);
      return false;
    }
  }
  // timeout
  @socket_close($sock);
  return false;
}
?>