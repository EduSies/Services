<?php

class Motofan_Model_DbTable_MobileCodes {

	//STATUS
	const STATUS_PENDING = 'PENDING';
	const STATUS_ERROR = 'ERROR';
	const STATUS_SENT = 'SENT';
	const STATUS_RETRY = 'RETRY';
	const STATUS_USED = 'USED';

	protected $_name = 'MobileCodes';

	public function getDataRelatedToCode($code, $id_user){

		$select = $this ->select()
						->from($this->_name, array('*'))
						->where('id_user = ?', $id_user)
						->where('code = ?', $code)
						->where('status = ?', self::STATUS_SENT);

		$row = $this->fetchRow($select);

		if ($row) {
			return $row->toArray();
		}

		return false;
	}

	public function errorCode($id_code, $id_user){

		$data = array(
			'status' => self::STATUS_ERROR,
			'date_updated'	=> time(),
		);

		$where[] = $this->getAdapter()->quoteInto('id = ?', $id_code);
		$where[] = $this->getAdapter()->quoteInto('id_user = ?', $id_user);

		return $this->update($data, $where);
	}

	public function alreadySent($id_user){

		$select = $this->select()
						->from($this->_name, array('id'))
						->where('id_user = ?', $id_user)
						->where('date_creation > unix_timestamp(now() - interval 15 minute)')
						->where('status = ?', self::STATUS_SENT)
						->order('id desc')
						->limit(1);

		$row = $this->fetchRow($select);

		if($row){

			$row = $row->toArray();

			return $row['id'];
		}

		return false;
	}

	public function updateExpiredPending($id_user){

		$status_pending = self::STATUS_PENDING;
		$status_sent = self::STATUS_SENT;

		$select = $this->select()
						->from($this->_name, array('*'))
						->where('id_user = ?', $id_user)
						->where('date_creation < unix_timestamp(now() - interval 15 minute)')
						->where("status = '$status_pending' OR status = '$status_sent'");

		$rows = $this->fetchAll($select);

		$ids = array();

		if ($rows) {
			foreach ($rows->toArray() as $data) {

				$where = array();

				$ids[] = $data['id'];

				$data_modify = array(
					'status' => self::STATUS_RETRY,
					'date_updated'	=> time(),
				);

				$where[] = $this->getAdapter()->quoteInto('id = ?', $data['id']);
				$where[] = $this->getAdapter()->quoteInto('id_user = ?', $data['id_user']);

				$ids[] = $this->update($data_modify, $where);

			}

			return $ids;
		}

		return false;
	}

	public function isSpam($ip){

		$select = $this->select()
						->from($this->_name, array('CID' => 'COUNT(id)'))
						->where('ip_client = ?', $ip)
						->where('date_creation > unix_timestamp(now() - interval 1 hour)');

		$smsCount = $this->fetchRow($select);

		if ($smsCount['CID'] >= MAX_SMS) {

			return true;

		} else {

			return false;
		}
	}

}
