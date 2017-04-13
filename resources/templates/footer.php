
</div>
    <script>
    $( function() {
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

      $("#errorDisplay").dialog({
          modal: true,
          autoOpen: false,
          title: "Error",
          open: function(){
          jQuery('.ui-widget-overlay').bind('click',function(){
              jQuery('#errorDisplay').dialog('close');
              })
          }
          });
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

    <div id="errorDisplay" class="display" title="Error" style="display:none;">
        <span id="message"></span><br><br>
    </div>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo OPEN_ROOT ?>js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
