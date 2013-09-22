<h1>アカウントの削除</h1>
<hr>

<div id="modal-contents">

<p>下記の <?php echo $connector_name ?> アカウントを削除してもよろしいですか？</p>

<form class="form-horizontal" method="post">
  <?php echo \Form::hidden(\Config::get('security.csrf_token_key'), \Security::fetch_token()); ?>
<?php foreach ($description as $key => $val): ?>
  <div class="control-group">
    <label class="control-label"><?php echo e($key); ?></label>
    <div class="controls">
      <?php echo e($val); ?>
    </div>
  </div>
<?php endforeach; ?>
  <div class="control-group">
    <div class="controls">
      <button type="submit" name="submit" value="submit" class="btn btn-danger">削除</button>
      <button type="submit" name="submit" value="cancel" class="btn"           >キャンセル</button>
    </div>
  </div>
</form>

</div>
