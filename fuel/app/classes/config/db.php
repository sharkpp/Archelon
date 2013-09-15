<?php

//namespace Fuel\Core;

/**
 * A base Config File class for File based configs.
 */
class Config_Db implements \Config_Interface
{
	protected static $table_name;

	protected $file;

	protected $cache_name;

	protected $vars = array();

	public static function _init()
	{
		self::$table_name = \Config::get('config_table', 'configs');
	}

	/**
	 * Sets up the file to be parsed and variables
	 *
	 * @param   string  $file  Config file name
	 * @param   array   $vars  Variables to parse in the file
	 * @return  void
	 */
	public function __construct($file = null, $vars = array())
	{
		$this->file = $file;
		$this->cache_name = 'db.config.' . md5($this->file);

		\Cache::delete($this->cache_name);
	}

	/**
	 * Loads the config file(s).
	 *
	 * @param   bool  $overwrite  Whether to overwrite existing values
	 * @return  array  the config array
	 */
	public function load($overwrite = false, $cache = true)
	{
		$config = array();

		if (\DBUtil::table_exists(self::$table_name))
		{
			$query = \DB::select()
						->from(self::$table_name)
						->where('file', $this->file);
			$result = $cache ? $query->cached(3600, $this->cache_name, false)->execute()
			                 : $query->execute();
			if (count($result))
			{
				$config = unserialize($result->get('config'));
			}
		}

		return $config;
	}

	/**
	 * Gets the default group name.
	 *
	 * @return  string
	 */
	public function group()
	{
		return $this->file;
	}

	/**
	 * Formats the output and saved it to disc.
	 *
	 * @param   $contents  $contents    config array to save
	 * @return  bool       \File::update result
	 */
	public function save($contents)
	{
		try
		{
			\DB::start_transaction();

			$query = \DB::select()
						->from(self::$table_name)
						->where('file', $this->file);
			$result = $query->execute();

			if (count($result))
			{
				$result = \DB::update(self::$table_name)
							->set(array(
									'config' => serialize($contents),
								))
							->where('file', $this->file)
							->execute();
			}
			else
			{
				$result = \DB::insert(self::$table_name)
							->set(array(
									'file'   => $this->file,
									'config' => serialize($contents),
								))
							->execute();
			}

			\DB::commit_transaction();

			\Cache::delete($this->cache_name);

			return true;
		}
		catch (Exception $e)
		{
			\DB::rollback_transaction();

			return false;
		}
	}
}
