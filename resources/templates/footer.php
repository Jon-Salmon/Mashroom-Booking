
</div>
    <script>
    $( function() {
      $( "#induct-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        autoOpen: false,
        open: function(){
          jQuery('.ui-widget-overlay').bind('click',function(){
              jQuery('#induct-confirm').dialog('close');
          })
        },
        buttons: {
          "Register": function() {
              $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/induct.php',
                      data: {induct: JSON.stringify('true')},
                      type: 'post',
                      success: function(output) {
                                  var textSpan = document.getElementById("induct-text");
                                  textSpan.innerHTML = output;
                                  $('#induct-confirm').dialog("option", "buttons", {
                                    Close: function() {
                                      $( this ).dialog( "close" );
                                    }
                                  });
                      }
              });
          },
          Close: function() {
            $( this ).dialog( "close" );
          }
        }
      });

      $( "#support-dialog" ).dialog({
        resizable: false,
        height: "auto",
        modal: true,
        autoOpen: false,
        open: function(){
          jQuery('.ui-widget-overlay').bind('click',function(){
              jQuery('#support-dialog').dialog('close');
          })
        }
      });
    });
    
    $("#bookInduction").click(function(e) {
        e.preventDefault();
        if ($('#navbar').hasClass("in") == true){
          $('.navbar-toggle').click();
        }
        $( "#induct-confirm" ).dialog( "open" );
    });

    $("#support").click(function(e) {
        e.preventDefault();
        if ($('#navbar').hasClass("in") == true){
          $('.navbar-toggle').click();
        }
        $( "#support-dialog" ).dialog( "open" );
    });
    
    
    $("#newBooking").click(function(e) {
        e.preventDefault();
        if (!(window.location.pathname == '<?php echo HTTP_ROOT . "index.php"?>')){
            var form = $('<form></form>');

            form.attr("method", "post");
            form.attr("action", '<?php echo HTTP_ROOT . "index.php"; ?>');

            var field = $('<input></input>');

            field.attr("type", "hidden");
            field.attr("name", "newBooking");
            field.attr("value", "1");

            form.append(field);

            $(document.body).append(form);
            form.submit();
        }
    });
          
    </script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo HTTP_ROOT ?>js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>