<?php
/**
 * Connector コネクタ共通インターフェース＆メソッド
 */

abstract class Connector
{
	protected $connector_id = null;

	// コネクタ情報の取得
	abstract public function get_connector_spec();

	// API情報の取得
	abstract public function get_api_spec();

	// コネクタ設定フォームの取得
	abstract public function get_config_form();

	// コネクタ設定の更新
	abstract public function save_config($validation);

	// 登録情報フォームの取得
	abstract public function get_account_form($account_id = null);

	// 登録情報の更新
	abstract public function save_account($validation, $account_id = null);

	// 登録情報の削除
	abstract public function drop_account($account_id);



	// クラスを生成
	public static function forge($connector_id)
	{
		$q = \Model_Connector::query();
		if (is_numeric($connector_id))
		{ // コネクタIDで検索
			$q = $q->where('id', $connector_id);
		}
		else
		{ // コネクタ名で検索
			$q = $q->where('name', $connector_id);
		}

		if (!($connector = $q->get_one()))
		{
			return false;
		}

		\Module::load($connector->name);
		$connector_class = \Inflector::words_to_upper($connector->name).'\\Connector';

		try
		{
			$inst = new $connector_class;
			$inst->connector_id = $connector->id;
			return $inst;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	// コネクタのIDを取得
	public static function get_connector_id($connector_name = null)
	{
		if ('connector' == \Uri::segment(1) ||
			'api' == \Uri::segment(1) ||
			'docs' == \Uri::segment(1))
		{
			$connector_name = \Uri::segment(2);
		}
		if (!$connector_name)
		{
			return false;
		}

		$connector = \Model_Connector::query()
				->where('name', $connector_name)
				->get_one();

		if (!$connector)
		{
			return false;
		}

		return $connector->id;
	}

	// APIキーを取得
	public static function get_api_key($salt)
	{
		$salt_ = \Config::get('crypt.crypto_key', null);

		$blowfish_salt_type = version_compare('5.3.7', PHP_VERSION, '<=') ? '$2y' : '$2a';
		$api_ke = md5(crypt($salt, $blowfish_salt_type.'$03$'.md5($salt_).'$'));
		
		return $api_ke;
	}

	// 暗号化
	public static function encrypt($data, &$salt, $update_salt = false)
	{
		if (!$update_salt)
		{
			$salt_ = base64_decode($salt);
		}
		else
		{
			$salt_ = '';
			for ($i = 0; $i < 8; $i++)
			{
				$salt_ .= pack('n', mt_rand(0, 0xFFFF));
			}
			$salt = base64_encode($salt_);
		}

		$crypto_key = \Config::get('crypt.crypto_key', null);
		if (is_null($crypto_key))
		{
			$blowfish_salt_type = version_compare('5.3.7', PHP_VERSION, '<=') ? '$2y' : '$2a';
			$crypto_key = crypt(sha1($salt_), $blowfish_salt_type.'$03$'.md5($salt_).'$');
		}
		$key = $salt_ . $crypto_key;

		return base64_encode(\Crypt::encode($data, $key));
	}

	// 復号化
	public static function decrypt($data, $salt)
	{
		$salt_ = base64_decode($salt);

		$crypto_key = \Config::get('crypt.crypto_key', null);
		if (is_null($crypto_key))
		{
			$blowfish_salt_type = version_compare('5.3.7', PHP_VERSION, '<=') ? '$2y' : '$2a';
			$crypto_key = crypt(sha1($salt_), $blowfish_salt_type.'$03$'.md5($salt_).'$');
		}
		$key = $salt_ . $crypto_key;

		return \Crypt::decode(base64_decode($data), $key);
	}
}
