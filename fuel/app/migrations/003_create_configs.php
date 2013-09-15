<?php

namespace Fuel\Migrations;

class Create_configs
{
	public function up()
	{
		\DBUtil::create_table('configs', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'file' => array('type' => 'text'),
			'config' => array('type' => 'text'),
			'created_at' => array('type' => 'timestamp', 'null' => true),
			'updated_at' => array('type' => 'timestamp', 'null' => true),

		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('configs');
	}
}