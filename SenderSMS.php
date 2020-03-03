<?php

class Motofan_Helpers_SenderSMS {

	const ENCODEKEY = "";

	/**
	 * @var dotFan_View_Url
	 */
	private $_view_hlp;

	/**
	 * @var string
	 */
	private $_endpoint;

	/**
	 * @var dotFan_Api_Interface
	 */
	private $_interface;

	/**
	 * @var Zend_Translate
	 */
	private $_translate;

	/**
	 * @var Motofan_Model_MobileCodes
	 */
	private $_mcodes_model;


	public function __construct() {

		$this->_endpoint = URL_SENDER_SMS;

		$this->_interface = new dotFan_Api_Interface($this->_endpoint);
		$this->_users_model = Motofan_Model_Users::getInstance();
	}

	public function checkPhone($phone) {

		$result = $this->_interface->checkphone( dotFan_Api_Interface::GET, array(
			'phone'		=> $phone,
			'country'		=> 'ES',
			),
			false,	// raw
			true	// friendly url
		);

		return $result;
	}

	// Devuelve id code
	public function sendCode($phone, $id_user, $ip, $country = 'ES') {

		$this->_translate = Zend_Registry::get('Zend_Translate');
		$this->_mcodes_model = Motofan_Model_MobileCodes::getInstance();

		$code = $this->_createCode();

		$formatted_phone = $this->checkMobileNationalPrefix($phone, $country);
		$is_phone_valid = $this->checkPhone($formatted_phone);

		if ($is_phone_valid['success']) {

			if ($id = $this->_mcodes_model->alreadySent($id_user)) {
				$resp['success'] = 1;
				return $resp;
			}

			$this->_mcodes_model->updateExpiredPending($id_user);
			$isSpam = $this->_mcodes_model->isSpam(Utils::getIP());

			if (!$isSpam && ($id = $this->_mcodes_model->create($formatted_phone, $id_user, $code, $country))) {

				$this->_view_hlp = new dotFan_View_Url();

				$result = $this->_interface->enqueue( dotFan_Api_Interface::POST, array(
						'phone'			=>	$formatted_phone,
						'message'		=>	sprintf($this->_translate->_('Motofan.com: El código para validar tu numero de teléfono es %s'), $code),
						'ip'				=>	$ip,
						'callback'	=>	$this->_view_hlp->url(array('id_code' => $id, 'id_user' => $id_user), 'smsmobilevalidatecallback'),
					),
					false,	// raw
					false	// friendly url
				);

				if ($result['success']) {
					$resp['success'] = 1;
					return $resp;
				}
			}
		}

		return $is_phone_valid;
	}

	public function validateCode($id_code, $id_user) {

		$this->_mcodes_model = Motofan_Model_MobileCodes::getInstance();

		return $this->_mcodes_model->validateCode($id_code, $id_user);
	}

	// Comprueba que el registro sea correcto
	public function successfulCode($id_code, $id_user) {

		$this->_mcodes_model = Motofan_Model_MobileCodes::getInstance();

		// Update status
		$success = $this->_mcodes_model->setCodeAsUsed($id_code, $id_user);

		return $success;
	}

	// Comprueba q el registro sea correcto
	public function errorCode($id_code, $id_user) {

		$this->_mcodes_model = Motofan_Model_MobileCodes::getInstance();

		return $this->_mcodes_model->errorCode($id_code, $id_user);
	}

	public function getDataRelatedToCode($code, $id_user) {

		// Updatea con estos parametros
		$this->_mcodes_model = Motofan_Model_MobileCodes::getInstance();

		if( $data =  $this->_mcodes_model->getDataRelatedToCode($code, $id_user)){

			$data['phone'] = $this->checkMobileNationalPrefix($data['phone'], 'ES', true);

			return $data;
		}

		return false;
	}

	protected function _createCode() {

		$seed = bin2hex(openssl_random_pseudo_bytes(3));

		// Para evitar confusiones:
		// Sustituyo ceros y letras o/O
		// Sustituir l minuscula e I mayusculas

		$seed = strtr($seed, array(
			'0' => rand(1,9),
			'O' => rand(1,9),
			'o' => rand(1,9),
			'l' => rand(1,9),
			'I' => rand(1,9)
		));

		return $seed;
	}

	public function checkMobileNationalPrefix($phone, $country, $reverse = false) {

		$phone = str_replace(' ', '', $phone);

		$intl_prefix = PHONE_PREFIX_ES;

		if($this->_startsWith($phone, $intl_prefix)){

			if(!$reverse){
				return $phone;
			}

			return substr($phone, 3);

		}else{

			if(!$reverse){
				return $intl_prefix.$phone;
			}

			return $phone;
		}
	}

	protected function _startsWith($haystack, $needle) {
		// Search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

}
