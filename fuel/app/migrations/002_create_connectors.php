<?php

namespace Fuel\Migrations;

class Create_connectors
{
	public function up()
	{
		\DBUtil::create_table('connectors', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'enable' => array('constraint' => 11, 'type' => 'int'),
			'name' => array('type' => 'text'),
			'screen_name' => array('type' => 'text'),
			'description' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),
			'deleted_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('connectors');
	}
}