<?php
class ModelExtensionModuleTamatay extends Model {
  
  	public function send_sms($data) {
		$username = $data['username'];
		$password = $data['password'];
		$to = $data['to'];
		$message = urlencode($data['message']);

		$api = "https://app.tamatay.com/api/send?number=$to&type=text&message=$message&instance_id=$username&access_token=$password";


		$optional_opts = array(
			CURLOPT_URL            => $api,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_CONNECTTIMEOUT=> 300,
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
	
		);

		$response = $this->curl($api, $optional_opts);

		$this->log('cURL Response: ' . print_r($response, 1), 1);

		$values = explode('|',$response);
		
		return $values;
                /*if (trim($values[0]) == "SUBMIT_SUCCESS") {  			          
	                return 'true';
                }else{                    
                  return 'false';   
                }*/
	}
	

	public function format_number($number){
		$number_lenght = strlen($number);
        return $to = substr($number,($number_lenght-12),$number_lenght);    
	}

	private function curl($api, $optional_opts = array()) {
		$default_opts = array(
			CURLOPT_PORT           => 443,
			CURLOPT_HEADER         => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE   => 1,
			CURLOPT_FRESH_CONNECT  => 1,
			CURLOPT_URL            => $api,
			CURLOPT_HTTPHEADER, array('Content-Type: application/json')
		);

		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		$response = curl_exec($ch);
		curl_close($ch);*/

		$ch = curl_init($api);

		$opts = $default_opts + $optional_opts;

		curl_setopt_array($ch, $opts);
		$response = curl_exec($ch);
		curl_close($ch);		

		return $response;
	}

	public function log($data, $class_step = 6) {		
			$backtrace = debug_backtrace();
			$this->log->write('SMS API (' . $backtrace[$class_step]['class'] . '::' . $backtrace[6]['function'] . ') - ' . $data);
		
	}

	public function checkdb() {
            $create_sms_dlr = "
				CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sms_dlr` (
				  `id` int(11) NOT NULL,
				  `customer_id` int(11) NOT NULL,
				  `customer_name` varchar(255) NOT NULL,
				  `mobile` varchar(11) NOT NULL,
				  `msg_id` varchar(255) NOT NULL,
				  `status` varchar(255) NOT NULL,
				  `message` text NOT NULL,
				  `language_id` int(11) NOT NULL,
				  `ip_address` varchar(50) NOT NULL,
				  `submitted_date` datetime NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
            $this->db->query($create_sms_dlr);

		$create_sms_dlr = " ALTER TABLE `" . DB_PREFIX . "sms_dlr`
		  ADD PRIMARY KEY (`id`);";
		$this->db->query($create_sms_dlr);

		$create_sms_dlr = "
		ALTER TABLE `" . DB_PREFIX . "sms_dlr`
		  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";
		$this->db->query($create_sms_dlr);
        }

	public function insert_dlr($data){

	    	if(isset($data) && !empty($data)){
	    		$this->db->query("INSERT INTO " . DB_PREFIX . "sms_dlr 
					SET customer_id = '" . (int)$data['customer_id'] . "', 
					customer_name = '" . $this->db->escape($data['customer_name']) . "',									
					mobile = '" . (int)$data['mobile'] . "', 								
					msg_id = '" . $this->db->escape($data['msg_id']) . "',
					status = '',
					message = '" . $this->db->escape($data['message']) . "',
					ip_address = '" . $_SERVER['REMOTE_ADDR'] . "',
					submitted_date = NOW(),
					language_id = '" . (int)$this->config->get('config_language_id') . "'");
	    	}
    }
}
?>
