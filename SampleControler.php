<?php if (!defined('BASEPATH')) exit('No direct script access allowed');



class SterklaControler extends REST_Controller
{

	protected $data;
	//static $StartTime;

	function __construct()
	{
		parent::__construct();
		$this->load->model('api/v15/SterklaModel');
		$this->load->helper(array('form', 'url'));
		$this->load->library('session');
		$this->load->library("braintree_lib");
		$this->load->library("S3");





		if ($this->post()) {
			$this->data = $this->post();
		} else {
			$this->data = $this->get();
		}

		date_default_timezone_set('UTC');
	}



	/* ================= Function for empty value or key  ================== */

	function getErrorMsg($required = array(), $request = array())
	{
		$notExist = true;
		foreach ($required as $value) {
			if (array_key_exists($value, $request)) {
				if ($request[$value] == "") {
					$data = array(
						"statusCode" => 400,
						"APICODERESULT" => "FAILED",
						"error_string" => $value . " is empty."
					);
					$this->response($data, 200);
					//echo json_encode($data);
					exit;
				}
			} else {
				$data = array(
					"statusCode" => 400,
					"APICODERESULT" => "FAILED",
					"error_string" => $value . " key is missing."
				);
				$this->response($data, 200);
				//echo json_encode($data);
				exit;
			}
		}
		return $notExist;
	}




	/* ==================== Check Access Key  =================== */

	function checkAssessKey($access_key)
	{
		$response    = $this->SterklaModel->checkAssessKey($access_key);

		if ($response == 1) {
			return true;
		} else {
			$data = array(
				"statusCode" => 500,
				"APICODERESULT"  => "Invalid Access Key"
			);
			$this->response($data, 200);
			exit();
		}
	}

    function checkCoach($user_id)
	{
		$response    = $this->SterklaModel->checkCoach($user_id);

		if ($response == 1) {
			return true;
		} else {
			$data = array(
				"statusCode" => 501,
				"APICODERESULT"  => "Invalid Access Key"
			);
			$this->response($data, 200);
			exit();
		}
	}


	/* ==================== Check Access Key  =================== */

	function checkMember($user_id)
	{
		$response    = $this->SterklaModel->checkMember($user_id);

		if ($response == 1) {
			return true;
		} else {
			$data = array(
				"statusCode" => 501,
				"APICODERESULT"  => "Invalid Access Key"
			);
			$this->response($data, 200);
			exit();
		}
	}


    	//Initiate Payment
	function initiatePayment_post()
	{
		$arrayRequired = array('user_id', 'paymentMethodNonce', 'amount');
		$var = $this->getErrorMsg($arrayRequired, $this->data);
		if ($var == true) {
			$paymentMethodNonce = $this->data['paymentMethodNonce'];

			$amount = $this->data['amount'];
			$response = $this->braintree_lib->initiate_payment($paymentMethodNonce, $amount);

			if (!empty($response)) {
				$this->data['paypal_trans_id'] = $response['balance_transaction'];
				$result = $this->SterklaModel->makePayment($this->data);
				$data =
					array(
						"APICODERESULT" => "Success.", "statusCode" => 200
					);
				$this->response($data, 200);
				exit;
			} else {
				$data = array("APICODERESULT" => "Something went wrong please try again.", "statusCode" => 202);
				$this->response($data, 202);
				exit;
			}
			$data = array(
				"APICODERESULT" => "Paypal Response.",
				"statusCode" => EXIT_SUCCESS,
				"result" => $response
			);
			$this->response($data, 200);
			exit;
		}
	}


    	/*====================== Get Category API ==================*/
	function getCategory_post()
	{
		$response    = $this->SterklaModel->getCategory();
		if ($response === false) {
			$data = array(
				"statusCode" => 400,
				"APICODERESULT"  => "Something went wrong"
			);
			$this->response($data, 200);
		} else {
			$data = array(
				"statusCode" => 200,
				"APICODERESULT"  => "Success",
				"result" => $response
			);
			$this->response($data, 200);
		}
	}



    

	/*====================== Get Personality API ==================*/
	function getPersonality_get()
	{
		$response    = $this->SterklaModel->getPersonality();
		if ($response === false) {
			$data = array(
				"statusCode" => 400,
				"APICODERESULT"  => "Something went wrong"
			);
			$this->response($data, 200);
		} else {
			$data = array(
				"statusCode" => 200,
				"APICODERESULT"  => "Success",
				"result" => $response
			);
			$this->response($data, 200);
		}
	}

    public function dashboard_post()
	{
		$arrayRequired = array('user_id', 'access_key', 'current_time');
		$var = $this->getErrorMsg($arrayRequired, $this->data);
		$post = $this->data;
		$this->checkAssessKey($post['access_key']);
		$this->checkCoach($post['user_id']);
		$response = $this->SterklaModel->dashboard($this->data);



		if ($response === false) {

			$data = array(
				"statusCode" => 400,
				"APICODERESULT"  => "Something went wrong"
			);
			$this->response($data, 200);
		} else {
			$data = array(
				"statusCode" => 200,
				"APICODERESULT"  => "Dashboard data",
				"result" => $response
			);
			$this->response($data, 200);
		}
	}


}