<?php

class Controller_Connector extends Controller_Base
{

	public function action_reload()
	{
		Autoloader::add_class('Fuel\Tasks\Connector', implode(DS, array(APPPATH,'tasks','connector.php')));
		// コネクタ一覧を更新
		$task = new \Fuel\Tasks\Connector;
		$message = $task->reload();

		$data['messages'] = explode("\r\n", $message);

		if (Input::is_ajax())
		{
			return View::forge('connector/reload.js', $data);
		}

		$this->template->breadcrumb = array('管理' => '', 'コネクタの再読み込み' => 'connector/config');
		$this->template->title = implode(' &raquo; ', array_keys($this->template->breadcrumb));
		$this->template->content = View::forge('connector/reload', $data);
	}

	public function action_config()
	{
		$data = array();
		$connectors = array();

		$q = \Model_Connector::query();
		foreach($q->get() as $row)
		{
			Module::load($row->name);
			$connector_class = Inflector::words_to_upper($row->name).'\\Connector';
			$connector = new $connector_class;
			$connectors[] = array(
					'connector'   => $row->name,
					'name'        => $connector->get_screen_name(),
					'description' => $connector->get_description(),
				);
		}

		$this->template->breadcrumb = array('管理' => '', 'コネクタの管理' => 'connector/config');
		$this->template->title = implode(' &raquo; ', array_keys($this->template->breadcrumb));
		$this->template->content = View::forge('connector/config', $data);
		$this->template->content->set_safe('connectors', $connectors);
	}

	public function action_docs()
	{
		$data = array();

		$connector_name = $this->param('connector');
		$type           = $this->param('type');
		$id             = $this->param('id');

		if ('api' != $type)
		{
			Response::redirect('');
		}

		Module::load($connector_name);
		$connector_class = Inflector::words_to_upper($connector_name).'\\Connector';
		$connector = new $connector_class;

		$account = Model_Account::find($id);

		$data['connector'] = $connector_name;
		$data['specs'] = $connector->get_api_spec();

		foreach ($data['specs'] as &$spec)
		{
			foreach ($spec['parameters'] as &$param)
			{
				if ('API_KEY' == $param['data_type'])
				{
					$param['value'] = $account->api_key;
				}
			}
		}

		$this->template->breadcrumb = array('APIドキュメント' => '', 'APIドキュメント' => '', 'aaa' => '');
		$this->template->title = implode(' &raquo; ', array_keys($this->template->breadcrumb));
		$this->template->script  = View::forge('connector/docs.js', $data);
		$this->template->content = View::forge('connector/docs', $data);
	}
}
