<?php

namespace Fuel\Migrations;

class Create_accounts
{
	public function up()
	{
		\DBUtil::create_table('accounts', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'user_id' => array('constraint' => 11, 'type' => 'int'),
			'connector_id' => array('constraint' => 11, 'type' => 'int'),
			'description' => array('type' => 'text'),
			'api_key' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('accounts');
	}
}