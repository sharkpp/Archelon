<?php if (isset($save) && $save): ?>
<h1><?php echo $connector_name; ?> のアカウントを編集</h1>
<?php else: ?>
<h1><?php echo $connector_name; ?> のアカウントを追加</h1>
<?php endif; ?>
<hr>

<form class="form-horizontal" method="post">
  <?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
<?php foreach ($form as $item): ?>
  <div class="control-group<?php echo !empty($item['error_message'])?' error':''; ?>">
<?php if ('checkbox' != $item['form']['type']): ?>
    <label class="control-label" for="form_<?php echo $item['name']; ?>"><?php echo $item['label']; ?></label>
<?php endif; ?>
    <div class="controls">
<?php if ('text' == $item['form']['type']): ?>
      <input type="text"     class="input-xlarge" <?php echo $item['required']?'required':''; ?>
             id="form_<?php echo $item['name']; ?>" placeholder="<?php echo $item['label']; ?>"
             name="<?php echo $item['name']; ?>" value="<?php echo $item['value']; ?>">
<?php elseif ('password' == $item['form']['type']): ?>
      <input type="password" class="input-xlarge" <?php echo $item['required']?'required':''; ?>
             id="form_<?php echo $item['name']; ?>" placeholder="<?php echo $item['label']; ?>"
             name="<?php echo $item['name']; ?>" value="<?php echo $item['value']; ?>">
<?php elseif ('checkbox' == $item['form']['type']): ?>
      <label class="checkbox"><input type="checkbox" name="<?php echo $item['name']; ?>"
             value="1" <?php echo !empty($item['value'])?'checked="checked"':''; ?>> <?php echo $item['label']; ?></label>
<?php endif; ?>
<?php if (!empty($item['error_message'])): ?>
      <span class="help-inline"><?php echo $item['error_message']; ?></span>
<?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
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
