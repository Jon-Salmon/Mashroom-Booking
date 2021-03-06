<?php
    // load up your config file
    require_once "../../../../resources/global.php";

if (!$USER->admin) {
    header('Location: ../index.php');
    die();
}

    require_once TEMPLATES_PATH . "/header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['checkbox'])) {
        $in_array = $_POST['checkbox'];
        $in  = str_repeat('?,', count($in_array) - 1) . '?';
        $sql = "DELETE FROM induction_requests WHERE id IN ($in)";
        $stm = $PDO->prepare($sql);
        $stm->execute($in_array);
    }
}

    $stmt = $PDO->query("SELECT id, name, email, created FROM induction_requests  WHERE requestedInduction = 1 ORDER BY created");
    $requests = $stmt->fetchAll();
?>

<div id="content" class="row">

    <div class="col-md-8">

    <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Date requested</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($requests as $row) {
                $name = $row['name'];
                $email = $row['email'];
                $id = $row['id'];
                $created = new DateTime($row['created']);

                echo '<tr>';
                echo '<td>' . $name . '</td>';
                echo '<td><a href=mailto:' . $email . ' >' . $email . '</a></td>';
                echo '<td>' . $created->format("d/m/Y") . '</td>';
                echo '<td><input class="checkbox" name="checkbox[]" type="checkbox" value="' . $id . '"></td>';
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="col-md-4">
        <div class="padded-button">
        <input class="form-control btn" type="submit" name="delete" value="Delete Selected" />
        </div>
    </form>
    <div class="padded-button">
    <button type="button" onclick="selectAll()" class="form-control btn">Select All</button>
    </div>
    <div class="padded-button">
    <form action="<?php echo "mailto:"; foreach($requests as $row) {echo $row['email'] . "; ";
   }?>" method="GET">
        <input class="form-control btn" type="submit" value="Email All" />
    </form>
        </div>
    </div>

</div>

<script>

function selectAll(){
    var unchecked = true;
    if ($('.checkbox:checked').length == $('.checkbox').length ){
        unchecked = false;
    }
    $(".checkbox").prop('checked', unchecked);
}

</script>

<?php require_once TEMPLATES_PATH . "/footer.php";?>
