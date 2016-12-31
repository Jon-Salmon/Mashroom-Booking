<?php
    // load up your config file
    require_once("../../resources/config.php");
     
    if (!$USER->admin){
        header('Location: ../index.php');
        die();
    }
    
    require_once(TEMPLATES_PATH . "/header.php");

?>

    <div id="content">
        <!-- content -->

        <?php
        $sucess = TRUE;
        $error = "";
        

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!empty($_POST['action'])) {
                if ($_POST['action'] == 'delete' && !empty($_POST['id'])){
                    list($sucess, $error) = $ADMINS->delete(intval($_POST['id']));
                }
                if ($_POST['action'] == 'change' && !empty($_POST['pos']) && !empty($_POST['id'])){
                    if ($_POST['id'] != 'default'){
                        list($sucess, $error) = $ADMINS->change($_POST['pos'], intval($_POST['id']));
                    }
                }
            }
        }

        $admins = $ADMINS->getAll();
        
        ?>
        
        <button id="create-user">Create new user</button>
        
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Position</th>
                <th>Name</th>
                <th>Change</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $list = ['mash', 'tech', 'web'];
                foreach ($list as $row){
                    $position = $ADMINS->{$row}->title;
                    $name = $ADMINS->{$row}->name;
                    $id = $ADMINS->{$row}->id;

                    echo '<tr>';
                    echo '<td>' . $position . '</td>';
                    echo '<td>' . $name . '</td>';

                    echo "
                    <td>
                    <form method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">  
                    <input type=\"hidden\" name=\"pos\" value=\"" . $row .  "\">
                    <input type=\"hidden\" name=\"action\" value=\"change\">
                    <select name=\"id\">
                        <option value=\"default\">Update to...</option>";
                    foreach ($admins as $item){
                        if ($item['id'] != $id){
                            echo "<option value=" . $item['id'] . ">" . $item['name'] . "</option>";
                        }
                    }
                    echo "</select>
                    <input type=\"submit\" name=\"submit\" value=\"Change\">  
                    </form>
                    </td>";
                    
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>

        <table class="table table-hover">
            <caption>Admins</caption>
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
                foreach ($admins as $row) {
                    $name = $row['name'];
                    $email = $row['email'];
                    $id = $row['id'];

                    echo '<tr>';
                    echo '<td>' . $name . '</td>';
                    echo '<td>' . $email . '</td>';

                    echo "
                    <td>
                    <form method=\"post\" iid=\"delete\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">  
                    <input type=\"hidden\" name=\"id\" value=\"" . $id .  "\">
                    <input type=\"hidden\" name=\"action\" value=\"delete\">
                    <input type=\"submit\" name=\"submit\" value=\"Delete\">  
                    </form>
                    </td>";
                    
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>

    </div>


<?php if(!$sucess) {
    echo <<< END
    <script>
    $( function() {
      $( "#dialog-errors" ).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        autoOpen: true,
        open: function(){
          jQuery('.ui-widget-overlay').bind('click',function(){
              jQuery('#dialog-errors').dialog('close');
          })
        }
      });
    });
    </script>

    <div id="dialog-errors" class="dialog" title="An error occured">
END;
      echo "<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:12px 12px 20px 0;\"></span><span id=\"induct-text\">" . $error . "</p>
    </div>";
    };
?>
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
        .addClass( "ui-state-error" );
      setTimeout(function() {
        tips.removeClass( "ui-state-error", 1500 );
      }, 500 );
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
      allFields.removeClass( "ui-state-error" );
 
 
      valid = checkRegexp( email, emailRegex, "eg. fred.blogs@durham.ac.uk" );
 
      if ( valid ) {

        $.ajax({ url: '/dev/public_html/ajax/adminAdd.php',
                dataType: "json",
                data: {action: JSON.stringify(email.val())},
                type: 'post',
                success: function(output) {
                    if (output[0] == true){
                        dialog.dialog( "close" );
                        location.reload(true);
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
        allFields.removeClass( "ui-state-error" );
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
  } );
  </script>

<div id="dialog-create-user" class="dialog" title="Create new user">
  <form>
    <fieldset>
      <label for="email">Durham email: </label>
      <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all">
 
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
  <p class="validateTips"></p>
</div>
 

<?php require_once(TEMPLATES_PATH . "/footer.php");?>