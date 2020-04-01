<?php 
class ControllerExtensionPaymentCmsopencart extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('extension/payment/cmsopencart');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('payment_cmsopencart', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		
		$data['entry_module'] = $this->language->get('entry_module');
		$data['entry_module_id'] = $this->language->get('entry_module_id');
		//$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_order_status'] = $this->language->get('entry_order_status');	
		$data['entry_order_fail_status'] = $this->language->get('entry_order_fail_status');	
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');		
		$data['text_edit'] = $this->language->get('text_edit');
		
		$data['entry_merchant'] = $this->language->get('entry_merchant');
		$data['entry_partner_name'] = $this->language->get('entry_partner_name');
		$data['entry_workingkey'] = $this->language->get('entry_workingkey');
		$data['entry_partner_id'] = $this->language->get('entry_partner_id');
		$data['entry_testurl'] = $this->language->get('entry_testurl');
		$data['entry_liveurl'] = $this->language->get('entry_liveurl');
		//$data['entry_salt'] = $this->language->get('entry_salt');
		$data['entry_test'] = $this->language->get('entry_test');
		//$data['entry_total'] = $this->language->get('entry_total');	
			
		$data['entry_vturl'] = $this->language->get('entry_vturl');
		$data['entry_access_key'] = $this->language->get('entry_access_key');
		$data['entry_secret_key'] = $this->language->get('entry_secret_key');
		
		$data['help_merchant'] = $this->language->get('help_merchant');
		$data['help_partner_name'] = $this->language->get('help_partner_name');
		$data['help_workingkey'] = $this->language->get('help_workingkey');
		$data['help_partner_id'] = $this->language->get('help_partner_id');
		$data['help_testurl'] = $this->language->get('help_testurl');
		$data['help_liveurl'] = $this->language->get('help_liveurl');
		$data['help_vturl'] = $this->language->get('help_vturl');
		$data['help_accesskey'] = $this->language->get('help_accesskey');
		$data['help_secretkey'] = $this->language->get('help_secretkey');
		//$data['help_total'] = $this->language->get('help_total');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
       // $data['help_salt'] = $this->language->get('help_salt');
		$data['tab_general'] = $this->language->get('tab_general');
		
		if(!isset($this->error['error_merchant'])) $this->error['error_merchant'] ='';
		if(!isset($this->error['error_workingkey'])) $this->error['error_workingkey'] ='';
		if(!isset($this->error['error_partner_id'])) $this->error['error_partner_id'] ='';
		if(!isset($this->error['error_partner_name'])) $this->error['error_partner_name'] ='';
		if(!isset($this->error['error_testurl'])) $this->error['error_testurl'] ='';
		if(!isset($this->error['error_liveurl'])) $this->error['error_liveurl'] ='';
		//if(!isset($this->error['error_salt'])) $this->error['error_salt'] = '';
		//if(!isset($this->error['error_currency'])) $this->error['error_currency'] = '';
		//if(!isset($this->error['error_route'])) $this->error['error_route'] = '';
		if(!isset($this->error['error_status'])) $this->error['error_status'] = '';
		if(!isset($this->error['error_module'])) $this->error['error_module'] = '';

 		if ($this->error) {
			$data = array_merge($data,$this->error);
		} 
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/payment/cmsopencart', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => ' :: '
   		);
				
		$data['action'] = $this->url->link('extension/payment/cmsopencart', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		if (isset($this->request->post['payment_cmsopencart_module'])) {
			$data['payment_cmsopencart_module'] = $this->request->post['payment_cmsopencart_module'];
		} else {
			$data['payment_cmsopencart_module'] = $this->config->get('payment_cmsopencart_module');
		}
		
		if (isset($this->request->post['payment_cmsopencart_cs_vturl'])) 
		{
			$data['payment_cmsopencart_cs_vturl'] = $this->request->post['payment_cmsopencart_cs_vturl'];
		} 
		else 
		{
			$data['payment_cmsopencart_cs_vturl'] = $this->config->get('payment_cmsopencart_cs_vturl');
		} 
		if (isset($this->request->post['payment_cmsopencart_cs_access_key'])) 
		{
			$data['payment_cmsopencart_cs_access_key'] = $this->request->post['payment_cmsopencart_cs_access_key'];
		} 
		else 
		{
			$data['payment_cmsopencart_cs_access_key'] = $this->config->get('payment_cmsopencart_cs_access_key');
		}
		if (isset($this->request->post['payment_cmsopencart_cs_secret_key'])) 
		{
			$data['payment_cmsopencart_cs_secret_key'] = $this->request->post['payment_cmsopencart_cs_secret_key'];
		} 
		else 
		{
			$data['payment_cmsopencart_cs_secret_key'] = $this->config->get('payment_cmsopencart_cs_secret_key');
		}

		
		
		if (isset($this->request->post['payment_cmsopencart_cms_merchant'])) {
			$data['payment_cmsopencart_cms_merchant'] = $this->request->post['payment_cmsopencart_cms_merchant'];
		} else {
			$data['payment_cmsopencart_cms_merchant'] = $this->config->get('payment_cmsopencart_cms_merchant');
		}
		
		if (isset($this->request->post['payment_cmsopencart_cms_workingkey'])) {
			$data['payment_cmsopencart_cms_workingkey'] = $this->request->post['payment_cmsopencart_cms_workingkey'];
		} else {
			$data['payment_cmsopencart_cms_workingkey'] = $this->config->get('payment_cmsopencart_cms_workingkey');
		}

		if (isset($this->request->post['payment_cmsopencart_cms_partner_id'])) {
			$data['payment_cmsopencart_cms_partner_id'] = $this->request->post['payment_cmsopencart_cms_partner_id'];
		} else {
			$data['payment_cmsopencart_cms_partner_id'] = $this->config->get('payment_cmsopencart_cms_partner_id');
		}

		if (isset($this->request->post['payment_cmsopencart_cms_partner_name'])) {
			$data['payment_cmsopencart_cms_partner_name'] = $this->request->post['payment_cmsopencart_cms_partner_name'];
		} else {
			$data['payment_cmsopencart_cms_partner_name'] = $this->config->get('payment_cmsopencart_cms_partner_name');
		}

		if (isset($this->request->post['payment_cmsopencart_cms_testurl'])) {
			$data['payment_cmsopencart_cms_testurl'] = $this->request->post['payment_cmsopencart_cms_testurl'];
		} else {
			$data['payment_cmsopencart_cms_testurl'] = $this->config->get('payment_cmsopencart_cms_testurl');
		}

		if (isset($this->request->post['payment_cmsopencart_cms_liveurl'])) {
			$data['payment_cmsopencart_cms_liveurl'] = $this->request->post['payment_cmsopencart_cms_liveurl'];
		} else {
			$data['payment_cmsopencart_cms_liveurl'] = $this->config->get('payment_cmsopencart_cms_liveurl');
		}
		
		
				
		if (isset($this->request->post['payment_cmsopencart_order_status_id'])) {
			$data['payment_cmsopencart_order_status_id'] = $this->request->post['payment_cmsopencart_order_status_id'];
		} else {
			$data['payment_cmsopencart_order_status_id'] = $this->config->get('payment_cmsopencart_order_status_id'); 
		} 

		if (isset($this->request->post['payment_cmsopencart_order_fail_status_id'])) {
			$data['payment_cmsopencart_order_fail_status_id'] = $this->request->post['payment_cmsopencart_order_fail_status_id'];
		} else {
			$data['payment_cmsopencart_order_fail_status_id'] = $this->config->get('payment_cmsopencart_order_fail_status_id'); 
		} 
		
		if (isset($this->request->post['payment_cmsopencart_order_cancel_status_id'])) {
			$data['payment_cmsopencart_order_cancel_status_id'] = $this->request->post['payment_cmsopencart_order_cancel_status_id'];
		} else {
			$data['payment_cmsopencart_order_cancel_status_id'] = $this->config->get('payment_cmsopencart_order_cancel_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		
		if (isset($this->request->post['payment_cmsopencart_status'])) {
			$data['payment_cmsopencart_status'] = $this->request->post['payment_cmsopencart_status'];
		} else {
			$data['payment_cmsopencart_status'] = $this->config->get('payment_cmsopencart_status');
		}
		
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

				
		$this->response->setOutput($this->load->view('extension/payment/cmsopencart', $data));
	}

	private function validate() {
		$flag=false;
		
		if (!$this->user->hasPermission('modify', 'extension/payment/cmsopencart')) {
			$this->error['error_warning'] = $this->language->get('error_permission');
		}
		//cms both parameters mandatory
		if( $this->request->post['payment_cmsopencart_cms_merchant'] ) {
			if (!$this->request->post['payment_cmsopencart_cms_merchant']) {
				$this->error['error_merchant'] = $this->language->get('error_merchant');
			}
			
			if (!$this->request->post['payment_cmsopencart_cms_workingkey']) {
				$this->error['error_workingkey'] = $this->language->get('error_workingkey');
			}

			if (!$this->request->post['payment_cmsopencart_cms_partner_id']) {
				$this->error['error_partner_id'] = $this->language->get('error_partner_id');
			}

			if (!$this->request->post['payment_cmsopencart_cms_partner_name']) {
				$this->error['error_partner_name'] = $this->language->get('error_partner_name');
			}

			if (!$this->request->post['payment_cmsopencart_cms_testurl']) {
				$this->error['error_testurl'] = $this->language->get('error_testurl');
			}

			if (!$this->request->post['payment_cmsopencart_cms_liveurl']) {
				$this->error['error_liveurl'] = $this->language->get('error_liveurl');
			}
			
			
		}
		if( $this->request->post['payment_cmsopencart_cms_merchant'] ) {
			$flag=true;	
		}
	
		
		if (!$this->request->post['payment_cmsopencart_module']) {
			$this->error['error_module'] = $this->language->get('error_module');
		}
		
		
		
		if(!$flag && $this->request->post['payment_cmsopencart_status'] == '1')
		{
			$this->error['error_status'] = $this->language->get('error_status');
		}

		return !$this->error;
	}
}
?>