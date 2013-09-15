<h1>追加できるアカウント</h1>
<hr>

<div class="row">
<?php foreach ($connectors as $connector): ?>
  <div class="span4">
    <div class="well well-small">
      <h2><?php echo $connector['name']; ?></h2>
      <p><?php echo $connector['description']; ?></p>
      <a class="btn btn-large btn-block"
         href="<?php echo Uri::create('connector/:name/account/connect', array('name' => $connector['connector'])); ?>"
         ><i class="icon-plus"></i> 追加</a>
    </div>
  </div>
<?php endforeach; ?>
</div>

