<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index1()
	{
		//$this->load->view('welcome_message');

		//echo "data\ndata";
		//echo 'data\ndata';

		$this->load->library('rest');
		$config = array(
	        'server' => 'https://10.60.114.141:7002/ovm/core/wsapi/rest/',
	        //'server' => 'https://10.60.114.141:7002/ovm/core/wsapi/rest/Vm/0004fb00000600001af38d655545ee4e',
	        'http_user' => 'admin',
	        'http_pass' => 'welcome1',
	        'http_auth' => 'basic',
	        'ssl_verify_peer' => FALSE,
	    );
		
		$this->rest->initialize($config);
		$this->rest->format('application/json');
		$this->rest->http_header('Accept', 'application/json');
	     
	    //$data['result'] = $this->rest->get('Server', NULL, 'application/json');
	    $data['result'] = $this->rest->get('Vm', NULL, 'application/json');
	   	
	   	$this->load->view('output', $data);
	}

	public function index()
	{
		$pkeyid = openssl_pkey_get_private("file:///Users/bkrisna/website/utilisasi/assets/keys/privkey.pem");
		//$date = date_create();

		$action = 'DescribeVservers';
		$vserverid = 'VSRV-33a7c98b-5807-41dd-971a-2213fc6996b8';
		$version = '1';
		$access_key = 'AK_1';
		$timestamp = round(microtime(true) * 1000);
		$expire = $timestamp+300000;
		$SignatureMethod="SHA512withRSA";
		$SignatureVersion='1';
		//$dataRequest = "Action=".$action."&Version=".$version."&Timestamp=".$timestamp."&Expires=".$expire."&ids.1=".$vserverid."&AccessKeyId=".$access_key;
		$dataRequest = "Action=".$action."&Version=".$version."&Timestamp=".$timestamp."&Expires=".$expire."&AccessKeyId=".$access_key;

		$data = "POST\n";
		$data .= "10.60.114.141\n";
		$data .= "/iaas/\n";
		$data .= $dataRequest."\n";


		// compute signature
		openssl_sign($data, $signature, $pkeyid, "sha512WithRSAEncryption");
		//openssl_sign($data, $signature, $pkeyid);

		//print_r($signature);
		$sign = base64_encode($signature);

		// free the key from memory
		openssl_free_key($pkeyid);

		//$server_path = "https://10.60.114.141/iaas/?".$dataRequest."&SignatureMethod=".$SignatureMethod."&SignatureVersion=".$SignatureVersion."&Signature=".$sign;
		$comm = "?".$dataRequest."&SignatureMethod=".$SignatureMethod."&SignatureVersion=".$SignatureVersion."&Signature=".$sign;

		$this->load->library('rest');
		$config = array(
	        'server' => 'https://10.60.114.141/iaas/',
	        'ssl_verify_peer' => FALSE,
	    );
		
		$this->rest->initialize($config);
	    
	    $data_res['result'] = $this->rest->get($comm, NULL, 'application/xml');
	   	
		echo $comm;
		
	   	$this->load->view('output', $data_res);
	}

	public function show_sby_account()
	{
		$rack_config = array(
			'emoc_ip' => '10.60.114.141',
	    	'ovmm_user' => 'admin',
	    	'ovmm_pass' => 'welcome1',
	    	'akm_user' => 'cloudadmin',
	    	'akm_pass' => 'cloudadmin'
		);

		$this->load->library('exaapi');
		$this->exaapi->initialize($rack_config);
		$data_res['result'] = $this->exaapi->akm_get_accounts();
		$this->exaapi->deinitialize();
		$this->load->view('output', $data_res);
	}

	public function show_sby_keys()
	{
		$rack_config = array(
			'emoc_ip' => '10.60.114.141',
	    	'ovmm_user' => 'admin',
	    	'ovmm_pass' => 'welcome1',
	    	'akm_user' => 'cloudadmin',
	    	'akm_pass' => 'cloudadmin'
		);

		$this->load->library('exaapi');
		$this->exaapi->initialize($rack_config);
		$data_res['result'] = $this->exaapi->akm_get_keys();
		$this->exaapi->deinitialize();
		$this->load->view('output', $data_res);
	}
	
	public function show_ovmm_vm()
	{
		$rack_config = array(
			'emoc_ip' => '10.60.114.141',
	    	'ovmm_user' => 'admin',
	    	'ovmm_pass' => 'welcome1',
	    	'akm_user' => 'cloudadmin',
	    	'akm_pass' => 'cloudadmin'
		);

		$this->load->library('exaapi');
		$this->exaapi->initialize($rack_config);
		$data_res['result'] = $this->exaapi->ovmm_get_all_vm();
		$this->exaapi->deinitialize();
		$this->load->view('output', $data_res);
	}
}
