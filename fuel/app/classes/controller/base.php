<?php

class Controller_Base extends Controller_Template
{

	public function before()
	{
		parent::before();
		// 初期処理
		$method = Uri::segment(1);
		$controller_method = Uri::segment(3); // controller/*/???
		//
		$through_methods = array( // ログインが必要ない次ページ
			'about',
			'setup',
			'404',
		);
		if (in_array($method, $through_methods))
		{
			return;
		}
		if (!self::installed())
		{
			Response::redirect('setup');
		}
		$authorized = null;
		// ログイン必須ページでログインしている状態か？のチェック
		$through_auth_methods = array( // ログインが必要ないページ
			'',
			'signup',
			'signin',
			'api',
		);
		if (!in_array($method, $through_auth_methods) &&
			((is_bool($authorized) && false ==- $authorized) ||
			 false === ($authorized = Auth::check())))
		{
			Log::warning('Required signup in "'.Uri::string().'"');
			Response::redirect('signin?url=' . urlencode(Uri::string()));
		}
		// ログイン済みチェック
		$nologin_methods = array(
			'signup',
			'signin',
		);
		if (in_array($method, $nologin_methods) &&
			((is_bool($authorized) && false !=- $authorized) ||
			 false !== ($authorized = Auth::check())))
		{
			Log::warning('Loggd in, and move to dashboard');
			Response::redirect('');
		}
		// CSRFチェック
		if (Input::method() === 'POST' &&
			!Security::check_token())
		{
			Log::warning('Expire form session!');
			Session::set_flash('error_message', 'セッションの有効期限が切れました。処理をおこなってください。');
			Response::redirect('signin?url=' . Uri::string());
		}
	}

	// ログイン済み？
	public static function authorized()
	{
		return self::installed() && Auth::check();
	}

	// インストール済み？
	public static function installed()
	{
		Config::load('migrations', true);
		return 0 !== Config::get('migrations.version.app.default', 0);
	}

	// Ldapのみ有効？
	public static function is_ldap_only()
	{
		$auth_drivers = Config::get('auth.driver', array());
		return 
			1 == count($auth_drivers) &&
			in_array('Ldapauth', $auth_drivers);
	}

	// 管理者？
	public static function is_admin()
	{
		return self::has_access('account.read');
	}

	// 表示名を取得
	public static function get_screen_name()
	{
		foreach (Config::get('auth.driver', array()) as $auth_driver)
		{
			$auth_inst = Auth::instance($auth_driver);
			$login_user = $auth_inst->get_user_id();
			if (false !== $login_user)
			{
				return $auth_inst->get_screen_name();
			}
		}
		return '';
	}

	// アクセス権がある？
	public static function has_access($rights)
	{
		foreach (Config::get('auth.driver', array()) as $auth_driver)
		{
			$auth_inst = Auth::instance($auth_driver);
			$login_user = $auth_inst->get_user_id();
			if (false !== $login_user &&
				$auth_inst->has_access($rights))
			{
				return true;
			}
		}
		return false;
	}

	protected function response_404()
	{
		$this->template->title = '404 Not Found';
		$this->template->content = View::forge('index/404');
		return Response::forge($this->template, 404);
	}
}
