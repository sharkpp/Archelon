<h1>追加できるアカウント</h1>
<hr>

<?php if (empty($connectors)): ?>
<div class="alert alert-info">
  追加できるアカウントは存在しません。
<?php if (100 == \Auth::get_groups()[0][1]): ?>
  コネクタを追加するか「<a href="<?php echo Uri::create('connector/reload') ?>">コネクタの再読み込み</a>」から更新してください。
<?php else: ?>
  管理者にコネクタの追加をするように連絡してください。
<?php endif; ?>
</div>
<?php else: ?>
<div class="row">
<?php foreach ($connectors as $connector): ?>
  <div class="span4">
    <div class="well well-small">
      <h2><?php echo $connector['name']; ?></h2>
      <p><?php echo $connector['description']; ?></p>
      <a class="btn btn-large btn-block"
         href="<?php echo Uri::create('account/connect/:id', array('id' => $connector['id'])); ?>"
         ><i class="icon-plus"></i> 追加</a>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
