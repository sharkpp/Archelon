<?php if (isset($save) && $save): ?>
<h1><?php echo $connector_name; ?> のアカウントを編集</h1>
<?php else: ?>
<h1><?php echo $connector_name; ?> のアカウントを追加</h1>
<?php endif; ?>
<hr>

<form class="form-horizontal" method="post">
  <?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
<?php echo \View::forge('form', array('form' => $form))->render(); ?>
  <div class="control-group">
    <div class="controls">
<?php if (isset($save) && $save): ?>
      <button type="submit" class="btn btn-primary">保存</button>
<?php else: ?>
      <button type="submit" class="btn btn-primary">追加</button>
<?php endif; ?>
    </div>
  </div>
</form>

<?php if (!empty($error_message)): ?>
<div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>
