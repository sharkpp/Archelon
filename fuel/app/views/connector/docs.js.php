<script type="text/javascript">
    $('form[id^="form_"]')
      .bind('submit', function(){
          $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: $(this).serialize(),
            target: $(this).attr('id').replace('form_', ''),
            headers: { 
              Accept : "application/json; charset=utf-8",
                       "Content-Type": "application/json; charset=utf-8"
            },
            success: function(data, dataType){
              console.log(this);
              console.log(data);
              $('#request_' + this.target)
                .html(this.url);
              $('#results_' + this.target)
                .show();
              $('#status_' + this.target)
                .html(200);
              $('#response_' + this.target)
                .html(data.toString());
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
              $('#request_' + this.target)
                .html(this.url);
              $('#results_' + this.target)
                .show();
              $('#status_' + this.target)
                .html(XMLHttpRequest.status);
              $('#response_' + this.target)
                .html(textStatus);
              console.log(XMLHttpRequest);
              console.log(errorThrown);
              console.log(this);
            },
          });
          return false;
        });
</script>
