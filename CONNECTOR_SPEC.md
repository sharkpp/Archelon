# コネクタ仕様

この文書では、Archelon で使用できるコネクタの仕様を記載しています。

## ディレクトリレイアウトと必須ファイル

コネクタ名は例として ```forum``` をサンプルにしています。

      forum/
      + classes/
      | + controller/
      | + model/
      | | + forum/
      | + view/
      | + connector.php ※ \Connector から派生すること
      + config/
      + lang/
      + migrations/
      + tasks/
      + views/

## 実装の制限

* URLは ```/api/コネクタ名/*``` で ```classes/controller/*``` がマッピングされています。。
* ```classes/connector.php``` は \Connector から派生し必須となるインターフェースを実装すること。
* ORMモデルは衝突回避のため ```Model_Forum_User``` つまり、コネクタ名をモデルの名前に含むこと。

## Connector インターフェース

### コネクタ情報の取得

このメソッドの結果を元に各ページのコネクタ名や説明を表示します。

#### インターフェース

      public function get_connector_spec();

#### 戻り値

        return array(
                    'screen_name' => 'コネクタの表示名',
                    'description' => 'コネクタの説明',
                );

### API情報の取得

このメソッドの結果を元にドキュメントページを構築します。

#### インターフェース

      public function get_api_spec();

#### 戻り値

        return array(
                'user' => array( // API名
                    'method' => 'GET', // 要求種別(GET|POST)
                    'path' => 'v1/user/{userId}', // パス(※コネクタ名などは除く)
                    'title' => 'ユーザーの取得', // タイトル
                    'description' => 'ユーザーの取得', // 説明
                    'parameters' => array( // 要求パラメータ
                        'userId' => array( // キー名(param_type='path'の場合 '{'+キー名+'}' を置換して問い合わせます)
                            'description' => 'ユーザーID', // 説明
                            'value'       => '', // デフォルト値
                            'required'    => false, // 必須項目か？
                            'param_type'  => 'path', // 種別(query|path)
                            'data_type'   => 'integer', // データ種別(string|integer|API_KEY)
                        ),
                        'key' => array(
                            'description' => 'APIキー',
                            'value'       => 'デフォルト値',
                            'required'    => true,
                            'param_type'  => 'query',
                            'data_type'   => 'API_KEY',
                        ),
                    ),
                    'status_codes' => array( // 要求結果で戻りうるHTTPステータスコードと意味
                        200 => array( 'reason' => '成功' ),
                        401 => array( 'reason' => '認証に失敗' ),
                        403 => array( 'reason' => 'アクセス権限がない' ),
                        404 => array( 'reason' => 'ユーザーが見つからない' ),
                    ),
                ),
                // 以下、API分繰り返す
            );

### コネクタ設定フォームの取得

このメソッドの結果を元にコネクタ設定ページを構築します。
[Creating models - Orm Package - FuelPHP Documentation](http://fuelphp.com/docs/packages/orm/creating_models.html#/propperties) を参考

#### インターフェース

      public function get_config_form();

#### 戻り値

        return array(
                'user' => array(
                    'label' => 'ユーザー名', // 画面に表示するラベル
                    'validation' => array('required', 'min_length' => array(3), 'max_length' => array(20)),
                    'form' => array('type' => 'text'),
                    'default' => 'New article', // ※すでに登録済みの場合は
                ),
                '@script' => "alert('test')", // 追加でスクリプトが必要であれば
            );

### 登録情報の更新

設定を更新します。

#### インターフェース

      public function save_config($validation);

#### 戻り値

* true  成功
* false 失敗

### 登録情報フォームの取得

このメソッドの結果を元にアカウント追加ページを構築します。
更新時に新規作成時にない項目を追加として出してもOK。
[Creating models - Orm Package - FuelPHP Documentation](http://fuelphp.com/docs/packages/orm/creating_models.html#/propperties) を参考

#### インターフェース

      public function get_account_form($account_id = null);

#### 戻り値

        return array(
                'user' => array(
                    'label' => 'ユーザー名', // 画面に表示するラベル
                    'validation' => array('required', 'min_length' => array(3), 'max_length' => array(20)),
                    'form' => array('type' => 'text'),
                    'default' => 'New article', // ※すでに登録済みの場合は
                ),
                '@script' => "alert('test')", // 追加でスクリプトが必要であれば
            );

### 登録情報の更新

登録情報を新規に追加もしくは更新します。

#### インターフェース

      public function save_account($validation, $account_id = null);

#### 戻り値

* true  成功
* false 失敗

### 登録情報の削除

登録情報を削除します。
```\Model_Account``` も削除が必要です。

#### インターフェース

      public function drop_account($account_id);

#### 戻り値

* true  成功
* false 失敗

