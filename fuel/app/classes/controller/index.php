<?php

class Controller_Index extends Controller_Base
{

	public function action_index()
	{
		if (Auth::check())
		{
			$this->action_dashboard();
		}
		else
		{
			$this->action_welcome();
		}
	}

	public function action_welcome()
	{
		$data = array();
		$this->template->content = View::forge('index/welcome', $data);
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
				catch (Exception $e)
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
