<?php 

class Socket {

	private $link=false;
	private $iv;
	private $key;

	function __construct($address, $port, $key) {
		
		if(!filter_var($address, FILTER_VALIDATE_IP)) {
			$address = gethostbyname($address);
		}
		
		if(filter_var($address, FILTER_VALIDATE_IP)) {
			$this->link = fsockopen($address, $port, $errno, $errstr);
			stream_set_timeout($this->link, 1);
		}
		
		$iv_size  = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		$this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$this->key = md5($key);
	}
	
	function write($action, $id=0, $para='') {
		$data = array(
			'action' => $action,
			'server' => $id,
			'para'   => $para
		);
		
		$in = json_encode($data);
		$in = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $in, MCRYPT_MODE_CBC, $this->iv));
		
		$trame = array(
			'data' => $in,
			'iv'   => base64_encode($this->iv)
		);
		
		fwrite($this->link, json_encode($trame));
	}
	
	function read() {
		$fullResult = '';
		
		if(!empty($this->link)) {
			while(true) {
				$message = fgets($this->link, 1024);
			
				if($message != null) {
					$fullResult .= $message;
					if(substr($fullResult, -5, 5) == 'close') {
						break;
					}
				}
			}
			
			$fullResult = substr(trim($fullResult), 0, -5);
			$fullResult = base64_decode($fullResult);
			$fullResult = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $fullResult, MCRYPT_MODE_CBC, $this->iv);
			
			$pos = strpos($fullResult, '[');
			if($pos != false) {
				$fullResult = substr($fullResult, $pos);
			}
		}
		return trim($fullResult);
	}
	
	function __destruct() {
		fclose($this->link);
	}	
}

?>
