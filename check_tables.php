<?php
$c = mysqli_connect("localhost", "root", "", "manufacturingerp");

$tables = ['sales_orders', 'goods_receipt_notes', 'products', 'stock', 'items'];

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
