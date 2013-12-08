<h1>ユーザーの設定</h1>
<hr>

<form class="form-horizontal" method="post">
  <fieldset>
    <?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
    <?php list($key_top) = array_keys($form_kind); ?>

    <ul id="configTab" class="nav nav-tabs">
<?php foreach($form_kind as $key => $name): ?>
      <li class="<?php $key_top != $key ?: print('active'); ?>"><a href="#tab_<?php echo $key; ?>" data-toggle="tab"><?php echo $name; ?></a></li>
<?php endforeach; ?>
    </ul>

    <div id="configTabContent" class="tab-content">
<?php foreach($form_kind as $key => $name): ?>
<?php if (isset($form[$key])): ?>
      <div class="tab-pane fade <?php $key_top != $key ?: print('in active'); ?>" id="tab_<?php echo $key; ?>">
<?php echo \View::forge('form', array('form' => $form[$key]))->render(); ?>
      </div>
<?php endif; ?>
    </div>
<?php endforeach; ?>

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
