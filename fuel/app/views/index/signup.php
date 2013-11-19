<h1>サインアップ</h1>
<hr>

<form class="form-horizontal" method="post">
  <?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
<?php echo \View::forge('form', array('form' => $form))->render(); ?>
  <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn btn-primary">サインアップ</button>
    </div>
  </div>
</form>

<?php if (!empty($error_message)): ?>
<div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>
