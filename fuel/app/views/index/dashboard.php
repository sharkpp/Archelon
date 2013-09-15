<h1>登録済みウェブアプリケーション</h1>
<hr>

<div class="row">
<?php foreach ($accounts as $account): ?>
  <div class="span4">
    <div class="well well-small">
      <ul class="nav nav-pills">
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#"><h2 style="display: inline;"><?php echo $account['title']; ?></h2> <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo Uri::create('connector/:name/account/edit/:id', array('name' => $account['connector'], 'id' => $account['id'])); ?>"
               ><i class="icon-pencil"></i> 編集</a></li>
            <li><a href="<?php echo Uri::create('docs/:name/api/:id', array('name' => $account['connector'], 'id' => $account['id'])); ?>"
               ><i class="icon-question"></i> 使い方(APIドキュメント)</a></li>
            <li class="divider"></li>
            <li><a href="<?php echo Uri::create('connector/:name/account/delete', array('name' => $account['connector'])); ?>"
               ><i class="icon-trash"></i> 削除</a></li>
          </ul>
        </li>
      </ul>
      <table class="table-condensed">
<?php foreach ($account['description'] as $key => $val): ?>
        <tr>
          <td><?php echo e($key); ?></td>
          <td><?php echo e($val); ?></td>
        </tr>
<?php endforeach; ?>
        <tr>
          <td>API KEY</td>
          <td><code><?php echo e($account['api_key']); ?></code></td>
        </tr>
      </table>
    </div>
  </div>
<?php endforeach; ?>
</div>