<h1>API仕様＆サンプル</h1>
<hr>

<div class="accordion" id="api_spec">
<?php foreach ($specs as $name => $info): ?>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#api_spec" href="#collapse_<?php echo $name; ?>">
<?php switch (strtolower($info['method'])): ?>
<?php case 'get': ?><span class="label label-info">GET</span><?php break; ?>
<?php case 'post': ?><span class="label label-success">POST</span><?php break; ?>
<?php endswitch; ?>
        <span><?php echo sprintf('/api/%s/%s', $connector, $info['path']); ?></span>
        <span class="pull-right"><?php echo $info['title']; ?></span>
      </a>
    </div>
    <div id="collapse_<?php echo $name; ?>" class="accordion-body collapse">
      <div class="accordion-inner">
        <p><?php echo $info['description']; ?></p>

        <h3>パラメータ</h3>
        <form id="form_<?php echo $name; ?>"
              method="<?php echo strtolower($info['method']); ?>"
              action="<?php echo Uri::create('/api/:connector/:path', array('connector' => $connector, 'path' => $info['path'])); ?>">
          <table class="table table-striped table-condensed">
            <tr>
              <th>パラメータ</th>
              <th>値</th>
              <th>説明</th>
              <th>パラメータ種別</th>
              <th>データ種別</th>
            </tr>
<?php foreach ($info['parameters'] as $param_name => $param_info): ?>
            <tr>
              <td><?php echo $param_name; ?></td>
              <td><input name="<?php echo $param_name; ?>" value="<?php echo $param_info['value']; ?>"></td>
              <td><?php echo $param_info['description']; ?></td>
              <td><?php echo $param_info['param_type']; ?></td>
              <td><?php echo $param_info['data_type']; ?></td>
            </tr>
<?php endforeach; ?>
          </table>
          <button type="submit" class="btn">送信</button>
        </form>

        <h3>ステータスコード</h3>
        <table class="table table-striped table-condensed">
          <tr>
            <th>値</th>
            <th>要因</th>
          </tr>
<?php foreach ($info['status_codes'] as $status_name => $status_info): ?>
          <tr>
            <td><?php echo $status_name; ?></td>
            <td><?php echo $status_info['reason']; ?></td>
          </tr>
<?php endforeach; ?>
        </table>

        <div id="results_<?php echo $name; ?>" style="display:none;">
          <h3>結果</h3>
          <h4>リクエストURL</h4>
          <p><code id="request_<?php echo $name; ?>"></code></p>
          <h4>レスポンス</h4>
          <p><code id="response_<?php echo $name; ?>"></code></p>
          <h4>ステータスコード</h4>
          <p><code id="status_<?php echo $name; ?>"></code></p>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
