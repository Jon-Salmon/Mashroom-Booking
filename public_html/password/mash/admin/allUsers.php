<?php    
    // load up your config file
    require_once("../../../../resources/global.php");
     
    if (!$USER->admin){
        header('Location: ../index.php');
        die();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once(LIBRARY_PATH . "/downloads.php");
        if ($_POST['export'] == 'csv') {
            downloadUserCSV();
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        require_once(LIBRARY_PATH . "/downloads.php");
        if ($_GET['export'] == 'pdf') {
            downloadUserPDF();
        }
    }

    require_once(TEMPLATES_PATH . "/header.php");
?>
<script src='<?php echo OPEN_ROOT ?>js/moment.min.js'></script>
<div id="content" class="row">

    <div class="col-md-3 col-md-push-9">
        <div class="padded-button">
            <button id="create-user" class="form-control btn">Create new user</button>
        </div>
        <div class="padded-button">
            <form action="" method="POST">
                <input type="hidden" name="export" value="csv" />
                <input class="form-control btn" type="submit" value="Export Data" />
            </form>
        </div>
        <div class="padded-button">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" target="_blank" method="GET">
                <input type="hidden" name="export" value="pdf" />
                <input class="form-control btn" type="submit" value="Print List" />
            </form>
        </div>
        <div class="padded-button">
            <button title="Removes all users who are no longer a member of the university." id="clean-users" class="form-control btn">Clean Users</button>
        </div>
    </div>
        
    <div class="col-md-9 col-md-pull-3">
<table class="table table-hover" id="dataTable">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Date Inducted</th>
                <th>Remove</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

    </div>
</div>

<script>
  $( function() {
    var dialog, form,
 
      // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
      emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
      email = $( "#email" ),
      allFields = $( [] ).add( email ),
      tips = $( ".validateTips" );
 
    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "alert alert-danger" );
    }
 
    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }
 
    function addUser() {
      var valid = true;
      allFields.removeClass( "alert alert-danger ui-state-error" );
 
 
      valid = checkRegexp( email, emailRegex, "eg. fred.blogs@durham.ac.uk" );
 
      if ( valid ) {

        $.ajax({ url: '<?php echo HTTP_ROOT . 'ajax/users.php'?>',
                dataType: "json",
                data: {
                    action: 'add',
                    data: JSON.stringify(email.val())
                    },
                type: 'post',
                success: function(output) {
                    if (output[0] == true){
                        form[ 0 ].reset();
                        updateTable();
                        email.focus();
                        updateTips('');
                        allFields.removeClass( "alert alert-danger ui-state-error" );
                        tips.removeClass( "alert alert-danger ui-state-error" );
                    } else {
                        updateTips(output[1]);
                    }
                }
        });

      }
      return valid;
    }
 
    dialog = $( "#dialog-create-user" ).dialog({
      autoOpen: false,
      height: 220,
      width: 400,
      modal: true,
        open: function(){
            jQuery('.ui-widget-overlay').bind('click',function(){
                jQuery('#dialog-create-user').dialog('close');
            })
        },
      buttons: {
        "Add User": addUser,
        Cancel: function() {
          dialog.dialog( "close" );
        }
      },
      close: function() {
        form[ 0 ].reset();
        allFields.removeClass( "alert alert-danger" );
      }
    });
 
    form = dialog.find( "form" ).on( "submit", function( event ) {
      event.preventDefault();
      addUser();
    });
 
    $( "#create-user" ).button().on( "click", function() {
      form[ 0 ].reset();
      dialog.dialog( "open" );
    });

    $( "#clean-users" ).button().on( "click", function() {
        $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/users.php',
                data: {
                    action: 'clean'
                },
                dataType: "json",
                type: 'post',
                success: function(output) {
                            if (output == 1){
                                updateTable();
                            }
                            else {
                                $("#message").html("Oops, something went a bit wrong");
                                $('#delete-confirm').dialog('close');
                                $('#errorDisplay').dialog('open');
                            }
                        }
        });
    });

      $( "#delete-confirm" ).dialog({
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
          "Delete": function() {
              var user = $(this).data('user'); // Get the stored result
                $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/users.php',
                        data: {
                            action: 'delete',
                            data: JSON.stringify(user)
                        },
                        dataType: "json",
                        type: 'post',
                        success: function(output) {
                                    if (output[0] == 1){
                                        updateTable();
                                        $('#delete-confirm').dialog('close');
                                    }
                                    else {
                                        $("#message").html(output[1]);
                                        $('#delete-confirm').dialog('close');
                                        $('#errorDisplay').dialog('open');
                                    }
                                }
                });
            $( this ).dialog( "close" );
          },
          Cancel: function() {
            $( this ).dialog( "close" );
          }
        }
      });
    
      $( document ).tooltip();
    
  } );
  

function updateTable(){
    $.ajax(
    {
        type: "post",
        url: '<?php echo HTTP_ROOT ?>ajax/users.php',
        data: {action: 'view'},
        dataType: "json",
        success: function (data) {
            $('#dataTable tbody').empty();
            $('#dataTable').append(

                $.map(data, function (item, index) {
                    return '<tr><td>' + item.name + '</td><td>' + item.email + '</td><td>'
                        + moment(item.created, "YYYY-MM-DD h:mm:ss").format('DD/MM/YYYY') + '</td>' +
                        '<td><button class=\"btn\" onclick=\"confirmDelete(\'' + item.user + '\')\">Remove</button></td>' +
                        '</tr>';
                }).join());
            
        
        }
    });
}

 function confirmDelete(user) {
     $("#delete-confirm")
        .data('user', user)  // The important part .data() method
        .dialog('open');
  }

 function deleteUser(user) {
    $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/users.php',
            data: {
                action: 'delete',
                data: JSON.stringify(user)
            },
            dataType: "json",
            type: 'post',
            success: function(output) {
                        if (output[0] == 1){
                            updateTable();
                        }
                        else {
                            $("#message").html(output[1]);
                            $('#errorDisplay').dialog('open');
                        }
                    }
    });
  }

 updateTable();

</script>

<div id="delete-confirm" class="dialog" title="Delete event as admin">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><span id="induct-text">Are you sure you want to delete this user, they will no longer be able to use this service or the MASH room.</span></p>
</div>

<div id="dialog-create-user" class="dialog" title="Create new user">
  <form>
    <fieldset>
      <label for="email">Durham email: </label>
      <input type="text" name="email" class="form-control" id="email" value="" class="text ui-widget-content ui-corner-all">
 
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
  <p class="alert validateTips"></p>
</div>

<?php require_once(TEMPLATES_PATH . "/footer.php");?>