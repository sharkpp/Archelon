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
				$this->run_dashboard();
			}
			else
			{
				$this->run_welcome();
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

		// LDAP認証パッケージが読み込める？なら選択項目を表示
		try
		{
			\Package::load('ldapauth');
			$use_ldapauth = true;
		}
		catch (\Exception $e)
		{ // Ldap認証が無効の場合は表示しない
			$use_ldapauth = false;
		}

		Config::load('db', true);
		Config::load('ldapauth', true);

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
					'label'      => 'パスワード認証を使う',
					'validation' => array('required_whichever' => array('auth_driver'), 'match_field_value' => array('auth_driver', 'Simpleauth')),
					'form'       => array('type' => 'checkbox'),
					'default'    => Input::post('auth_simple_enable', \Input::post() ? false : true),
				),
				'auth_ldap_enable' => !$use_ldapauth ?: array(
					'label'      => 'LDAP認証を使う',
					'validation' => array('required_whichever' => array('auth_driver'), 'match_field_value' => array('auth_driver', 'Ldapauth')),
					'form'       => array('type' => 'checkbox'),
					'default'    => Input::post('auth_ldap_enable', \Input::post() ? false : true),
				),
				'auth_admin_user' => array(
					'label'      => '管理者ユーザー名',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('auth_admin_user', 'admin'),
				),
				'auth_admin_pass' => array(
					'label'      => '管理者パスワード',
					'validation' => array('required'),
					'form'       => array('type' => 'password'),
					'default'    => Input::post('auth_admin_pass', ''),
				),
				'auth_admin_pass2' => array(
					'label'      => '管理者パスワード(確認)',
					'validation' => array('required', 'match_field' => array('auth_admin_pass')),
					'form'       => array('type' => 'password'),
					'default'    => Input::post('auth_admin_pass2', ''),
				),
				'auth_admin_email' => array(
					'label'      => '管理者メールアドレス',
					'validation' => array('valid_email', 'match_field_value' => array('auth_driver', 'Simpleauth')),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('auth_admin_email', ''),
				),
				'auth_driver' => array(
					'label'      => '認証方式',
					'validation' => array('required'),
					'form'       => array('type' => 'select', 'options' => array('Simpleauth' => 'パスワード認証', 'Ldapauth' => 'Ldap認証')),
					'default'    => Input::post('auth_driver', 'Simpleauth'),
				),
			),
			'ldapauth' => array(
				'ldap_host' => !$use_ldapauth ?: array(
					'label'      => 'Ldapサーバー名',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('ldap_host', Config::get('ldapauth.host')),
				),
				'ldap_port' => !$use_ldapauth ?: array(
					'label'      => 'Ldapサーバーポート',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('ldap_port', Config::get('ldapauth.port')),
				),
				'ldap_secure' => !$use_ldapauth ?: array(
					'label'      => 'SSLを使用する',
					'validation' => array(),
					'form'       => array('type' => 'checkbox'),
					'default'    => Input::post('ldap_secure', \Input::post() ? '' : Config::get('ldapauth.secure')),
				),
				'ldap_username' => !$use_ldapauth ?: array(
					'label'      => 'ユーザー名',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('ldap_username', Config::get('ldapauth.username')),
				),
				'ldap_password' => !$use_ldapauth ?: array(
					'label'      => 'パスワード',
					'validation' => array(),
					'form'       => array('type' => 'password'),
					'default'    => Input::post('ldap_password', Config::get('ldapauth.password')),
				),
				'ldap_basedn' => !$use_ldapauth ?: array(
					'label'      => '基準DN',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('ldap_basedn', Config::get('ldapauth.basedn')),
				),
				'ldap_account' => !$use_ldapauth ?: array(
					'label'      => 'アカウント名フィールド',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('ldap_account', Config::get('ldapauth.account')),
				),
				'ldap_email' => !$use_ldapauth ?: array(
					'label'      => 'メールアドレスフィールド',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('ldap_email', Config::get('ldapauth.email')),
				),
				'ldap_firstname' => !$use_ldapauth ?: array(
					'label'      => '姓 フィールド',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('ldap_firstname', Config::get('ldapauth.firstname')),
				),
				'ldap_lastname' => !$use_ldapauth ?: array(
					'label'      => '名 フィールド',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('ldap_lastname', Config::get('ldapauth.lastname')),
				),
			),
		);

		$validator = \Validation::forge('validation');
		$validator->add_callable('Validation_Required'); // required_whichever用
		$validator->add_callable('Validation_Match');    // match_field_value用

		// 入力フォームを構築
		$form = array();
		foreach ($setup_form as $category => $form_info)
		{
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

				Config::set('auth.driver', array_merge(
												$validator->validated('auth_simple_enable') ? array('Simpleauth') : array(),
												$validator->validated('auth_ldap_enable')   ? array('Ldapauth')   : array()
											));
				Config::set('auth.verify_multiple_logins', true);
				Config::set('config.always_load.packages', array_merge(
																array('orm', 'auth'),
																$validator->validated('auth_ldap_enable') ? array('ldapauth') : array()
															));

				Config::set('ldapauth.host',      $validator->validated('ldap_host'));
				Config::set('ldapauth.port',      $validator->validated('ldap_port'));
				Config::set('ldapauth.secure',    $validator->validated('ldap_secure'));
				Config::set('ldapauth.username',  $validator->validated('ldap_username'));
				Config::set('ldapauth.password',  $validator->validated('ldap_password'));
				Config::set('ldapauth.basedn',    $validator->validated('ldap_basedn'));
				Config::set('ldapauth.account',   $validator->validated('ldap_account'));
				Config::set('ldapauth.email',     $validator->validated('ldap_email'));
				Config::set('ldapauth.firstname', $validator->validated('ldap_firstname'));
				Config::set('ldapauth.lastname',  $validator->validated('ldap_lastname'));
				Config::set('ldapauth.db.table_name', 'users_ldapauth');

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
					// ファイルがないとcoreやpackagesが優先されるので、空ファイルを設置
					File::update(APPPATH.'config', 'db.php', '');
					File::update(APPPATH.'config', 'auth.php', '');
					File::update(APPPATH.'config', 'ldapauth.php', '');
					File::update(APPPATH.'config'.DS.Fuel::$env, 'config.php', '');

					Config::save('db', 'db');
					Config::save('auth', 'auth');
					Config::save('ldapauth', 'ldapauth');
					Config::save(Fuel::$env.DS.'config.php', 'config');

					foreach($migrate as $param)
					{
						Migrate::latest($param['name'], $param['type']);
					}

					// 初期ユーザーを作成
					$auth_driver = $validator->validated('auth_driver');
					if ('Simpleauth' == $auth_driver)
					{ // パスワード認証の場合は作成してから認証
						// saltを初期化
						$salt = '';
						for ($i = 0; $i < 8; $i++)
						{
							$salt .= pack('n', mt_rand(0, 0xFFFF));
						}
						$salt = base64_encode($salt);

						if (false === Auth::instance($auth_driver)->create_user(
										$validator->validated('auth_admin_user'),
										$validator->validated('auth_admin_pass'),
										$validator->validated('auth_admin_email'),
										100, 
										array(
											'salt' => $salt,
										)
									))
						{
							throw new Exception('ユーザーの作成に失敗');
						}
					}
					if (!Auth::instance($auth_driver)->login($validator->validated('auth_admin_user'),
					                                         $validator->validated('auth_admin_pass')))
					{
						throw new Exception('認証に失敗');
					}
					if (!Auth::instance($auth_driver)->update_user(array('group' => 100)))
					{
						throw new Exception('ユーザー情報の更新に失敗');
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
				// 更新してしまったファイルを削除
				try { @File::delete(APPPATH.'config'.DS.'db.php'); } catch (\Exception $e) {}
				try { @File::delete(APPPATH.'config'.DS.'auth.php'); } catch (\Exception $e) {}
				try { @File::delete(APPPATH.'config'.DS.'ldapauth.php'); } catch (\Exception $e) {}
				try { @File::delete(APPPATH.'config'.DS.Fuel::$env.DS.'config.php'); } catch (\Exception $e) {}
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

	public function run_welcome()
	{
		$this->template->content = View::forge('index/welcome');
	}

	public function run_dashboard()
	{
		$user_id = \Auth::instance()->get_user_id();
		$accounts = \Model_Account::query()
						->where('user_id', $user_id[1])
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
		if (self::is_ldap_only())
		{
			Response::redirect('signin');
		}

		$data['username_error_message'] =
		$data['password_error_message'] =
		$data['email_error_message']    =
		$data['error_message']          = '';

		$signup_form = array(
				'username' => array(
					'label'      => 'ユーザー名',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('username', ''),
				),
				'password' => array(
					'label'      => 'パスワード',
					'validation' => array(),
					'form'       => array('type' => 'password'),
					'default'    => Input::post('password', ''),
				),
				'email' => array(
					'label'      => 'メールアドレス',
					'validation' => array('required', 'valid_email'),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('email', ''),
				),
			);

		$validator = \Validation::forge('validation');

		// 入力フォームを構築
		$form = array();
		foreach ($signup_form as $field => $info)
		{
			if (!is_array($info)) {
				continue;
			}
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
					!is_int($key) || 'required' != $value ?: $is_required = true;
					call_user_func_array(
							array($validat_field, 'add_rule'),
							is_int($key) ? array($value) : array_merge(array($key), $value)
						);
				});
		}

		if (Input::post())
		{
			// 入力内容の検証
			if ($validator->run())
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
									$validator->validated('username'),
									$validator->validated('password'),
									$validator->validated('email'),
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

		$this->template->breadcrumb = array('サインアップ' => 'index/signup');
		$this->template->title = 'サインアップ';
		$this->template->content = View::forge('index/signup', $data);
	}

	public function action_signin()
	{
		$data['username_error_message'] =
		$data['password_error_message'] =
		$data['error_message']          = '';

		$driver_type_list = array();
		foreach (Config::get('auth.driver', array()) as $driver)
		{
			switch ($driver)
			{
			case 'Simpleauth': $driver_type_list[$driver] =  'パスワード認証'; break;
			case 'Ldapauth': $driver_type_list[$driver] =  'Ldap認証'; break;
			}
		}

		$signin_form = array(
				'username' => array(
					'label'      => 'ユーザー名',
					'validation' => array('required', 'min_length' => array(1)),
					'form'       => array('type' => 'text'),
					'default'    => Input::post('username', ''),
				),
				'password' => array(
					'label'      => 'パスワード',
					'validation' => array(),
					'form'       => array('type' => 'password'),
					'default'    => Input::post('password', ''),
				),
				'driver' => count($driver_type_list) < 2?:array(
					'label'      => '認証方式',
					'validation' => array('required'),
					'form'       => array('type' => 'select', 'options' => $driver_type_list),
					'default'    => Input::post('driver', ''),
				),
			);

		$validator = \Validation::forge('validation');

		// 入力フォームを構築
		$form = array();
		foreach ($signin_form as $field => $info)
		{
			if (!is_array($info)) {
				continue;
			}
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
					!is_int($key) || 'required' != $value ?: $is_required = true;
					call_user_func_array(
							array($validat_field, 'add_rule'),
							is_int($key) ? array($value) : array_merge(array($key), $value)
						);
				});
		}

		if (Input::post())
		{
			// 入力内容の検証
			if ($validator->run())
			{
				if (count($driver_type_list) < 2)
				{ // フォームに存在しないので自動でセット
					$driver = array_keys($driver_type_list);
					$driver = array_shift($driver);
				}
				else
				{
					$driver = $validator->validated('driver');
				}
				if (Auth::instance($driver)->login())
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
