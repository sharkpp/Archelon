<?php

class Model_Account extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'user_id',
		'connector_id',
		'connector_id',
		'description',
		'api_key',
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
		// API KEY ‚ğ‰Šú‰»
		$salt_ = '';
		for ($i = 0; $i < 8; $i++)
		{
			$salt_ .= pack('n', mt_rand(0, 0xFFFF));
		}

		$crypto_key = \Config::get('crypt.crypto_key', null);
		$blowfish_salt_type = version_compare('5.3.7', PHP_VERSION, '<=') ? '$2y' : '$2a';
		$api_key = crypt($salt_, $blowfish_salt_type.'$02$'.md5($crypto_key).'$');

		$this->api_key = md5( $api_key . base64_decode(\Auth::instance()->get_profile_fields('salt', 'XXX')) );
	}
}
