<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Exaapi 
{
    protected $_ci;
    protected $priv_key_id;
    protected $emoc_ip;
    protected $ovmm_user;
    protected $ovmm_pass;
    protected $akm_user;
    protected $akm_pass;

    function __construct($config = array())
    {
        $this->_ci =& get_instance();
        $this->_ci->load->library('rest');

        // If a URL was passed to the library
        empty($config) OR $this->initialize($config);
    }

    public function setprivkey($privkey) 
    {
    	$this->priv_key_id = $privkey;
    }

    public function setControlServer($emoc_ip)
    {
    	$this->emoc_ip = $emoc_ip;
    }

    public function initialize($config)
    {
    	isset($config['emoc_ip']) && $this->emoc_ip = $config['emoc_ip'];
    	isset($config['ovmm_user']) && $this->ovmm_user = $config['ovmm_user'];
    	isset($config['ovmm_pass']) && $this->ovmm_pass	= $config['ovmm_pass'];
    	isset($config['akm_user']) && $this->akm_user = $config['akm_user'];
    	isset($config['akm_pass']) && $this->akm_pass = $config['akm_pass'];
    }

    public function deinitialize()
    {
    	$this->emoc_ip = "";
    	$this->ovmm_user = "";
    	$this->ovmm_pass = "";
    	$this->akm_user = "";
    	$this->akm_pass = "";
    	return null;
    }

    private function _prepare_ovm_config()
    {
    	$config = array(
	        'server' => 'https://'.$this->emoc_ip.':7002/ovm/core/wsapi/rest/',
	        'http_user' => $this->ovmm_user,
	        'http_pass' => $this->ovmm_pass,
	        'http_auth' => 'basic',
	        'ssl_verify_peer' => FALSE,
	    );

	    return $config;
    }

    private function _prepare_akm_config()
    {
    	$config = array(
	        'server' => 'https://'.$this->akm_user.':'.$this->akm_pass.'@'.$this->emoc_ip.'/akm/',
	        'ssl_verify_peer' => FALSE,
	    );

	    return $config;
    }

    private function _prepare_akm_query($action = array())
    {
    	$uri = (is_array($action) ? http_build_query($action) : $params);
    	$timestamp = round(microtime(true) * 1000);
    	$add_param = array(
			'Version' => '1',
			'Timestamp' => $timestamp,
			'Expires' => $timestamp + 300000
		);

		return $uri."&".http_build_query($add_param);

    }

    private function _prepare_iaas_query($action  = array())
    {
    	$uri = (is_array($action) ? http_build_query($action) : $params);
    	$timestamp = round(microtime(true) * 1000);
    	
    	$add_param = array(
			'Version' => '1',
			'Timestamp' => $timestamp,
			'Expires' => $timestamp + 300000
		);

		$dataRequest = $uri."&".http_build_query($add_param);
		
		$sign_data = "POST\n".$this->emoc_ip."\n/iaas/\n".$dataRequest."\n";

		$sign_param = array(
			'SignatureMethod' => 'SHA512withRSA',
			'SignatureVersion' => '1',
			'Signature' => $this->_create_signature($sign_data)
		);
		
		return $dataRequest."&".http_build_query($sign_param);
    }

    private function _create_signature($dataRequest)
    {
    	$pkeyid = openssl_pkey_get_private($this->priv_key_id);
    	openssl_sign($dataRequest, $signature, $pkeyid, "sha512WithRSAEncryption");
		$sign = base64_encode($signature);
		openssl_free_key($pkeyid);

		return $sign;
    }

    public function ovmm_get_all_vm()
	{
		$this->_ci->rest->initialize($this->_prepare_ovm_config());
		$this->_ci->rest->format('application/json');
		$this->_ci->rest->http_header('Accept', 'application/json');
	     
	    //$data['result'] = $this->rest->get('Server', NULL, 'application/json');
	    return $this->_ci->rest->get('Vm', NULL, 'application/json');
	   	
	   	//$this->load->view('output', $data);
	}

	public function akm_get_accounts()
	{
		$this->_ci->rest->initialize($this->_prepare_akm_config());
		$this->_ci->rest->format('application/json');
		$this->_ci->rest->http_header('Accept', 'application/json');

		$action_param = array(
			'Action' => 'DescribeAccounts'
		);
	     
		$res = $this->_ci->rest->get('', $this->_prepare_akm_query($action_param), 'application/json');
		return $res['items'];
	}

	public function akm_get_keys()
	{
		$this->_ci->rest->initialize($this->_prepare_akm_config());
		$this->_ci->rest->format('application/json');
		$this->_ci->rest->http_header('Accept', 'application/json');

		$action_param = array(
			'Action' => 'DescribeAccessKeys'
		);
	     
		$res = $this->_ci->rest->get('', $this->_prepare_akm_query($action_param), 'application/json');
		return $res['items'];
	}
	
	public function akm_set_keys($config) {
		return $config;
	}
    
}