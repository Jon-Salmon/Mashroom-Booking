<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
    require_once(CLASSES_PATH . "/events.php");
?>
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

<table class="table table-hover">
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
            <?php
                foreach ($bookings as $row) {
                    $band = $row['band'];
                    $details = $row['details'];
                    $id = $row['id'];
                    $start = new DateTime($row['start']);
                    $end = new DateTime($row['end']);

                    echo '<tr>';
                    echo '<td>' . $band . '</td>';
                    echo '<td>' . $start->format("d/m/Y") . '</td>';
                    echo '<td>' . $start->format("H:i") . '</td>';
                    echo '<td>' . $end->format("H:i") . '</td>';
                    echo '<td>' . $details . '</td>';

                    echo "
                    <td>
                    <form method=\"post\" action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]) . "\">  
                    <input type=\"hidden\" name=\"id\" value=\"" . $id .  "\">
                    <input type=\"submit\" class=\"btn\" name=\"submit\" value=\"Delete\">  
                    </form>
                    </td>";
                    
                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>

    </div>
</div>
<?php require_once(TEMPLATES_PATH . "/footer.php");?>