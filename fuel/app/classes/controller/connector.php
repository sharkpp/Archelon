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

	public function action_admin()
	{
		$data = array();
		$connectors = array();

		$q = \Model_Connector::query();

		foreach($q->get() as $row)
		{
			$connectors[] = array(
					'id'          => $row->id,
					'connector'   => $row->name,
					'name'        => $row->screen_name,
					'description' => $row->description,
				);
		}

		$this->template->breadcrumb = array('管理' => '', 'コネクタの管理' => 'connector/admin');
		$this->template->title = implode(' &raquo; ', array_keys($this->template->breadcrumb));
		$this->template->content = View::forge('connector/admin', $data);
		$this->template->content->set_safe('connectors', $connectors);
	}

	public function action_config($connector_id = null)
	{
		$data = array();
		$data['error_message'] = '';

		if (!($connector = \Connector::forge($connector_id)))
		{ // 指定されたコネクタがおかしいので飛ばす
			// 404 ページの表示
			return $this->response_404();
		}

		$connector_spec = $connector->get_connector_spec();
		$screen_name = \Arr::get($connector_spec, 'screen_name', '');

		$config_form = $connector->get_config_form();

		$validator = \Validation::forge('validation');

		// 入力フォームを構築
		$form = array();
		foreach ($config_form as $field => $info)
		{
			$form[$field] = array(
					'name'     => $field,
					'label'    => \Arr::get($info, 'label', ''),
					'form'     => \Arr::get($info, 'form', ''),
					'required' => false,
					'value'    => \Input::post($field, \Arr::get($info, 'default', '')),
					'error_message' => '',
				);
			$is_required = &$form[$field]['required'];
			// バリデーションルールを追加
			$validat_field = $validator->add($field, $form[$field]['label']);
			$info['validation'] = \Arr::get($info, 'validation', array());
			array_walk($info['validation'],
				function($value, $key) use (&$validat_field, &$is_required) {
					if (is_int($key)) {
						'required' != $value ?: $is_required = true;
						$validat_field->add_rule($value);
					} else {
						$validat_field->add_rule($key, $value);
					}
				});
		}

		if (\Input::post())
		{
			// 入力内容の検証
			if ($validator->run())
			{
				if ($connector->save_config($validator))
				{
					\Response::redirect('connector/admin');
				}
				else
				{
					$data['error_message'] = '設定が保存できませんでした';
				}
			}
			else
			{
				foreach ($form as &$field)
				{
					$field['error_message']
						= $validator->validated($field['name'])
							? '' : $validator->error($field['name']);
				}
			}
		}

		$data['form'] = $form;

		$this->template->breadcrumb = array('管理' => '', 'コネクタの管理' => 'connector/admin',
		                                    $screen_name . ' コネクタの設定' => 'connector/config');
		$this->template->title = implode(' &raquo; ', array_keys($this->template->breadcrumb));
		$this->template->content = View::forge('connector/config', $data);
		$this->template->content->set_safe('connector_name', $screen_name);
	}

	public function action_docs()
	{
		$data = array();

		$connector_name = $this->param('connector');
		$type           = $this->param('type');
		$id             = $this->param('id');

		if ('api' != $type)
		{
			// 404 ページの表示
			return $this->response_404();
		}

		if (!($connector = Connector::forge($connector_name)) ||
			!($account = Model_Account::find($id)))
		{
			// 404 ページの表示
			return $this->response_404();
		}

		$connector_spec = $connector->get_connector_spec();
		$screen_name = \Arr::get($connector_spec, 'screen_name', '');

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

		$this->template->breadcrumb = array('APIドキュメント' => '', 'APIドキュメント' => '', $screen_name.' のAPIサンプル' => '');
		$this->template->title = implode(' &raquo; ', array_keys($this->template->breadcrumb));
		$this->template->script  = View::forge('connector/docs.js', $data);
		$this->template->content = View::forge('connector/docs', $data);
	}
}
