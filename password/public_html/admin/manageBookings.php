<?php
    // load up your config file
    require_once("../../resources/config.php");
     
    if (!$USER->admin){
        header('Location: ../index.php');
        die();
    }
    
    require_once(CLASSES_PATH . "/events.php");
    require_once(TEMPLATES_PATH . "/header.php");
?>

    <div id="content">
        <!-- content -->

        <?php
        $events = new Event($PDO);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!empty($_POST['id'])) {
                $events->deleteEvent($_POST['id'], FALSE, TRUE);
            }
        }

        $bookings = $events->listEvents(TRUE);
        
        ?>

        <table class="table table-hover">
            <thead>
            <tr>
                <th>Booking Name</th>
                <th>Booked By</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Details</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
                foreach ($bookings as $row) {
                    $band = $row['band'];
                    $details = $row['details'];
                    $id = $row['id'];
                    $name = $row['name'];
                    $start = new DateTime($row['start']);
                    $end = new DateTime($row['end']);

                    echo '<tr>';
                    echo '<td>' . $band . '</td>';
                    echo '<td>' . $name . '</td>';
                    echo '<td>' . $start->format("d/m/Y") . '</td>';
                    echo '<td>' . $start->format("H:i") . '</td>';
                    echo '<td>' . $end->format("H:i") . '</td>';
                    echo '<td>' . $details . '</td>';

                    /*echo "
                    <td>
                    <form method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">  
                    <input type=\"hidden\" name=\"id\" value=\"" . $id .  "\">
                    <input type=\"submit\" name=\"submit\" class=\"btn\" value=\"Delete\">  
                    </form>
                    </td>";*/

                    echo "
                    <td>
                    <button class=\"btn\" onclick=\"confirmDelete(". $id . ")\">Delete</button>
                    </td>";
                    
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>

    </div>

<script>

 function confirmDelete(id) {
     $("#delete-confirm")
        .data('id', id)  // The important part .data() method
        .dialog('open');
  }

  
    $( function() {
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
              var id = $(this).data('id'); // Get the stored result
                $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChangeAdmin.php',
                        dataType: "json",
                        data: {
                            action: 'delete',
                            data: JSON.stringify(id)
                        },
                        type: 'post',
                        success: function(output) {
                            location.reload();
                        }
                });
            $( this ).dialog( "close" );
          },
          Cancel: function() {
            $( this ).dialog( "close" );
          }
        }
      });

    });
</script>


<div id="delete-confirm" class="dialog" title="Delete event as admin">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><span id="induct-text">Note: Deleting this event will notify the event's creator of the cancellation by email.</span></p>
</div>


<?php require_once(TEMPLATES_PATH . "/footer.php");?>