<script type="text/javascript">
  // それぞれのアイテムの削除メニューに関連付け
  $('.well a[href*="account/disconnect"]')
    .on('click', function(){
      // ドロップダウンメニューをクローズ
      $(this).parent().parent().prev().dropdown('toggle');
      //
      $('#modal_account_delete .modal-footer .btn:nth-child(1)')
        .attr('data-href', $(this).attr('href'))
        .on('click', function(){
            $('#modal_account_delete .btn')
              .addClass('disabled');
            $.post($(this).attr('data-href'),
                   $('#modal_account_delete form').serialize() + '&submit=submit',
                   function(data, textStatus, jqXHR){
                       window.location.reload(true);
                     });
          });
      // ダイアログ表示準備
      $('#modal_account_delete')
        .attr('data-href', $(this).attr('href'))
        .on('show', function () {
            $('#modal_account_delete .btn')
              .addClass('disabled');
            // 内容を読み込み
            $('#modal_account_delete .modal-body p')
              .load($(this).attr('data-href') + ' .container #modal-contents',
                    function(responseText, textStatus, XMLHttpRequest){
                        $('#modal_account_delete .control-group:last')
                          .hide();
                        $('#modal_account_delete .btn')
                          .removeClass('disabled');
                      });
          })
        .on('hide', function () {
            $('#modal_account_delete .modal-body p')
              .html('');
          })
        .modal('show');
      return false;
    });
</script>
