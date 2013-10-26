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
    <div class="accordion" id="config">
<?php foreach(array(
          'db'       => 'データベース関連',
          'auth'     => '認証関連',
          'ldapauth' => 'Ldap認証関連',
          ) as $key => $name): ?>
      <div class="accordion-group">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse" data-parent="#config" href="#collapse_<?php echo $key; ?>" ><?php echo $name; ?></a>
        </div>
        <div id="collapse_<?php echo $key; ?>" class="accordion-body collapse <?php 'ldapauth' == $key ?: print('in'); ?>">
          <div class="accordion-inner">
<?php echo \View::forge('form', array('form' => $form[$key]))->render(); ?>
          </div>
        </div>
      </div>
<?php endforeach; ?>
<!-- 更新ボタン ------------------------------------------------------------ -->
      <p> </p>
      <div class="control-group">
        <div class="controls">
          <button type="submit" class="btn btn-primary">保存</button>
        </div>
      </div>
    </div>
  </fieldset>
</form>

<?php if (!empty($error_message)): ?>
<div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>
