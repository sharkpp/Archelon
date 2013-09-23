<h1>セットアップ</h1>
<hr>

<p>アプリケーションの使用する準備が整っていません。</p>
<p>アプリケーションを使用するにはセットアップを行なってください。</p>

<?php if (\Fuel::PRODUCTION != \Fuel::$env): ?>
<div class="alert">
  <strong>警告！</strong> 現在の環境は <?php echo \Fuel::PRODUCTION; ?>(つまり本番環境)ではなく、<strong><?php echo \Fuel::$env; ?></strong> になっています。
</div>
<?php endif; ?>

<form class="form-horizontal" method="post">
  <fieldset>
    <?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
<!-- データベース関連 ------------------------------------------------------ -->
    <legend>データベース関連</legend>
<?php echo \View::forge('form', array('form' => $form['db']))->render(); ?>
<!-- 更新ボタン ------------------------------------------------------------ -->
    <div class="control-group">
      <div class="controls">
        <button type="submit" class="btn btn-primary">保存</button>
      </div>
    </div>
  </fieldset>
</form>

<?php if (!empty($error_message)): ?>
<div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>
