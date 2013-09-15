<script type="text/javascript">
    hljs.tabReplace = '    '; //4 spaces
    hljs.initHighlighting();
    $('form[id^="form_"]')
      .bind('submit', function(){
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
            target: $(this).attr('id').replace('form_', ''),
            dataType: 'json',
            success: function(data, dataType){
console.log(this);
console.log(data);
              json = JSON.stringify(data, undefined, 4);
              $('#request_' + this.target)
                .html(this.url);
              $('#results_' + this.target)
                .show();
              $('#status_' + this.target)
                .html(200);
              $('#response_' + this.target)
                .addClass('json')
                .html('*')
                .html(json ? hljs.highlight('json', json).value : '');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
console.log(XMLHttpRequest);
console.log(errorThrown);
console.log(this);
              json = JSON.stringify(XMLHttpRequest.responseJSON, undefined, 4);
              $('#request_' + this.target)
                .html(this.url);
              $('#results_' + this.target)
                .show();
              $('#status_' + this.target)
                .html(XMLHttpRequest.status);
              $('#response_' + this.target)
                .addClass('json')
                .html('*')
                .html(json ? hljs.highlight('json', json).value : '');
            },
          });
          return false;
        });
</script>
