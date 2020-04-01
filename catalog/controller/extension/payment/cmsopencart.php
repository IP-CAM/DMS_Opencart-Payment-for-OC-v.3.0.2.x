<?php
class ControllerExtensionPaymentCmsopencart extends Controller {
	
	public function index()
	{
		$this->load->model('checkout/order');	
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('config_order_status_id'), "Default order status before payment.", false);
		
		if($order_info['payment_method'] == 'cmsopencart') {
			$data=$this->process_cms();
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'extension/payment/cmsopencart_cms')) {
				return $this->load->view($this->config->get('config_template') . 'extension/payment/cmsopencart_cms', $data);	
			} else {
				return $this->load->view('extension/payment/cmsopencart_cms', $data);
			}		
		}
		
		
	}

	 private function process_cs()
	{
		$data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']); 		
		
		$this->load->language('extension/payment/cmsopencart');
		$data['cs_module'] = $this->config->get('payment_cmsopencart_module');
		$data['cs_vturl'] = $this->config->get('payment_cmsopencart_cs_vturl');
		$data['cs_access_key'] = $this->config->get('payment_cmsopencart_cs_access_key');
		$data['cs_secret_key'] = $this->config->get('payment_cmsopencart_cs_secret_key');
		$data['cs_merchant_trans_id'] = rand(10,90).'ORD'.$order_info['order_id'];
		$data['currency'] = $order_info['currency_code'];
		$total = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$ntotal = $this->currency->convert( $total, $this->session->data['currency'],$data['currency']);
		$data['total'] = sprintf("%.2f", $ntotal);
		
		$data['firstname'] = $order_info['payment_firstname'];
		$data['lastname'] = $order_info['payment_lastname'];
		$data['addr1'] = $order_info['payment_address_1'];
		$data['city'] = $order_info['payment_city'];
		$data['state'] = $order_info['payment_zone'];
		$data['zip'] = $order_info['payment_postcode'];
		$data['country'] = $order_info['payment_country'];
		$data['email'] = $order_info['email'];
		$data['phone'] = $order_info['telephone'];
				
		//$vtUrl = $data['cs_vturl'];
		$currency =$data['currency'];	
		//$merchantTxnId = $data['cs_merchant_trans_id'];
		$orderAmount = $data['total'];		
		//$tmpdata = "$vtUrl$orderAmount$merchantTxnId$currency";
		
		//$secSignature = $this->generateHmacKey($tmpdata,$data['cs_secret_key']);
		$action = ""; 
		
		$data['action']=$action;
		//$data['secSignature']=$secSignature;			
		$data['products'] = array();
		$products = $this->cart->getProducts();     
		foreach ($products as $product) 
		{
			$data['products'][] = array(
				'product_id'  => $product['product_id'],
				'name' => $product['name'],
				'description' => $product['name'],
				'quantity'    => $product['quantity'],
				'price'  => $this->currency->format($product['price'], 
						$order_info['currency_code'], $order_info['currency_value'], false));
		}
		$data['lang'] = $this->session->data['language'];
		$data['vt_url'] = $vtUrl;
		$data['redir_url'] = $this->url->link('extension/payment/cmsopencart/callback', '', 'SSL');			
		$data['notify_url'] = $this->url->link('extension/payment/cmsopencart/callback', '', 'SSL');
		
		return $data;
			
	} 

	public function callback() { //callback and notify
		$this->load->model('checkout/order');
		$data['cs_module'] = $this->config->get('payment_cmsopencart_module');
		$data['cs_secret_key'] = $this->config->get('payment_cmsopencart_cs_secret_key');
		$workingkey = $this->config->get('cmsopencart_cms_workingkey');


		$amount = $_POST['amount']; 
		$desc = $_POST['desc']; 
		$status = $_POST['status'];
		$checksum = $_POST['checksum'];
		$billingdiscriptor = $_POST['descriptor'];
		$token = $_POST['token'];
		$trackingid = "null"; //$_REQUEST['trackingid'];

		if($_REQUEST['trackingid'] !=null && $_REQUEST['trackingid'] != "")
		{
		$trackingid=$_REQUEST['trackingid'];	
		}
			
//$finalStatus = "This is Amount --- " . $amount . "<br>" . "This is Desc --- " . $desc . "<br>" . "This is status -- " . $status . "<br>" . "This is checksum --- " . $checksum . "<br>" . "This is billingdiscriptor --- " . $billingdiscriptor . "<br>" . "This is token --- " . $token . "<br>" . "This is Tracking Id --- " . $trackingid . "<br>";    

       // echo "<script> alert('".$finalStatus."'); </script>";
		//exit();

			
		$str = $trackingid .'|'. $desc .'|' . $amount .'|' . $status .'|'. $workingkey;
		$finalString = md5($str);
		//$respSig = $checksum;
		$respSig = $finalString;

		$this->load->model('checkout/order');	
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);  
		$order_id = $order_info['order_id'];

/*****************************************************************************************************************************/
		//$pdata = array();
		//if (isset($_POST['TxStatus'])) {
			//$pdata = $_POST;				
		//}
		//else
		//{
			//invalid return data
			//if($this->session) $this->session->data['error'] = "Transaction error ....";		
		//	if($this->response) $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		//	return;
	//	}
		
		//resp signature validation
		//$str=$pdata['TxId'].$pdata['TxStatus'].$pdata['amount'].$pdata['pgTxnNo'].$pdata['issuerRefNo'].$pdata['authIdCode'].$pdata['firstName'].$pdata['lastName'].$pdata['pgRespCode'].$pdata['addressZip'];
		
		//$respSig=$pdata['signature'];
		
		//$order_id = substr(trim($pdata['TxId']),5);
		
		if($finalString == $respSig)
		{ 	
			if ($_POST['status'] == 'Y')
			{
				//echo "<script> alert('".$_POST['status']."') </script>";
				//exit();
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_cmsopencart_order_status_id'),$_POST['status'],true);
				if($this->response) $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));	

                 echo "<script> alert('".$_POST['status']."'); </script>";	
                 exit();				 
			}
			elseif ($_POST['status'] == 'N') 
			{
				//echo "<script> alert('".$_POST['status']."') </script>";
				//exit();
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_cmsopencart_order_fail_status_id'),$_POST['status'],true);					
				if($this->session) $this->session->data['error'] = "Transaction Declined/Failed";
				if($this->response) $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			}
			elseif ($_POST['status'] == 'C') 
			{	
				
				//echo "<script> alert('".$_POST['status']."') </script>";
				//exit();
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_cmsopencart_order_cancel_status_id'),$_POST['status'],true);					
				if($this->session) $this->session->data['error'] = "Transaction has been Cancelled";
				if($this->response) $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
			}
			
		}
		else //signature mismatch
		{
			if($this->session) $this->session->data['error'] = "Invalid or forged transactiond..";		//forged 
			if($this->response) $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		}
	}

	private function process_cms() {	
    	$data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');
		$this->language->load('extension/payment/cmsopencart');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
		$data['merchant'] = $this->config->get('payment_cmsopencart_cms_merchant');
		
		 /////////////////////////////////////Start Cms Vital  Information /////////////////////////////////
		
		if($this->config->get('payment_cmsopencart_module')=='Test')
			//$data['action'] = 'https://sandboxsecure.payu.in/_payment.php';
			//$data['action'] = 'https://staging.paymentz.com/transaction/PayProcessController';
			$data['action'] = $this->config->get('payment_cmsopencart_cms_testurl');
		else
		  //  $data['action'] = 'https://secure.payu.in/_payment.php';
		    $data['action'] = $this->config->get('payment_cmsopencart_cms_liveurl');
			
		//$txnid        = 	$this->session->data['order_id'];

		             
		$data['toid'] = $this->config->get('payment_cmsopencart_cms_merchant');
		$data['key'] = $this->config->get('payment_cmsopencart_cms_workingkey');
		$data['testurl'] = $this->config->get('payment_cmsopencart_cms_testurl');
		$data['liveurl'] = $this->config->get('payment_cmsopencart_cms_liveurl');
		$data['totype'] = $this->config->get('payment_cmsopencart_cms_partner_name');
		$data['cardtype'] = '';
		$data['paymenttype'] = '';
		$data['reservedField1'] = '';
		$data['reservedField2'] = '';
		$data['pctype'] = '1_1|1_2';
		$data['partenerid'] = $this->config->get('payment_cmsopencart_cms_partner_id');
		$data['ipaddr'] = $this->config->get('payment_cmsopencart_cms_ipaddr');
		//$data['txnid'] = $txnid;
		$data['amount'] = (int)$order_info['total'];
		$data['description'] = $order_info['order_id'];
		$data['currency'] = $this->config->get('payment_cmsopencart_cms_currency');
		$data['currency'] = $order_info['currency_code'];;




		//$data['productinfo'] = 'opencart products information';
		$data['firstname'] = $order_info['payment_firstname'];
		$data['Lastname'] = $order_info['payment_lastname'];
		$data['Zipcode'] = $order_info['payment_postcode'];
		$data['TMPL_zip'] = $order_info['payment_postcode'];
		$data['zip'] = $order_info['payment_postcode'];
		$data['email'] = $order_info['email'];
		$data['phone'] = $order_info['telephone'];
		$data['street'] = $order_info['payment_address_1'] .' '. $order_info['payment_address_2'];
        //$data['address2'] = $order_info['payment_address_2'];
        $data['state'] = $order_info['payment_zone'];
        $data['city']=$order_info['payment_city'];
        $data['country']=$order_info['payment_country'];

		$order_result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order`");
		$order_iso_code_country = $order_result->row['payment_country'];
		$order_iso_code_country_id = $order_result->row['payment_country_id'];

		$country_result = $this->db->query('SELECT * FROM '.DB_PREFIX.'country WHERE country_id = ' . $order_result->row['payment_country_id']);
		$country_iso_code = $country_result->row['iso_code_2'];
		$data['country']=  $order_info['payment_iso_code_2'];


/********************************************************************************************************/
		
		$country_code = array(
					"AF"=>"093", 
					"AX"=>"358", 
					"AL"=>"355",
					"DZ"=>"231",
					"AS"=>"684",
					"AD"=>"376",
					"AO"=>"244",
					"AI"=>"001",
					"AQ"=>"000",
					"AG"=>"001",
					"AR"=>"054",
					"AM"=>"374",
					"AW"=>"297",
					"AU"=>"061",
					"AT"=>"043",
					"AZ"=>"994",
					"BS"=>"001",
					"BH"=>"973",
					"BD"=>"880",
					"BB"=>"001",
					"BY"=>"375",
					"BE"=>"032",
					"BZ"=>"501",
					"BJ"=>"229",
					"BM"=>"001",
					"BT"=>"975",
					"BO"=>"591",
					"BA"=>"387",
					"BW"=>"267",
					"BV"=>"000",
					"BR"=>"055",
					"IO"=>"246",
					"VG"=>"001",
					"BN"=>"673",
					"BG"=>"359",
					"BF"=>"226",
					"BI"=>"257",
					"KH"=>"855",
					"CM"=>"237",
					"CA"=>"001",
					"CV"=>"238",
					"KY"=>"001",
					"CF"=>"236",
					"TD"=>"235",
					"CL"=>"056",
					"CN"=>"086",
					"CX"=>"061",
					"CC"=>"061",
					"CC"=>"061",
					"CO"=>"057",
					"KM"=>"269",
					"CK"=>"682",
					"CR"=>"506",
					"CI"=>"225",
					"HR"=>"385",
					"CU"=>"053",
					"CY"=>"357",
					"CZ"=>"420",
					"CD"=>"243",
					"DK"=>"045",
					"DJ"=>"253",
					"DM"=>"001",
					"DO"=>"001",
					"EC"=>"593",
					"EG"=>"020",
					"SV"=>"503",
					"GQ"=>"240",
					"ER"=>"291",
					"EE"=>"372",
					"ET"=>"251",
					"FK"=>"500",
					"FO"=>"298",
					"FJ"=>"679",
					"FI"=>"358",
					"FR"=>"033",
					"GF"=>"594",
					"PF"=>"689",
					"TF"=>"000",
					"GA"=>"241",
					"GM"=>"220",
					"GE"=>"995",
					"DE"=>"049",
					"GH"=>"233",
					"GI"=>"350",
					"GR"=>"030",
					"GL"=>"299",
					"GD"=>"001",
					"GP"=>"590",
					"GU"=>"001",
					"GT"=>"502",
					"GG"=>"000",
					"GN"=>"224",
					"GW"=>"245",
					"GY"=>"592",
					"HT"=>"509",
					"HM"=>"672",
					"HN"=>"504",
					"HK"=>"852",
					"HU"=>"036",
					"IS"=>"354",
					"IN"=>"091",
					"ID"=>"062",
					"IR"=>"098",
					"IQ"=>"964",
					"IE"=>"353",
					"IL"=>"972",
					"IT"=>"039",
					"JM"=>"001",
					"JP"=>"081",
					"JE"=>"044",
					"JO"=>"962",
					"KZ"=>"007",
					"KE"=>"254",
					"KI"=>"686",
					"KW"=>"965",
					"KG"=>"996",
					"LA"=>"856",
					"LV"=>"371",
					"LB"=>"961",
					"LS"=>"266",
					"LR"=>"231",
					"LY"=>"218",
					"LI"=>"423",
					"LT"=>"370",
					"LU"=>"352",
					"MO"=>"853",
					"MK"=>"389",
					"MG"=>"261",
					"MW"=>"265",
					"MY"=>"060",
					"MV"=>"960",
					"ML"=>"223",
					"MT"=>"356",
					"MH"=>"692",
					"MQ"=>"596",
					"MR"=>"222",
					"MU"=>"230",
					"YT"=>"269",
					"MX"=>"052",
					"FM"=>"691",
					"MD"=>"373",
					"MC"=>"377",
					"MN"=>"976",
					"ME"=>"382",
					"MS"=>"001",
					"MA"=>"212",
					"MZ"=>"258",
					"MM"=>"095",
					"NA"=>"264",
					"NR"=>"674",
					"NP"=>"977",
					"AN"=>"599",
					"NL"=>"031",
					"NC"=>"687",
					"NZ"=>"064",
					"NI"=>"505",
					"NE"=>"227",
					"NG"=>"234",
					"NU"=>"683",
					"NF"=>"672",
					"KP"=>"850",
					"MP"=>"001",
					"NO"=>"047",
					"OM"=>"968",
					"PK"=>"092",
					"PW"=>"680",
					"PS"=>"970",
					"PA"=>"507",
					"PG"=>"675",
					"PY"=>"595",
					"PE"=>"051",
					"PH"=>"063",
					"PN"=>"064",
					"PL"=>"048",
					"PT"=>"351",
					"PR"=>"001",
					"QA"=>"974",
					"CG"=>"242",
					"RE"=>"262",
					"RO"=>"040",
					"RU"=>"007",
					"RW"=>"250",
					"BL"=>"590",
					"SH"=>"290",
					"KN"=>"001",
					"LC"=>"001",
					"MF"=>"590",
					"PM"=>"508",
					"VC"=>"001",
					"WS"=>"685",
					"SM"=>"378",
					"ST"=>"239",
					"SA"=>"966",
					"SN"=>"221",
					"RS"=>"381",
					"SC"=>"248",
					"SL"=>"232",
					"SG"=>"065",
					"SK"=>"421",
					"SI"=>"386",
					"SB"=>"677",
					"SO"=>"252",
					"ZA"=>"027",
					"GS"=>"000",
					"KR"=>"082",
					"ES"=>"034",
					"LK"=>"094",
					"SD"=>"249",
					"SR"=>"597",
					"SJ"=>"047",
					"SZ"=>"268",
					"SE"=>"046",
					"CH"=>"041",
					"SY"=>"963",
					"TW"=>"886",
					"TJ"=>"992",
					"TZ"=>"255",
					"TH"=>"066",
					"TL"=>"670",
					"TG"=>"228",
					"TK"=>"690",
					"TO"=>"676",
					"TT"=>"001",
					"TN"=>"216",
					"TR"=>"090",
					"TM"=>"993",
					"TC"=>"001",
					"TV"=>"688",
					"UG"=>"256",
					"UA"=>"380",
					"AE"=>"971",
					"GB"=>"044",
					"US"=>"001",
					"VI"=>"001",
					"UY"=>"598",
					"UZ"=>"998",
					"VU"=>"678",
					"VA"=>"379",
					"VE"=>"058",
					"VN"=>"084",
					"WF"=>"681",
					"EH"=>"212",
					"YE"=>"967",
					"ZM"=>"260",
					"ZW"=>"263",
					);
					$country_value = $country_code[$order_info['payment_iso_code_2']];
		
		/********************************************************************************************************/

		$data['TMPL_telnocc'] = $country_value;
		$data['telnocc'] = $country_value;

		//$data['Pg'] = 'CC';
		$data['redirecturl'] = $this->url->link('extension/payment/cmsopencart/callback');
		$key          =  $this->config->get('payment_cmsopencart_cms_merchant');
		$amount       = (int)$order_info['total'];
		//$productInfo  = $data['productinfo'];
	    $firstname    = $order_info['payment_firstname'];
		$email        = $order_info['email'];

		$amount = $data['amount'];
		$toid = $this->config->get('payment_cmsopencart_cms_merchant');
		$workingkey = $this->config->get('payment_cmsopencart_cms_workingkey');
		$totype =  $this->config->get('payment_cmsopencart_cms_partner_name');
		$description = $data['description'];
		$redirecturl = $data['redirecturl'];

		//$checksum=md5($toid .'|'.$totype.'|'.$amount.'|'. $description .'|'. $redirecturl .'|'. $workingkey); 
		$checksum=md5($toid .'|'.$totype.'|'.number_format((float)$amount, 2, '.', '').'|'. $description .'|'. $redirecturl .'|'. $workingkey);
		
		$data['user_credentials'] = $this->data['key'].':'.$this->data['email'];
		$data['checksum'] = $checksum;

		return $data;		


	}
	
}
	
?>