<h1>ユーザーの設定</h1>
<hr>

<form class="form-horizontal" method="post">
  <?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
  <div class="control-group<?php echo !empty($username_error_message)?' error':''; ?>">
    <label class="control-label" for="inputUsername">ユーザー名</label>
    <div class="controls">
      <input type="text" required id="inputUsername" placeholder="ユーザー名" name="username" value="<?php echo Input::post('username'); ?>">
<?php if (!empty($username_error_message)): ?>
      <span class="help-inline"><?php echo $username_error_message; ?></span>
<?php endif; ?>
    </div>
  </div>
  <div class="control-group<?php echo !empty($password_error_message)?' error':''; ?>">
    <label class="control-label" for="inputPassword">パスワード</label>
    <div class="controls">
      <input type="password" id="inputPassword" placeholder="パスワード" name="password" value="<?php echo Input::post('password'); ?>">
<?php if (!empty($password_error_message)): ?>
      <span class="help-inline"><?php echo $password_error_message; ?></span>
<?php endif; ?>
    </div>
  </div>
  <div class="control-group<?php echo !empty($password_error_message)?' error':''; ?>">
    <label class="control-label" for="inputPasswordConfirm">パスワード(確認)</label>
    <div class="controls">
      <input type="password" id="inputPasswordConfirm" placeholder="パスワード(確認)" name="password2" value="<?php echo Input::post('password2'); ?>">
<?php if (!empty($password_error_message)): ?>
      <span class="help-inline"><?php echo $password_error_message; ?></span>
<?php endif; ?>
    </div>
  </div>
  <div class="control-group<?php echo !empty($email_error_message)?' error':''; ?>">
    <label class="control-label" for="inputEmail">メールアドレス</label>
    <div class="controls">
      <input type="email" required id="inputEmail" placeholder="メールアドレス" name="email" value="<?php echo Input::post('email'); ?>">
<?php if (!empty($email_error_message)): ?>
      <span class="help-inline"><?php echo $email_error_message; ?></span>
<?php endif; ?>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn btn-primary">保存</button>
    </div>
  </div>
</form>

<?php if (!empty($error_message)): ?>
<div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>
