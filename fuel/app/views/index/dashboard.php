<h1>登録済みウェブアプリケーション</h1>
<hr>

<div class="row">
<?php foreach ($accounts as $account): ?>
  <div class="span4">
    <div class="well well-small">
      <ul class="nav nav-pills">
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><h2 style="display: inline;"><?php echo $account->connector->screen_name; ?></h2> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo Uri::create('connector/:name/account/edit/:id', array('name' => $account->connector->name, 'id' => $account->id)); ?>"
               ><i class="icon-pencil"></i> 編集</a></li>
            <li><a href="<?php echo Uri::create('docs/:name/api/:id', array('name' => $account->connector->name, 'id' => $account->id)); ?>"
               ><i class="icon-question"></i> 使い方(APIドキュメント)</a></li>
            <li class="divider"></li>
            <li><a href="<?php echo Uri::create('connector/:name/account/delete', array('name' => $account->connector->name)); ?>"
               ><i class="icon-trash"></i> 削除</a></li>
          </ul>
        </li>
      </ul>
      <p><?php echo e($account->description); ?></p>
    </div>
  </div>
<?php endforeach; ?>
</div>
