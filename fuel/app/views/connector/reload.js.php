<ul class="unstyled">
<?php foreach ($messages as $message): ?>
  <li><?php echo $message; ?></li>
<?php endforeach; ?>
</ul>

<script type="text/javascript">
(function(){
    $('#modal_connector_reload .modal-footer button')
        .removeClass('disabled')
        .attr('data-dismiss', 'modal');
    $('#modal_connector_reload')
        .on('hidden', function () {
                window.location.reload(true);
            });
})();
</script>
