<?php

class Model_Account extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'user_id',
		'connector_id',
		'connector_id',
		'description',
		'salt',
		'created_at',
		'updated_at',
		'deleted_at',
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'mysql_timestamp' => true,
		),
		'Orm\\Observer_Self' => array(
			'events' => array('before_insert')
		),
		'Observer_UserId' => array(
			'events' => array('before_save')
		),
	);

	protected static $_belongs_to = array(
		'connector' => array(
			'key_from' => 'connector_id',
			'model_to' => '\Model_Connector',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);
	protected static $_soft_delete = array(
		'mysql_timestamp' => true,
	);
	protected static $_table_name = 'accounts';

	public function _event_before_insert()
	{
		// salt‚ğ‰Šú‰»
		$salt_ = '';
		for ($i = 0; $i < 8; $i++)
		{
			$salt_ .= pack('n', mt_rand(0, 0xFFFF));
		}
		$this->salt = base64_encode($salt_);
	}
}
