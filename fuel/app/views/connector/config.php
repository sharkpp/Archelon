<h1>コネクタの管理</h1>
<hr>

<div class="row">
<?php foreach ($connectors as $connector): ?>
  <div class="span5">
    <div class="well well-small">
      <h2><?php echo $connector['name']; ?></h2>
      <p><?php echo $connector['description']; ?></p>
      <a class="btn"
         href="<?php echo Uri::create('connector/:name/admin/config', array('name' => $connector['connector'])); ?>"
         ><i class="icon-cog"></i> 設定</a>
    </div>
  </div>
<?php endforeach; ?>
</div>

