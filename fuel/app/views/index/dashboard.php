<style type="text/css">
body {
	background-image: url(<?php echo Asset::get_file('archelon-bg.png', 'img'); ?>);
	background-repeat: no-repeat;
	background-position: center 50px;
}
</style>

<h1>登録済みウェブアプリケーション</h1>
<hr>

<?php if (empty($accounts)): ?>
<div class="alert alert-info">
  登録済みウェブアプリケーションは存在しません。
  「<a href="<?php echo Uri::create('account/connect') ?>">アカウント追加</a>」から登録してください。
</div>
<?php else: ?>
<div class="row">
<?php foreach ($accounts as $account): ?>
  <div class="span5">
    <div class="well well-small">
      <ul class="nav nav-pills">
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><h2 style="display: inline;"><?php echo $account['title']; ?></h2> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo Uri::create('account/edit/:id', array('id' => $account['id'])); ?>"
               ><i class="icon-pencil"></i> 編集</a></li>
            <li><a href="<?php echo Uri::create('docs/:name/api/:id', array('name' => $account['connector'], 'id' => $account['id'])); ?>"
               ><i class="icon-question"></i> 使い方(APIドキュメント)</a></li>
            <li class="divider"></li>
            <li><a href="<?php echo Uri::create('account/disconnect/:id', array('id' => $account['id'])); ?>"
               ><i class="icon-trash"></i> 削除</a></li>
          </ul>
        </li>
      </ul>
      <table class="table-condensed">
<?php foreach ($account['description'] as $key => $val): ?>
        <tr>
          <td><?php echo e($key); ?></td>
          <td><?php echo e($val); ?></td>
        </tr>
<?php endforeach; ?>
        <tr>
          <td>API KEY</td>
          <td><code><?php echo e($account['api_key']); ?></code></td>
        </tr>
      </table>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<div id="modal_account_delete" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-header">
    <h3>アカウントの削除</h3>
  </div>
  <div class="modal-body">
    <p></p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-danger"                             >削除</a>
    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">キャンセル</a>
  </div>
</div>
