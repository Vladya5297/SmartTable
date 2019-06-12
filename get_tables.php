<?php
if (isset($_POST['data'])) {
    $arr = array();
    include_once "database.php";
    $result = mysqli_query($db, "SELECT table_number FROM tables");
    for ($i = 0; $i < mysqli_num_rows($result); ++$i) {
        $row = mysqli_fetch_array($result);
        array_push($arr, $row['table_number']);
    }
    echo json_encode($arr);
}