<?php foreach ($form as $item): ?>
<?php if (!isset($item['form'])) { continue; } ?>
  <div class="control-group<?php echo !empty($item['error_message'])?' error':''; ?>">
    <label class="control-label" for="form_<?php echo $item['name']; ?>"><?php echo $item['label']; ?></label>
    <div class="controls">
<?php if ('text'     == $item['form']['type'] ||
        'password' == $item['form']['type'] ||
        'hidden'   == $item['form']['type']): ?>
      <?php echo \Form::input($item['name'], $item['value'],
                              array_merge(array('type' => $item['form']['type'], 'class' => 'input-xlarge',
                                                'placeholder' => $item['label']),
                                          $item['required']?array('required'):array())) ?>
<?php elseif ('checkbox' == $item['form']['type']): ?>
      <?php echo \Form::checkbox($item['name'], '1', $item['value']) ?>
<?php elseif ('select' == $item['form']['type']): ?>
      <?php echo \Form::select($item['name'], $item['value'], $item['form']['options']) ?>
<?php endif; ?>
<?php if (!empty($item['error_message'])): ?>
      <span class="help-inline"><?php echo $item['error_message']; ?></span>
<?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
