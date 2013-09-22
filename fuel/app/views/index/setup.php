<h1>セットアップ</h1>
<hr>

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
<?php foreach ($form['db'] as $item): ?>
    <div class="control-group<?php echo !empty($item['error_message'])?' error':''; ?>">
<?php if ('checkbox' != $item['form']['type']): ?>
      <label class="control-label" for="form_<?php echo $item['name']; ?>"><?php echo $item['label']; ?></label>
<?php endif; ?>
      <div class="controls">
<?php if ('text'     == $item['form']['type'] ||
          'password' == $item['form']['type'] ||
          'hidden'   == $item['form']['type']): ?>
        <?php echo \Form::input($item['name'], $item['value'],
                                array_merge(array('type' => $item['form']['type'], 'class' => 'input-xlarge',
                                                  'placeholder' => $item['label']),
                                            $item['required']?array('required'):array())) ?>
<?php elseif ('checkbox' == $item['form']['type']): ?>
        <label class="checkbox"><input type="checkbox" name="<?php echo $item['name']; ?>"
               value="1" <?php echo !empty($item['value'])?'checked="checked"':''; ?>> <?php echo $item['label']; ?></label>
<?php elseif ('select' == $item['form']['type']): ?>
        <?php echo \Form::select($item['name'], $item['value'], $item['form']['options']) ?>
<?php endif; ?>
<?php if (!empty($item['error_message'])): ?>
        <span class="help-inline"><?php echo $item['error_message']; ?></span>
<?php endif; ?>
      </div>
    </div>
<?php endforeach; ?>
<!-- 更新ボタン ------------------------------------------------------ -->
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
