<?php
class ControllerExtensionModuleTamatayReports extends Controller {
	public function index() {
		$this->load->language('extension/module/tamatayreports');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = date('Y-m-d 00:00:00', strtotime($this->request->get['filter_date_start']));
		} else {
			$filter_date_start = date('Y-m-d 00:00:00');
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = date('Y-m-d 23:59:59', strtotime($this->request->get['filter_date_end']));
		} else {
			$filter_date_end = date('Y-m-d 23:59:59');
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}


		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/tamatayreports', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$this->load->model('extension/module/tamatayreports');

		$data['dlrs'] = array();

		$filter_data = array(
			'filter_date_start'	     => $filter_date_start,
			'filter_date_end'	     => $filter_date_end,			
			'start'                  => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                  => $this->config->get('config_limit_admin')
		);

		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_all_status'] = $this->language->get('text_all_status');

		$data['column_customerid'] = $this->language->get('column_customerid');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_mobile'] = $this->language->get('column_mobile');
		$data['column_msgid'] = $this->language->get('column_msgid');
		$data['column_message'] = $this->language->get('column_message');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date'] = $this->language->get('column_date');

		

		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		
		$data['button_filter'] = $this->language->get('button_filter');

		$data['user_token'] = $this->session->data['user_token'];

		


		$url = '';

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		$pagination = new Pagination();
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/tamatayreports', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		$dir_total = 0;

		$data['results'] = sprintf($this->language->get('text_pagination'),  (($page - 1) * $this->config->get('config_limit_admin')) + 1, ((($page - 1) * $this->config->get('config_limit_admin')) > ($dlr_total - $this->config->get('config_limit_admin'))) ? $dlr_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $dlr_total, ceil($dlr_total / $this->config->get('config_limit_admin')));

		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/tamatayreports', $data));
	}

}
