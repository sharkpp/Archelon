<?php

class Controller_Account extends Controller_Base
{

	public function action_index()
	{
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'Account &raquo; Index';
		$this->template->content = View::forge('account/index', $data);
	}

	public function action_connect($connector_id = null)
	{
		$data = array();

		if (is_null($connector_id))
		{
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

			$this->template->breadcrumb = array('アカウント追加' => 'account/connect');
			$this->template->title = 'アカウント追加';
			$this->template->content = View::forge('account/connect', $data);
			$this->template->content->set_safe('connectors', $connectors);
		}
		else
		{
			$this->edit_account($connector_id, null);
		}
	}

	public function action_edit($account_id = null)
	{
		$data = array();

		if (!is_null($account_id))
		{
			$account = Model_Account::find($account_id);

			if ($account)
			{
				$this->edit_account($account->connector_id, $account_id);

				return;
			}
		}

		Response::redirect('account/connect');
	}

	public function action_disconnect($account_id = null)
	{
		if (!is_numeric($account_id))
		{
			Response::redirect('');
		}
		$account_id = intval($account_id);

		$data = array();

		if (!($account = \Model_Account::find($account_id)))
		{
			Response::redirect('');
		}

		if (Input::post())
		{
			if ('submit' != Input::post('submit', 'cancel'))
			{
				Response::redirect('');
			}

			$connector = \Connector::forge($account->connector_id);
			if (!$connector)
			{ // 指定されたコネクタがおかしいので飛ばす
				Response::redirect('');
			}

			$connector->drop_account($account_id);

			Response::redirect('');
		}

		if (!($connector = \Model_Connector::find($account->connector_id)))
		{
			Response::redirect('');
		}

		$data['description']    = @ unserialize($account->description);
		$data['connector_name'] = $connector->screen_name;

		$this->template->breadcrumb = array('アカウント追加' => 'account/connect');
		$this->template->title = 'アカウント追加';
		$this->template->content = View::forge('account/disconnect', $data);
		$this->template->content->set_safe('connector_name', $data['connector_name']);
	}


	private function edit_account($connector_id, $account_id)
	{
		$data['error_message'] = '';

		$connector = \Connector::forge($connector_id);
		if (!$connector)
		{ // 指定されたコネクタがおかしいので飛ばす
			Response::redirect('account/connect');
		}

		$connector_spec = $connector->get_connector_spec();
		$screen_name = \Arr::get($connector_spec, 'screen_name', '');

		$account_form = $connector->get_account_form($account_id);

		$validator = \Validation::forge('validation');

		// 入力フォームを構築
		$form = array();
		foreach ($account_form as $field => $info)
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
					is_int($key) && 'required' != $value ?: $is_required = true;
					call_user_func_array(
							array($validat_field, 'add_rule'),
							is_int($key) ? array($value) : array_merge(array($key), $value)
						);
				});
		}
		if (\Input::post())
		{
			// 入力内容の検証
			if ($validator->run())
			{
				if ($connector->save_account($validator, $account_id))
				{
					Response::redirect('');
				}
				else
				{
					$data['error_message'] = '保存できませんでした';
				}
			}
			else
			{
				foreach ($form as $field)
				{
					$field['error_message']
						= $validator->validated($field['name'])
							? '' : $validator->error($field['name']);
				}
			}
		}

		$data['form'] = $form;
		$data['save'] = !is_null($account_id);

		$this->template->breadcrumb = array('アカウント追加' => 'account/connect',
		                                    $screen_name . ' のアカウントを追加' => 'account/connect');
		$this->template->title = implode(' &raquo; ', array_keys($this->template->breadcrumb));
		$this->template->content = View::forge('account/form', $data);
		$this->template->content->set_safe('connector_name', $screen_name);
	}
}
