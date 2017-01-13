<?php    
    // load up your config file
    require_once("../../../resources/global.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
    require_once(CLASSES_PATH . "/events.php");
?>
<script src='<?php echo HTTP_ROOT ?>js/moment.min.js'></script>
<div id="container">
    <div id="content">
        <!-- content -->

        <?php
        $events = new Event($PDO);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!empty($_POST['id'])) {
                $events->deleteEvent($_POST['id'], TRUE);
            }
        }

        $bookings = $events->listEvents(FALSE);
        
        ?>

<table class="table table-hover" id="dataTable">
            <thead>
            <tr>
                <th>Booking Name</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Details</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

    </div>
</div>

<script>

function updateTable(){
    $.ajax(
    {
        type: "GET",
        url: '<?php echo HTTP_ROOT ?>ajax/eventsUser.php',
        data: "{}",
        dataType: "json",
        cache: false,
        success: function (data) {
            $('#dataTable tbody').empty();
            $('#dataTable').append(

                $.map(data, function (item, index) {
                    return '<tr><td>' + item.band + '</td><td>' + moment(item.start, "YYYY-MM-DD h:mm:ss").format('DD/MM/YYYY') + '</td>' +
                        '<td>' + moment(item.start, "YYYY-MM-DD h:mm:ss").format('H:mm') + '</td><td>' + moment(item.end, "YYYY-MM-DD h:mm:ss").format('H:mm') + '</td><td>' + item.details + '</td>'+
                        '<td><button class=\"btn\" onclick=\"confirmDelete(' + item.id + ')\">Delete</button></td>' +
                        '</tr>';
                }).join());
            
        
        }
    });
}

 function confirmDelete(id) {
     $("#delete-confirm")
        $.ajax({ url: '<?php echo HTTP_ROOT ?>ajax/eventChange.php',
                data: {
                    action: 'delete',
                    data: JSON.stringify(id)
                },
                type: 'post',
                success: function(output) {
                            if (output == '1'){
                                updateTable();
                            }
                        }
        });
  }

 updateTable();

</script>

<?php require_once(TEMPLATES_PATH . "/footer.php");?>