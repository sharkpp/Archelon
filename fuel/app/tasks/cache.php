<?php

namespace Fuel\Tasks;

class Cache
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r cache:help
	 *
	 * @return string
	 */
	public function help()
	{
	}

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r cache:gc
	 *
	 * @return string
	 */
	public function gc()
	{
		\Config::load('cache', true);

		if ('file' != \Config::get('cache.driver'))
		{
			return;
		}

		$cache_path = \Config::get('cache.path', \Config::get('cache_dir', APPPATH.'cache'.DS));
		$cache_path = trim($cache_path, DS);

	//	$config = array(
	//		'basedir'		=> $cache_path,
	//		'use_locks'		=> true,
	//	);
	//	$area = \File::forge($config);

		try
		{
		//	$dir = $area->read_dir('cache', 0, null);
			$dir = \File::read_dir($cache_path, 0, null);

			// パスに変換
			$cache_list = array();
			$dir_stack = array( $dir );
			$folder_stack = array( '' );
			while (!empty($dir_stack))
			{
				if (!(list($folder, $children) = each($dir_stack[count($dir_stack)-1])))
				{
					array_pop($dir_stack);
					array_pop($folder_stack);
					continue;
				}
				if (is_int($folder))
				{
					$cache_list[] = str_replace(DS, '.', implode('', $folder_stack)) . basename($children, '.cache');
				}
				else
				{
					$dir_stack[] = $children;
					$folder_stack[] =  $folder;
				}
			}
			unset($dir_stack);
			unset($folder_stack);

//print_r($cache_list);

			// 期限切れのキャッシュを削除
			foreach ($cache_list as $identifier)
			{
				try
				{
					\Cache::get($identifier);
				}
				catch (\CacheNotFoundException $e)
				{
				}
			}

		}
		catch(\FileAccessException $e)
		{
		    // 失敗したときの処理
			print_r('>>'.$e->getMessage());
		}

	}

}
/* End of file tasks/connector.php */
