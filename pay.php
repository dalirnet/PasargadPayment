<?php 
class PAY extends controller
{	
	// ----------------------
	//	property
	// ----------------------
	public $client;
	public $result;
	public $curl;
	public $terminal	= array(
		'pasargad'	=> array(
			'terminal'	=> 'XXXXXX',
			'user'		=> 'XXXXXX'
		)
	);
	public $params		= array();
	public $namespace	= 'http://interfaces.core.sw.bps.com/';
	
	// ----------------------
	//	__construct
	// ----------------------
	public function __construct()
	{
		parent::__construct();	
		require_once BASEPATH.'Class/curl.php';
	}
	
	// ----------------------
	//	pasargad
	// ----------------------
	public function pasargad($id,$price)
	{
		require_once BASEPATH.'Class/RSAProcessor.class.php';
		$key = new RSAProcessor(BASEPATH.'Class/certificate.xml',RSAKeyType::XMLFile);		
		$this->params['callBackUrl']	= BASEURL.'payment/callback/pasargad';
		$this->params['orderId']		= $id;
		$this->params['amount']			= $price;
		$this->params['localDate']		= date('Y/m/d H:i:s');
		$this->params['localTime']		= date('Y/m/d H:i:s');
		$this->params['payerId']		= '1003';
		$data = '#';
		$data .= $this->terminal['pasargad']['user'].'#';
		$data .= $this->terminal['pasargad']['terminal'].'#';
		$data .= $this->params['orderId'].'#';
		$data .= $this->params['localDate'].'#';
		$data .= $this->params['amount'].'#';
		$data .= $this->params['callBackUrl'].'#';
		$data .= $this->params['payerId'].'#';
		$data .= $this->params['localTime'].'#';
		$this->params['sign'] = base64_encode($key->sign(sha1($data,true)));
		$data = array(
			'bank'	=> 'pasargad',
			'data'	=> array(
				'orderId'		=> $this->params['orderId'],
				'localDate' 	=> $this->params['localDate'],
				'amount'		=> $this->params['amount'],
				'terminal'		=> $this->terminal['pasargad']['terminal'],
				'user'			=> $this->terminal['pasargad']['user'],
				'callBackUrl' 	=> $this->params['callBackUrl'],
				'localTime' 	=> $this->params['localTime'],
				'payerId' 		=> $this->params['payerId'],
				'sign'			=> $this->params['sign'],
			)
		);
		$this->view('system/bank_form',$data);
	}
	
	// ----------------------
	//	Verify_pasargad
	// ----------------------
	public function verify_pasargad($id,$date)
	{
		$resutl 	= $this->db->get('customers2',array('pcode' => $id))->row();
		require_once BASEPATH.'library/class/RSAProcessor.class.php';
		$key = new RSAProcessor(BASEPATH.'library/class/certificate.xml',RSAKeyType::XMLFile);		
		$this->params['orderId']		= $id;
		$this->params['amount']			= $resutl['price'];
		$this->params['localDate']		= $date;
		$this->params['localTime']		= date('Y/m/d H:i:s');
		$data = '#';
		$data .= $this->terminal['pasargad']['user'].'#';
		$data .= $this->terminal['pasargad']['terminal'].'#';
		$data .= $this->params['orderId'].'#';
		$data .= $this->params['localDate'].'#';
		$data .= $this->params['amount'].'#';
		$data .= $this->params['localTime'].'#';
		$this->params['sign'] = base64_encode($key->sign(sha1($data,true)));
		$this->curl	= new curl;
		$this->curl->set_ssl();
		$this->curl->post_param('merchantCode',$this->terminal['pasargad']['user']);
		$this->curl->post_param('terminalCode',$this->terminal['pasargad']['terminal']);
		$this->curl->post_param('invoiceNumber',$this->params['orderId']);
		$this->curl->post_param('invoiceDate',$this->params['localDate']);
		$this->curl->post_param('amount',$this->params['amount']);
		$this->curl->post_param('timeStamp',$this->params['localTime']);
		$this->curl->post_param('sign',$this->params['sign']);
		$this->curl->execute('https://pep.shaparak.ir/VerifyPayment.aspx');
		return $this->parse_xml($this->curl->out);
	}
	
	
	// ----------------------
	//	check_status_pasargad
	// ----------------------
	public function check_status_pasargad($ref)
	{
		$this->curl	= new curl;
		$this->curl->set_ssl();
		$this->curl->post_param('invoiceUID',$ref);
		$this->curl->execute('https://pep.shaparak.ir/CheckTransactionResult.aspx');
		return $this->parse_xml($this->curl->out);
	}
	
	
	// ----------------------
	//	parse_xml
	// ----------------------
	public function parse_xml($input)
	{
		$return = json_decode(json_encode(simplexml_load_string($input)),true);
		return $return;
	}

}
