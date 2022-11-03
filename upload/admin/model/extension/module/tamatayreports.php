<?php
class ModelExtensionModuleTamatayReports extends Model {
	public function getTotalDlr($data = array()) {
		$sql = "SELECT count(id) AS total FROM `" . DB_PREFIX . "sms_dlr`  ";

		if (!empty($data['filter_date_start'])) {
			$sql .= " WHERE submitted_date >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND submitted_date <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}


	public function getDlrs($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "sms_dlr` ";

		if (!empty($data['filter_date_start'])) {
			$sql .= " WHERE DATE(submitted_date) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(submitted_date) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getDlr($id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "sms_dlr` WHERE id = '".(int)$id."'";		

		$query = $this->db->query($sql);

		return $query->row;
	}


	public function log($data, $class_step = 6) {		
			$backtrace = debug_backtrace();
			$this->log->write('SMS API (' . $backtrace[$class_step]['class'] . '::' . $backtrace[6]['function'] . ') - ' . $data);
		
	}

}
