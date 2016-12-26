<?php    
    // load up your config file
    require_once("../resources/config.php");
     
    require_once(TEMPLATES_PATH . "/header.php");
    require_once(CLASSES_PATH . "/events.php");
    require_once(LIBRARY_PATH . "/common.php");
?>
<div id="container">
    <div id="content">
        <!-- content -->

        <?php
        $events = new Event;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!empty($_POST['google_id'])) {
                $events->deleteEvent($_POST['google_id'], TRUE);
            }
        }

        $bookings = $events->listEvents();
        
        ?>

        <table>
            <tr>
                <th>Booking Name</th>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Details</th>
                <th>Delete</th>
            </tr>
            <?php
                foreach ($bookings as $row) {
                    $band = $row['band'];
                    $details = $row['details'];
                    $google_id = $row['google_id'];
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
                    <input type=\"hidden\" name=\"google_id\" value=\"" . $google_id .  "\">
                    <input type=\"submit\" name=\"submit\" value=\"Delete\">  
                    </form>
                    </td>";
                    
                    echo "</tr>";
                }
            ?>
        </table>

    </div>
</div>