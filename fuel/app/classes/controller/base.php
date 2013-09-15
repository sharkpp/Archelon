<?php

class Controller_Base extends Controller_Template
{

	public function before()
	{
		parent::before();
		// 初期処理
		$method = Uri::segment(1);
		$controller_method = Uri::segment(3); // controller/*/???
		// ログインチェック
		$auth_methods = array(
			'',
			'signup',
			'signin',
			'controller',
		);
		if ((!in_array($method, $auth_methods) ||
			 ('controller' == $method && 'api' == $controller_method)) &&
			!Auth::check())
		{
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
			Response::redirect('');
		}
		// CSRFチェック
		if (Input::method() === 'POST' &&
			!Security::check_token())
		{
			Session::set_flash('error_message', 'セッションの有効期限が切れました。処理をおこなってください。');
			Response::redirect('signin?url=' . Uri::string());
		}
	}
}
