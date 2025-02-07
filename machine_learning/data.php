<?php
if (($handle = fopen("products_data.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
    fclose($handle);
}
?>
