<?php
class ControllerExtensionModuleTamatay extends Controller {
	
	private $data = array();
	private $error = array();

  public function install() {
		$this->load->model('extension/module/tamatay');
		$this->model_extension_module_tamatay->createTable();
	}

	public function uninstall() {
		$this->load->model('extension/module/tamatay');
		$this->model_extension_module_tamatay->deleteTables();
	}

    public function index() { 

        $this->load->language('extension/module/tamatay');
		
        $this->load->model('extension/module/tamatay');
        $this->load->model('setting/store');
        $this->load->model('localisation/language');
		$this->load->model('localisation/currency');
        $this->load->model('design/layout');		
		$this->load->model('setting/setting');
		$this->load->model('setting/module');

	$this->document->setTitle($this->language->get('heading_title'));

        if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0; 
        }
	
        $store = $this->get_current_store($this->request->get['store_id']);
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST')  && $this->validate() ) { 	          
		
        	if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('tamatay', $this->request->post);
			} else {				
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));			

        }		

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['save_changes'] = $this->language->get('save_changes');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_admin_order_msg'] = $this->language->get('text_admin_order_msg');
		$data['text_admin_cust_reg'] = $this->language->get('text_admin_cust_reg');
		$data['text_cust_reg_msg'] = $this->language->get('text_cust_reg_msg');
		$data['text_cust_order_msg'] = $this->language->get('text_cust_order_msg');
		$data['text_cust_order_status'] = $this->language->get('text_cust_order_status');
		$data['text_admin_return_product_msg'] = $this->language->get('text_admin_return_product_msg');
		$data['text_cust_return_product_msg'] = $this->language->get('text_cust_return_product_msg');
		$data['text_cust_return_product_status'] = $this->language->get('text_cust_return_product_status');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_api_username'] = $this->language->get('entry_api_username');
		$data['entry_api_password'] = $this->language->get('entry_api_password');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_admin_order_msg'] = $this->language->get('entry_admin_order_msg');
		$data['entry_admin_cust_reg'] = $this->language->get('entry_admin_cust_reg');
		$data['entry_cust_order_msg'] = $this->language->get('entry_cust_order_msg');
		$data['entry_cust_reg_msg'] = $this->language->get('entry_cust_reg_msg');
		$data['entry_cust_order_status'] = $this->language->get('entry_cust_order_status');
		$data['entry_admin_return_product_msg'] = $this->language->get('entry_admin_return_product_msg');
		$data['entry_cust_return_product_msg'] = $this->language->get('entry_cust_return_product_msg');
		$data['entry_cust_return_product_status'] = $this->language->get('entry_cust_return_product_status');
  

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['api_username'])) {
			$data['error_api_username'] = $this->error['api_username'];
		} else {
			$data['error_api_username'] = '';
		}

		if (isset($this->error['api_password'])) {
			$data['error_api_password'] = $this->error['api_password'];
		} else {
			$data['error_api_password'] = '';
		}

		if (isset($this->error['owner_telephone'])) {
			$data['error_owner_telephone'] = $this->error['owner_telephone'];
		} else {
			$data['error_owner_telephone'] = '';
		}

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

       $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/tamatay', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/tamatay', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/tamatay', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/tamatay', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}
		
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['api_username'])) {
			$data['api_username'] = $this->request->post['api_username'];
		} elseif (!empty($module_info)) {
			$data['api_username'] = $module_info['api_username'];
		} else {
			$data['api_username'] = '';
		}

		if (isset($this->request->post['api_password'])) {
			$data['api_password'] = $this->request->post['api_password'];
		} elseif (!empty($module_info)) {
			$data['api_password'] = $module_info['api_password'];
		} else {
			$data['api_password'] = '';
		}

		if (isset($this->request->post['owner_telephone'])) {
			$data['owner_telephone'] = $this->request->post['owner_telephone'];
		} elseif (!empty($module_info)) {
			$data['owner_telephone'] = $module_info['owner_telephone'];
		} else {
			$data['owner_telephone'] = '';
		}

		if (isset($this->request->post['admin_place_order'])) {
			$data['admin_place_order'] = $this->request->post['admin_place_order'];
		} elseif (isset($module_info['admin_place_order']) && !empty($module_info['admin_place_order'])) {
			$data['admin_place_order'] = $module_info['admin_place_order'];
		} else {
			$data['admin_place_order'] = '';
		}

		if (isset($this->request->post['admin_place_order_text'])) {
			$data['admin_place_order_text'] = $this->request->post['admin_place_order_text'];
		} elseif (!empty($module_info)) {
			$data['admin_place_order_text'] = $module_info['admin_place_order_text'];
		} else {
			$data['admin_place_order_text'] = '';
		}

		if (isset($this->request->post['admin_register'])) {
			$data['admin_register'] = $this->request->post['admin_register'];
		} elseif (isset($module_info['admin_register']) && !empty($module_info['admin_register'])) {
			$data['admin_register'] = $module_info['admin_register'];
		} else {
			$data['admin_register'] = '';
		}

		if (isset($this->request->post['admin_register_text'])) {
			$data['admin_register_text'] = $this->request->post['admin_register_text'];
		} elseif (!empty($module_info)) {
			$data['admin_register_text'] = $module_info['admin_register_text'];
		} else {
			$data['admin_register_text'] = '';
		}

		if (isset($this->request->post['customer_place_order'])) {
			$data['customer_place_order'] = $this->request->post['customer_place_order'];
		} elseif (isset($module_info['customer_place_order']) && !empty($module_info['customer_place_order'])) {
			$data['customer_place_order'] = $module_info['customer_place_order'];
		} else {
			$data['customer_place_order'] = '';
		}

		if (isset($this->request->post['customer_place_order_text'])) {
			$data['customer_place_order_text'] = $this->request->post['customer_place_order_text'];
		} elseif (!empty($module_info)) {
			$data['customer_place_order_text'] = $module_info['customer_place_order_text'];
		} else {
			$data['customer_place_order_text'] = '';
		}

		if (isset($this->request->post['customer_register'])) {
			$data['customer_register'] = $this->request->post['customer_register'];
		} elseif (isset($module_info['customer_register']) && !empty($module_info['customer_register'])) {
			$data['customer_register'] = $module_info['customer_register'];
		} else {
			$data['customer_register'] = '';
		}

		if (isset($this->request->post['customer_register_text'])) {
			$data['customer_register_text'] = $this->request->post['customer_register_text'];
		} elseif (!empty($module_info)) {
			$data['customer_register_text'] = $module_info['customer_register_text'];
		} else {
			$data['customer_register_text'] = '';
		}


		if (isset($this->request->post['order_status'])) {
			$data['order_status'] = $this->request->post['order_status'];
		} elseif (isset($module_info['order_status']) && !empty($module_info['order_status'])) {
			$data['order_status'] = $module_info['order_status'];
		} else {
			$data['order_status'] = '';
		}

		if (isset($this->request->post['order_status_change'])) {
			$data['order_status_change'] = $this->request->post['order_status_change'];
		} elseif (isset($module_info['order_status_change']) && !empty($module_info['order_status_change'])) {
			$data['order_status_change'] = $module_info['order_status_change'];
		} else {
			$data['order_status_change'] = '';
		}

		if (isset($this->request->post['order_status_change_text'])) {
			$data['order_status_change_text'] = $this->request->post['order_status_change_text'];
		} elseif (!empty($module_info)) {
			$data['order_status_change_text'] = $module_info['order_status_change_text'];
		} else {
			$data['order_status_change_text'] = '';
		}   

		if (isset($this->request->post['admin_return_product'])) {
			$data['admin_return_product'] = $this->request->post['admin_return_product'];
		} elseif (isset($module_info['admin_return_product']) && !empty($module_info['admin_return_product'])) {
			$data['admin_return_product'] = $module_info['admin_return_product'];
		} else {
			$data['admin_return_product'] = '';
		}

		if (isset($this->request->post['admin_return_product_text'])) {
			$data['admin_return_product_text'] = $this->request->post['admin_return_product_text'];
		} elseif (isset($module_info['admin_return_product_text']) && !empty($module_info)) {
			$data['admin_return_product_text'] = $module_info['admin_return_product_text'];
		} else {
			$data['admin_return_product_text'] = '';
		}		
		
		if (isset($this->request->post['cust_return_product'])) {
			$data['cust_return_product'] = $this->request->post['cust_return_product'];
		} elseif (isset($module_info['cust_return_product']) && !empty($module_info['cust_return_product'])) {
			$data['cust_return_product'] = $module_info['cust_return_product'];
		} else {
			$data['cust_return_product'] = '';
		}

		if (isset($this->request->post['cust_return_product_text'])) {
			$data['cust_return_product_text'] = $this->request->post['cust_return_product_text'];
		} elseif (isset($module_info['cust_return_product_text']) && !empty($module_info)) {
			$data['cust_return_product_text'] = $module_info['cust_return_product_text'];
		} else {
			$data['cust_return_product_text'] = '';
		}
		
		if (isset($this->request->post['cust_return_product_status'])) {
			$data['cust_return_product_status'] = $this->request->post['cust_return_product_status'];
		} elseif (isset($module_info['cust_return_product_status']) && !empty($module_info['cust_return_product_status'])) {
			$data['cust_return_product_status'] = $module_info['cust_return_product_status'];
		} else {
			$data['cust_return_product_status'] = '';
		}

		if (isset($this->request->post['cust_return_product_status_text'])) {
			$data['cust_return_product_status_text'] = $this->request->post['cust_return_product_status_text'];
		} elseif (isset($module_info['cust_return_product_status_text']) && !empty($module_info)) {
			$data['cust_return_product_status_text'] = $module_info['cust_return_product_status_text'];
		} else {
			$data['cust_return_product_status_text'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		
		$data['token'] = $this->session->data['user_token'];

		
		$data['stores'] = array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' ' . $data['text_default'], 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
		$data['error_warning']          = '';  
		$data['languages']              = $this->model_localisation_language->getLanguages();
		$data['store']                  = $store;

		$data['header']  					 = $this->load->controller('common/header');
		$data['column_left']				= $this->load->controller('common/column_left');
		$data['footer']					 = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/tamatay', $data));
    }

    protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/tamatay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen(trim($this->request->post['owner_telephone'])) < 12) || (utf8_strlen(trim($this->request->post['owner_telephone'])) > 12)) {
			$this->error['owner_telephone'] = $this->language->get('error_owner_telephone');
		}

		

		return !$this->error;
	}

    private function get_current_store($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->get_store_url(); 
        }
        return $store;
    }

   private function get_store_url() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $store_url = HTTPS_CATALOG;
        } else {
            $store_url = HTTP_CATALOG;
        } 
        return $store_url;
    }

}
?>
