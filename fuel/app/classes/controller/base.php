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
		);
		if (in_array($method, $through_methods))
		{
			return;
		}
		// ログイン必須ページでログインしている状態か？のチェック
		$through_auth_methods = array( // ログインが必要ないページ
			'',
			'signup',
			'signin',
			'api',
		);
		if (!in_array($method, $through_auth_methods) &&
			!Auth::check())
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
			Auth::check())
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

	public static function authorized()
	{
		return self::installed() && Auth::check();
	}

	public static function installed()
	{
		return 0 !== Config::get('migrations.version.app.default', 0);
	}

	protected function response_404()
	{
		$this->template->title = '404 Not Found';
		$this->template->content = View::forge('index/404');
		return Response::forge($this->template, 404);
	}
}
