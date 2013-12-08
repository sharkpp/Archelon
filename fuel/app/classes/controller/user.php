<?php

class Controller_User extends Controller_Base
{

	public function action_index()
	{
		$data["subnav"] = array('index'=> 'active' );
		$this->template->title = 'User &raquo; Index';
		$this->template->content = View::forge('user/index', $data);
	}

	public function action_new()
	{
		$data["subnav"] = array('new'=> 'active' );
		$this->template->title = 'User &raquo; New';
		$this->template->content = View::forge('user/new', $data);
	}

	public function action_edit()
	{
		$data["subnav"] = array('edit'=> 'active' );
		$this->template->title = 'User &raquo; Edit';
		$this->template->content = View::forge('user/edit', $data);
	}

	public function action_delete()
	{
		$data["subnav"] = array('delete'=> 'active' );
		$this->template->title = 'User &raquo; Delete';
		$this->template->content = View::forge('user/delete', $data);
	}

	public function action_config($id = null)
	{
		
		$data = array();

		$config_form = array(
				'auth' => array(
					'username' => array(
						'label'      => 'ユーザー名',
						'validation' => array('required', 'min_length' => array(1)),
						'form'       => array('type' => 'text'),
						'default'    => Input::post('username', ''),
					),
					'password' => array(
						'label'      => '管理者パスワード',
						'validation' => array('required'),
						'form'       => array('type' => 'password'),
						'default'    => Input::post('password', ''),
					),
					'password2' => array(
						'label'      => '管理者パスワード(確認)',
						'validation' => array('required', 'match_field' => array('password')),
						'form'       => array('type' => 'password'),
						'default'    => Input::post('password2', ''),
					),
					'email' => array(
						'label'      => 'メールアドレス',
						'validation' => array('required', 'valid_email'),
						'form'       => array('type' => 'text'),
						'default'    => Input::post('email', ''),
					),
				)
			);

		$validator = \Validation::forge('validation');
		$validator->add_callable('Validation_Required'); // required_whichever用
		$validator->add_callable('Validation_Match');    // match_field_value用

		// 入力フォームを構築
		$form = array();
		foreach ($config_form as $category => $form_info)
		{
			if (!is_array($form_info)) {
				continue;
			}
			foreach ($form_info as $field => $info)
			{
				if (!is_array($info)) {
					continue;
				}
				$form[$category][$field] = array(
						'name'     => $field,
						'label'    => \Arr::get($info, 'label', ''),
						'form'     => \Arr::get($info, 'form', ''),
						'required' => false,
						'value'    => \Input::post($field, \Arr::get($info, 'default', '')),
						'error_message' => '',
					);
				$is_required = &$form[$category][$field]['required'];
				// バリデーションルールを追加
				$validat_field = $validator->add($field, $form[$category][$field]['label']);
				$info['validation'] = \Arr::get($info, 'validation', array());
				array_walk($info['validation'],
					function($value, $key) use (&$validat_field, &$is_required) {
						!is_int($key) || 'required' != $value ?: $is_required = true;
						call_user_func_array(
								array($validat_field, 'add_rule'),
								is_int($key) ? array($value) : array_merge(array($key), $value)
							);
					});
			}
		}

		if (Input::post())
		{
			// 入力内容の検証
			if ($validator->run())
			{
			//	if (Auth::instance($driver)->login())
			//	{
			//		Response::redirect(Str::lower(urldecode(Input::get('url'))));
			//	}
			//	else
			//	{
			//		$data['error_message'] = 'ユーザー名もしくはパスワードが違います';
			//	}
			}
			else
			{
				foreach ($form as $category => &$form_info)
				{
					foreach ($form_info as &$field)
					{
						$field['error_message']
							= $validator->validated($field['name'])
								? '' : $validator->error($field['name']);
					}
				}
			}
		}

		$data['form_kind'] = array('auth' => '認証関連');
		$data['form'] = $form;

		$this->template->breadcrumb = array('設定' => 'user/config');
		$this->template->title = implode(' &raquo; ', array_keys($this->template->breadcrumb));
		$this->template->content = View::forge('user/config', $data);
	}

}
