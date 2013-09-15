<script type="text/javascript">
    hljs.tabReplace = '    '; //4 spaces
    hljs.initHighlighting();
    $('form[id^="form_"]')
      .bind('submit', function(){
          var target  = $(this).attr('id').replace('form_', '');
          var form_id = '#' + $(this).attr('id');
          var form_action = $(this).attr('action');
          // build form action url
          $(form_id + ' *[data-param-type="path"]') // build url, exclude query parts
            .each(function(){
              form_action = form_action.replace('{'+$(this).attr('name')+'}', $(this).val());
            });
          $.ajax({
            url: form_action,
            type: $(this).attr('method'),
            data: $(form_id + ' *[data-param-type!="path"]').serialize(), // build query, exclude path parts
            dataType: 'json',
            beforeSend: function(XMLHttpRequest){
              // update display
              $('#results_' + target)
                .show();
              $('#results_wait_' + target)
                .show();
              $('#results_response_' + target)
                .hide();
              $('#request_' + target)
                .html(this.url);
            },
            success: function(data, dataType){
console.log(this);
console.log(data);
              json = JSON.stringify(data, undefined, 4);
              $('#status_' + target)
                .html(200);
              $('#response_' + target)
                .addClass('json')
                .html(json ? hljs.highlight('json', json).value : '');
              $('#results_wait_' + target)
                .hide();
              $('#results_response_' + target)
                .show();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
console.log(XMLHttpRequest);
console.log(errorThrown);
console.log(this);
              json = JSON.stringify(XMLHttpRequest.responseJSON, undefined, 4);
              $('#status_' + target)
                .html(XMLHttpRequest.status);
              $('#response_' + target)
                .addClass('json')
                .html(json ? hljs.highlight('json', json).value : '');
              $('#results_wait_' + target)
                .hide();
              $('#results_response_' + target)
                .show();
            },
          });
          return false;
        });
</script>
