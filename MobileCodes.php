<?php

class Motofan_Model_MobileCodes {

	/**
	 *
	 * @var Motofan_Model_DbTable_MobileCodes
	 */
	protected $_db;

	public function __construct() {
		parent::__construct();

		$this->_db = Motofan_Model_DbTable_MobileCodes::getInstance();
	}

	public function alreadySent($id_user) {

		return $this->_db->alreadySent($id_user);
	}

	public function create($phone, $id_user, $code, $country = 'ES'){

		if ($id_code = $this->_db->alreadySent($id_user)) {

			return $id_code;
		}

		$data = array(
			'id_user' => $id_user,
			'phone' => $phone,
			'code' => $code,
			'status' => Motofan_Model_DbTable_MobileCodes::STATUS_PENDING,
			'country' => $country,
			'date_creation' => time(),
			'ip_client' => Utils::getIP(),
		);

		return $this->_db->save($data);
	}

	public function setCodeAsUsed($id_code, $id_user){

		$data = array(
			'id' => $id_code,
			'id_user' => $id_user,
			'status' => Motofan_Model_DbTable_MobileCodes::STATUS_USED,
			'date_updated'	=>	time(),
		);

		return $this->_db->save($data);
	}

	public function validateCode($id_code, $id_user){

		$data = array(
			'id' => $id_code,
			'id_user' => $id_user,
			'status' => Motofan_Model_DbTable_MobileCodes::STATUS_SENT,
			'date_updated'	=>	time(),
		);

		return $this->_db->save($data);
	}

	public function errorCode($id_code, $id_user){

		return $this->_db->errorCode($id_code, $id_user);
	}

	public function getDataRelatedtoCode($code, $id_user){

		return $this->_db->getDataRelatedtoCode($code, $id_user);
	}

	public function updateExpiredPending($id_user){

		return $this->_db->updateExpiredPending($id_user);
	}

	public function isSpam($ip){

		return $this->_db->isSpam($ip);
	}

}
