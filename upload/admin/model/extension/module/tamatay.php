<?php 
class ModelExtensionModuleTamatay extends Model {


public function createTable() {
$this->db->query("
CREATE TABLE IF NOT EXISTS ".DB_PREFIX ."sms_dlr (
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
				) ENGINE=InnoDB DEFAULT CHARSET=latin1");

$this->db->query("ALTER TABLE " . DB_PREFIX ."sms_dlr ADD PRIMARY KEY (`id`)");

$this->db->query("ALTER TABLE ".DB_PREFIX ."sms_dlr MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");	

 }

 public function deleteTables() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sms_dlr`");

	}

  	public function getSetting($group, $store_id = 0) {
	    $data = array(); 
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
	    foreach ($query->rows as $result) {
	      if (!$result['serialized']) {
	        $data[$result['key']] = $result['value'];
	      } else {
	        $data[$result['key']] = unserialize($result['value']);
	      }
	    } 
	    return $data;
	}
  
  	public function editSetting($group, $data, $store_id = 0) {
	    $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
	    foreach ($data as $key => $value) {
	      if (!is_array($value)) {
	        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
	      } else {
	        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
	      }
	    }
	}

	public function send_sms($data) {
		$username = $data['username'];
		$password = $data['password'];
		$to = $data['to'];
		$message = urlencode($data['message']);

		$api = "https://app.tamatay.com/api/send.php?number=$to&type=text&message=$message&instance_id=$username&access_token=$password";

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
	
	// sms Bulk Messages
	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}
		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}	
		if (!empty($data['filter_ip'])) {
			$implode[] = "customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}	
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}			
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
		}		
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}	
		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}	
		if (!empty($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}	
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}	
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}	
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.approved',
			'c.ip',
			'c.date_added'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY name";	
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		$query = $this->db->query($sql);
		return $query->rows;	
	}
	
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		return $query->row;
	}
	
	public function getTotalAffiliates($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "LCASE(email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
		}	
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}			
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
		}		
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getAffiliates($data = array()) {
		$sql = "SELECT *, CONCAT(a.firstname, ' ', a.lastname) AS name, (SELECT SUM(at.amount) FROM " . DB_PREFIX . "affiliate_transaction at WHERE at.affiliate_id = a.affiliate_id GROUP BY at.affiliate_id) AS balance FROM " . DB_PREFIX . "affiliate a";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "LCASE(a.email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
		}
		if (!empty($data['filter_code'])) {
			$implode[] = "a.code = '" . $this->db->escape($data['filter_code']) . "'";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "a.status = '" . (int)$data['filter_status'] . "'";
		}	
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "a.approved = '" . (int)$data['filter_approved'] . "'";
		}		
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(a.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$sort_data = array(
			'name',
			'a.email',
			'a.code',
			'a.status',
			'a.approved',
			'a.date_added'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY name";	
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		$query = $this->db->query($sql);
		return $query->rows;	
	}
	
	public function getAffiliate($affiliate_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		return $query->row;
	}
	
	public function getTotalTelephonesByProductsOrdered($products) {
		$implode = array();
		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}
		$query = $this->db->query("SELECT DISTINCT telephone FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");
		return $query->row['total'];
	}
	
	public function getTelephonesByProductsOrdered($products, $start, $end) {
		$implode = array();
		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}
		$query = $this->db->query("SELECT DISTINCT telephone FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0' LIMIT " . (int)$start . "," . (int)$end);
		return $query->rows;
	}
	// sms Bulk Messages

  }
?>
