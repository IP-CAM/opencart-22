<?php
class ControllerPaymentIpayy extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/ipayy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('ipayy', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['ipayy_logo_url'] = $this->language->get('ipayy_logo_url');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_authorization'] = $this->language->get('text_authorization');
		$this->data['text_sale'] = $this->language->get('text_sale');

		$this->data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$this->data['entry_merchant_help'] = $this->language->get('entry_merchant_help');
		$this->data['entry_application_id'] = $this->language->get('entry_application_id');
		$this->data['entry_application_help'] = $this->language->get('entry_application_help');
		$this->data['entry_item_display'] = $this->language->get('entry_item_display');
		$this->data['entry_item_help'] = $this->language->get('entry_item_help');
		$this->data['entry_item_options'] = $this->language->get('entry_item_options');
		$this->data['entry_test'] = $this->language->get('entry_test');
		$this->data['entry_debug'] = $this->language->get('entry_debug');
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_completed_status'] = $this->language->get('entry_completed_status');
		$this->data['entry_denied_status'] = $this->language->get('entry_denied_status');
		$this->data['entry_failed_status'] = $this->language->get('entry_failed_status');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['merchant_id'])) {
			$this->data['error_merchant_id'] = $this->error['merchant_id'];
		} else {
			$this->data['error_merchant_id'] = '';
		}
		
		if (isset($this->error['application_id'])) {
			$this->data['error_application_id'] = $this->error['application_id'];
		} else {
			$this->data['error_application_id'] = '';
		}
		
		if (isset($this->error['item_display'])) {
			$this->data['error_item_display'] = $this->error['item_display'];
		} else {
			$this->data['error_item_display'] = '';
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/ipayy', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('payment/ipayy', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['ipayy_merchant_id'])) {
			$this->data['ipayy_merchant_id'] = $this->request->post['ipayy_merchant_id'];
		} else {
			$this->data['ipayy_merchant_id'] = $this->config->get('ipayy_merchant_id');
		}
		
		if (isset($this->request->post['ipayy_application_id'])) {
			$this->data['ipayy_application_id'] = $this->request->post['ipayy_application_id'];
		} else {
			$this->data['ipayy_application_id'] = $this->config->get('ipayy_application_id');
		}
		
		if (isset($this->request->post['ipayy_item_display'])) {
			$this->data['ipayy_item_display'] = $this->request->post['ipayy_item_display'];
		} elseif ($this->config->get('ipayy_item_display')) {
			$this->data['ipayy_item_display'] = $this->config->get('ipayy_item_display');
		} else {
			$this->data['ipayy_item_display'] = $this->language->get('default_item_display');
		}
		
		if (isset($this->request->post['ipayy_item_display_other'])) {
			$this->data['ipayy_item_display_other'] = $this->request->post['ipayy_item_display_other'];
		} else {
			$this->data['ipayy_item_display_other'] = $this->config->get('ipayy_item_display_other');
		}

		if (isset($this->request->post['ipayy_test'])) {
			$this->data['ipayy_test'] = $this->request->post['ipayy_test'];
		} else {
			$this->data['ipayy_test'] = $this->config->get('ipayy_test');
		}

		if (isset($this->request->post['ipayy_transaction'])) {
			$this->data['ipayy_transaction'] = $this->request->post['ipayy_transaction'];
		} else {
			$this->data['ipayy_transaction'] = $this->config->get('ipayy_transaction');
		}

		if (isset($this->request->post['ipayy_debug'])) {
			$this->data['ipayy_debug'] = $this->request->post['ipayy_debug'];
		} else {
			$this->data['ipayy_debug'] = $this->config->get('ipayy_debug');
		}
		
		if (isset($this->request->post['ipayy_total'])) {
			$this->data['ipayy_total'] = $this->request->post['ipayy_total'];
		} elseif ($this->config->get('ipayy_total')) {
			$this->data['ipayy_total'] = $this->config->get('ipayy_total');
		} else {
			$this->data['ipayy_total'] = $this->language->get('default_max_total');
		} 

		if (isset($this->request->post['ipayy_completed_status_id'])) {
			$this->data['ipayy_completed_status_id'] = $this->request->post['ipayy_completed_status_id'];
		} else {
			$this->data['ipayy_completed_status_id'] = $this->config->get('ipayy_completed_status_id');
		}	
		
		if (isset($this->request->post['ipayy_denied_status_id'])) {
			$this->data['ipayy_denied_status_id'] = $this->request->post['ipayy_denied_status_id'];
		} else {
			$this->data['ipayy_denied_status_id'] = $this->config->get('ipayy_denied_status_id');
		}
		
		if (isset($this->request->post['ipayy_failed_status_id'])) {
			$this->data['ipayy_failed_status_id'] = $this->request->post['ipayy_failed_status_id'];
		} else {
			$this->data['ipayy_failed_status_id'] = $this->config->get('ipayy_failed_status_id');
		}	
								
		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['ipayy_status'])) {
			$this->data['ipayy_status'] = $this->request->post['ipayy_status'];
		} else {
			$this->data['ipayy_status'] = $this->config->get('ipayy_status');
		}
		
		if (isset($this->request->post['ipayy_sort_order'])) {
			$this->data['ipayy_sort_order'] = $this->request->post['ipayy_sort_order'];
		} elseif ($this->config->get('ipayy_sort_order')) {
			$this->data['ipayy_sort_order'] = $this->config->get('ipayy_sort_order');
		} else {
			$this->data['ipayy_sort_order'] = $this->language->get('default_sort_order');
		}

		$this->template = 'payment/ipayy.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/ipayy')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['ipayy_merchant_id']) {
			$this->error['merchant_id'] = $this->language->get('error_merchant_id_required');
		}
		
		if (!$this->request->post['ipayy_application_id']) {
			$this->error['application_id'] = $this->language->get('error_application_id_required');
		}
		
		if ($this->request->post['ipayy_item_display'] == "Custom" && !$this->request->post['ipayy_item_display_other']) {
			$this->error['item_display'] = $this->language->get('error_item_display_other_required');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>