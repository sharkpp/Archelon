<?php

class Model_Account extends \Orm\Model_Soft
{
	protected static $_properties = array(
		'id',
		'user_id',
		'connector_id',
		'connector_id',
		'description',
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

}
