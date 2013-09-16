<?php
/**
 * Mymodule モジュールの中でのモジュールコントローラ
 */

abstract class Connector
{
	const BLOWFISH_COST = '03';

	abstract public function get_screen_name();

	abstract public function get_description();

	abstract public function get_api_spec();

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
		$api_ke = md5(crypt($salt, $blowfish_salt_type.'$'.self::BLOWFISH_COST.'$'.md5($salt_).'$'));
		
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
			$crypto_key = crypt(sha1($salt_), $blowfish_salt_type.'$'.self::BLOWFISH_COST.'$'.md5($salt_).'$');
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
			$crypto_key = crypt(sha1($salt_), $blowfish_salt_type.'$'.self::BLOWFISH_COST.'$'.md5($salt_).'$');
		}
		$key = $salt_ . $crypto_key;

		return \Crypt::decode(base64_decode($data), $key);
	}
}
