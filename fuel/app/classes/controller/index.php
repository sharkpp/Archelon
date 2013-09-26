<?php

class Controller_Index extends Controller_Base
{

	public function action_index()
	{
	//	try
	//	{
			if (!self::installed())
			{
				Response::redirect('setup');
			}
			else if (Auth::check())
			{
				$this->action_dashboard();
			}
			else
			{
				$this->action_welcome();
			}
	//	}
	//	catch (\Exception $e)
	//	{
	//		// マイグレーションされている？
	//		if (!self::installed())
	//		{
	//			Response::redirect('setup');
	//		}
	//		throw $e;
	//	}
	}

	public function action_404()
	{
		// 404 ページの表示
		return $this->response_404();
	}

	public function action_setup()
	{
		if (self::installed())
		{
			Response::redirect('');
		}

		$data = array();

		Config::load('db', true);

		if ($dsn = Config::get('db.default.connection.dsn', ''))
		{ // 'pgsql:host=localhost;dbname=fuel_db',
			$dsn = preg_split('/[:;]/', $dsn, null, PREG_SPLIT_NO_EMPTY);
			$dsn[0] = 'pdo_type='.$dsn[0];
			$dsn_parts = array();
			array_map(function($val) use (&$dsn_parts) { $v = explode('=', $val); $dsn_parts[$v[0]]=$v[1]; }, $dsn);
			$pdo_type_to_connection = array('mysql' => 'pdo_mysql',
			                                'pgsql' => 'pdo_pgsql');
			$db_config = array(
					'connection' => Arr::get($pdo_type_to_connection, Arr::get($dsn_parts, 'pdo_type', 'mysql'), 'pdo_mysql'),
					'hostname' => Arr::get($dsn_parts, 'host',   'localhost'),
					'port'     => Arr::get($dsn_parts, 'port',   '3306'),
					'database' => Arr::get($dsn_parts, 'dbname', 'aaas'),
				);
		} else {
			$db_config = array(
					'connection' => Config::get('db.default.type', 'mysql'),
					'hostname' => Config::get('db.default.connection.hostname', 'localhost'),
					'port'     => Config::get('db.default.connection.port',     '3306'),
					'database' => Config::get('db.default.connection.database', 'aaas'),
				);
		}

		$setup_form = array(
			'db' => array(
				'db_connection' => array(
					'label'      => '接続に使うドライバの種類',
					'validation' => array('required'),
					'form'       => array('type' => 'select', 'options' => array('mysql' => 'mysql',
					                                                             'mysqli' => 'mysqli',
					                                                             'pdo_mysql' => 'PDO(MySQL)',
					                                                             'pdo_pgsql' => 'PDO(PostgreSQL)')),
					'default'    => Input::post('db_username', Arr::get($db_config, 'connection', 'pdo_mysql')),
				),
				'db_hostname' => array(
					'label'      => 'ホスト名',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('db_hostname', Arr::get($db_config, 'hostname', 'localhost')),
				),
				'db_port' => array(
					'label'      => 'ポート',
					'validation' => array('required', 'numeric_between' => array(1, 65535), 'valid_string' => array(array('numeric'))),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('db_port', Arr::get($db_config, 'port', '3306')),
				),
				'db_database' => array(
					'label'      => 'データベース名',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('db_database', Arr::get($db_config, 'database', 'aaas')),
				),
				'db_username' => array(
					'label'      => 'ユーザー名',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('db_username', Config::get('db.default.connection.username', 'root')),
				),
				'db_password' => array(
					'label'      => 'パスワード',
					'validation' => array(),
					'form'       => array('type' => 'password'),
					'default'    => Input::post('db_password', Config::get('db.default.connection.password', 'test')),
				),
				'db_table_prefix' => array(
					'label'      => 'テーブル名のプレフィックス',
					'validation' => array(),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('db_table_prefix', Config::get('db.default.table_prefix', '')),
				),
			//	'send_email_when_expiration_password' => array(
			//		'label'      => 'パスワードの期限が切れたらメールを送信',
			//		'validation' => array(),
			//		'form'       => array('type' => 'checkbox'),
			//		'default'    => 'on',
			//	),
			),
			'auth' => array(
				'auth_simple_enable' => array(
					'label'      => 'ログイン認証を使う',
					'validation' => array(),
					'form'       => array('type' => 'checkbox'),
					'default'    => Input::post('auth_simple_enable', 'on'),
				),
				'auth_ldap_enable' => array(
					'label'      => 'LDAP認証を使う',
					'validation' => array(),
					'form'       => array('type' => 'checkbox'),
					'default'    => Input::post('auth_ldap_enable', ''),
				),
			),
		);

		$validator = \Validation::forge('validation');

		// 入力フォームを構築
		$form = array();
		foreach ($setup_form as $category => $form_info)
		{
			foreach ($form_info as $field => $info)
			{
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
		if (\Input::post())
		{
			// 入力内容の検証
			if ($validator->run())
			{
				// 設定に反映
				$db_connection = $validator->validated('db_connection');
				switch ($db_connection)
				{
				case 'mysql':
				case 'mysqli':
					Config::set('db.default.type',                $db_connection);
					Config::set('db.default.connection.hostname', $validator->validated('db_hostname'));
					Config::set('db.default.connection.port',     $validator->validated('db_port'));
					Config::set('db.default.connection.database', $validator->validated('db_database'));
					break;
				case 'pdo_mysql':
				case 'pdo_pgsql':
					$dsn = substr($db_connection, 4) . ':'; // 種別を取り出す
					$dsn.= 'host='  . $validator->validated('db_hostname') . ';';
					$dsn.= 'port='  . $validator->validated('db_port')     . ';';
					$dsn.= 'dbname='. $validator->validated('db_database') . ';';
					Config::set('db.default.connection.dsn', trim($dsn, ';'));
					break;
				}
				Config::set('db.default.connection.username', $validator->validated('db_username'));
				Config::set('db.default.connection.password', $validator->validated('db_password'));
				Config::set('db.default.table_prefix',        $validator->validated('db_table_prefix'));

				// マイグレーション
				$migrate = array();
				$migrate[] = array('name' => 'default', 'type' => 'app');
				foreach(array_keys(Module::loaded()) as $name) {
					$migrate[] = array('name' => $name, 'type' => 'module');
				}
				foreach(array_keys(Package::loaded()) as $name) {
					$migrate[] = array('name' => $name, 'type' => 'package');
				}

				try
				{
					Config::save('db', 'db');

					foreach($migrate as $param)
					{
						Migrate::latest($param['name'], $param['type']);
					}

					Response::redirect('');
				}
				catch (\Fuel\Core\Database_Exception $e)
				{
					\Log::error($e->getMessage());
					$data['error_message'] = 'エラーが発生しました('.$e->getMessage().') '
					                       . 'データベースの接続先などを見直してください。';
					foreach ($form['db'] as &$field)
					{
						$field['error_message'] = '&nbsp;';
					}
				}
				catch (\Exception $e)
				{
					\Log::error($e->getMessage());
					$data['error_message'] = 'エラーが発生しました('.$e->getMessage().')';
				}
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

		$data['form'] = $form;

		$this->template->title = 'セットアップ';
		$this->template->content = View::forge('index/setup', $data);
	}

	public function action_about()
	{
		$this->template->title = 'About';
		$this->template->content = View::forge('index/about');
	}

	public function action_welcome()
	{
		$this->template->content = View::forge('index/welcome');
	}

	public function action_dashboard()
	{
		$accounts = \Model_Account::query()
						->where('user_id', \Auth::instance()->get_user_id()[1])
					//	->related('connector')
						->get();

		$data['accounts'] = array();
		foreach ($accounts as $account)
		{
			$data['accounts'][] = array(
					'title' => $account->connector->screen_name,
					'connector' => $account->connector->name,
					'id' => $account->id,
					'description' => unserialize($account->description),
					'api_key' => $account->api_key,
				);
		}

		$this->template->script  = View::forge('index/dashboard.js');
		$this->template->content = View::forge('index/dashboard', $data);
		$this->template->content->set_safe('accounts', $data['accounts']);
	}

	public function action_signup()
	{
		$val = Validation::forge('validation');
		// ユーザー名、パスワード、メールアドレス、いずれも必須
		$val->add('username', __('Username'))
		    ->add_rule('required');
		$val->add('password', __('Password'))
		    ->add_rule('required')
		    ->add_rule('min_length', 1);
		$val->add('email', __('Email'))
		    ->add_rule('required')
		    ->add_rule('valid_email');

		$data['username_error_message'] =
		$data['password_error_message'] =
		$data['email_error_message']    =
		$data['error_message']          = '';

		if (Input::post())
		{
			if ($val->run())
			{
				try
				{
					// saltを初期化
					$salt = '';
					for ($i = 0; $i < 8; $i++)
					{
						$salt .= pack('n', mt_rand(0, 0xFFFF));
					}
					$salt = base64_encode($salt);

					if (false !== Auth::create_user(
									$val->validated('username'),
									$val->validated('password'),
									$val->validated('email'),
									Auth\Model\Auth_User::query()->count() ? 1 : 100, // 一人もいない場合は管理者とする
									array(
										'salt' => $salt,
									)
								))
					{
						if (Auth::login())
						{
							Response::redirect('');
						}
					}
				}
				catch (Auth\SimpleUserUpdateException $e)
				{
					// SimpleAuth::create_user()の実装を参考
					switch ($e->getCode())
					{
					case 1: $data['error_message']          = $e->getMessage(); break;
					case 2: $data['email_error_message']    = $e->getMessage(); break;
					case 3: $data['username_error_message'] = $e->getMessage(); break;
					}
				}
				catch (\Exception $e)
				{
					\Log::error($e->getMessage());
					$data['error_message'] = 'エラーが発生しました';
				}
			}
			else
			{
				foreach (array('username', 'password', 'email') as $field)
				{
					$data[$field . '_error_message'] = $val->validated($field) ? '' : $val->error($field);
				}
			}
		}

		$this->template->breadcrumb = array('サインアップ' => 'index/signup');
		$this->template->title = 'サインアップ';
		$this->template->content = View::forge('index/signup', $data);
	}

	public function action_signin()
	{
		$val = Validation::forge('validation');
		// ユーザー名、パスワード、いずれも必須
		$val->add('username', __('Username'))
		    ->add_rule('required');
		$val->add('password', __('Password'))
		    ->add_rule('required')
		    ->add_rule('min_length', 1);

		$data['username_error_message'] =
		$data['password_error_message'] =
		$data['error_message']          = '';

		if (Input::post())
		{
			if ($val->run())
			{
				if (Auth::login())
				{
					Response::redirect(Str::lower(urldecode(Input::get('url'))));
				}
				else
				{
					$data['error_message'] = 'ユーザー名もしくはパスワードが違います';
				}
			}
			else
			{
				foreach (array('username', 'password') as $field)
				{
					$data[$field . '_error_message'] = $val->validated($field) ? '' : $val->error($field);
				}
			}
		}

		$this->template->breadcrumb = array('サインイン' => 'index/signin');
		$this->template->title = 'サインイン';
		$this->template->content = View::forge('index/signin', $data);
	}

	public function action_signout()
	{
		Auth::logout();
		Response::redirect('');
	}

}
