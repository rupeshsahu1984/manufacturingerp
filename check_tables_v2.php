<?php
$c = mysqli_connect("localhost", "root", "", "manufacturingerp");

$tables = ['distributors', 'customer_communications', 'customer_notes', 'leads', 'quotations', 'dispatch_notes'];

foreach ($tables as $t) {
    echo "--- Table: $t ---\n";
    $res = mysqli_query($c, "DESCRIBE $t");
    if ($res) {
        while($row = mysqli_fetch_assoc($res)) {
            echo $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Error or table not found.\n";
    }
    echo "\n";
}
