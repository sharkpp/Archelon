<?php

namespace Fuel\Tasks;

class Connector
{

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r connector
	 *
	 * @return string
	 */
	public function run($args = NULL)
	{
		echo "\n===========================================";
		echo "\nRunning DEFAULT task [Connector:Run]";
		echo "\n-------------------------------------------\n\n";

		/***************************
		 Put in TASK DETAILS HERE
		 **************************/
	}

	/**
	 * This method gets ran when a valid method name is not used in the command.
	 *
	 * Usage (from command line):
	 *
	 * php oil r connector:reload
	 *
	 * @return string
	 */
	public function reload()
	{
		$del_connectors = array();
		$new_connectors = array();

		$search_connector_name
			= function($needle, array $haystack) {
					foreach ($haystack as $key => $val) {
						if ($needle == $val['name']) {
							return $key;
						}
					}
					return false;
				};

		// データベースからコネクタ一覧を列挙
		$q = \Model_Connector::query()
				->order_by('name', 'asc');
		$connectors = $q->get();
		$del_connectors = array_map(function($connector) {
				return array('id' => $connector->id, 'name' => $connector->name);
			}, $connectors);

		// モジュールフォルダからコネクタっぽいフォルダを列挙
		$contents = \File::read_dir(APPPATH.DS.'modules', 1);

		foreach ($contents as $name => $content)
		{
			if (!is_int($name) && // ファイルを除く
				file_exists(implode(DS, array(APPPATH,'modules',$name,'classes','connector.php')))) // 規定のインターフェースを持っているか？
			{
				$name = trim($name, DS);
				// モジュールを読み込んでマイグレーション
				\Migrate::latest($name, 'module');
				// すでに存在していれば削除対象から除外
				$connector_id = null;
				if (false !== ($key = $search_connector_name($name, $del_connectors)))
				{
					$connector_id = $del_connectors[$key]['id'];
					unset($del_connectors[$key]);
					$update = true;
				}

				\Module::load($name);
				$connector_class = \Inflector::words_to_upper($name).'\\Connector';
				$connector = new $connector_class;
				$connector_spec = $connector->get_connector_spec();
				// 追加対象を更新
				$new_connectors[] = array(
										'id'          => $connector_id,
										'name'        => $name,
										'screen_name' => \Arr::get($connector_spec, 'screen_name', $name),
										'description' => \Arr::get($connector_spec, 'description', ''),
									);
			}
		}

		$messages = array();

		if (!empty($del_connectors) ||
			!empty($new_connectors))
		{
			try
			{
				\DB::start_transaction();

				// 既存のコネクタがなくなっていたらデータベース上でも削除
				foreach ($del_connectors as $name)
				{
					$q = \Model_Connector::find('all', array(
								'where' => array(array('name', $name)),
							));
					$messages[] = sprintf('%s が削除されました。', $name);
					$q->delete();
				}
				// コネクタ一覧データベースに追加
				foreach ($new_connectors as $info)
				{
					$connector = is_null($info['id']) ? new \Model_Connector()
					                                  : \Model_Connector::find($info['id']);
					$connector->enable      = 1;
					$connector->name        = $info['name'];
					$connector->screen_name = $info['screen_name'];
					$connector->description = $info['description'];
					$connector->save();
					$messages[] = sprintf(is_null($info['id']) ? '%s が追加されました。' : '%s が更新されました。', \Security::strip_tags($info['screen_name']));
				}

				\DB::commit_transaction();
			}
			catch (\Exception $e)
			{
				\DB::rollback_transaction();
			}
		}
		else
		{
			$messages[] = sprintf('追加もしくは削除されたコネクタはありません。');
		}

		return implode("\r\n", $messages);
	}

}
/* End of file tasks/connector.php */
