<?php

class Controller_Account extends Controller_Base
{

	public function action_index()
	{
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'Account &raquo; Index';
		$this->template->content = View::forge('account/index', $data);
	}

	public function action_connect()
	{
		$data = array();
		$connectors = array();

		$q = \Model_Connector::query();
		foreach($q->get() as $row)
		{
			$connectors[] = array(
					'connector'   => $row->name,
					'name'        => $row->screen_name,
					'description' => $row->description,
				);
		}

		$this->template->breadcrumb = array('アカウント追加' => 'account/connect');
		$this->template->title = 'アカウント追加';
		$this->template->content = View::forge('account/connect', $data);
		$this->template->content->set_safe('connectors', $connectors);
	}

	public function action_edit()
	{
		$data = array();
		$this->template->title = 'Account &raquo; Edit';
		$this->template->content = View::forge('account/edit', $data);
	}

	public function action_disconnect()
	{
		$data = array();
		$this->template->title = 'Account &raquo; Delete';
		$this->template->content = View::forge('account/disconnect', $data);
	}

}
