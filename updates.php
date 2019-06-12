<?php
if (isset($_POST['data'])) {
    $arr = array();

    include_once "database.php";
    //all events since last minute
    $result_event = mysqli_query($db, "SELECT data, coreid FROM events WHERE published_at >= date_sub(now(), INTERVAL 1 MINUTE) AND handled = 0");
    mysqli_query($db, "UPDATE events SET handled = 1 WHERE published_at >= date_sub(now(), INTERVAL 1 MINUTE) AND handled = 0");
    //number of events
    $event_row_number = mysqli_num_rows($result_event);
    //loop through all rows
    for ($i = 0; $i < $event_row_number; ++$i) {
        $row_event = mysqli_fetch_array($result_event);
        //take table name for every event
        $result_table = mysqli_query($db, "SELECT * FROM tables WHERE coreid = '$row_event[coreid]'");
        $row_table = mysqli_fetch_array($result_table);
        //take card name for every event
        $result_card = mysqli_query($db, "SELECT * FROM cards WHERE card_id = '$row_event[data]'");
        $row_card = mysqli_fetch_array($result_card);
        if (mysqli_num_rows($result_table) == 0) {
            $temp = array("add_table" => $row_event['coreid']);
            array_push($arr, $temp);
        }
        if (mysqli_num_rows($result_card) == 0) {
            $temp = array("add_card" => $row_event['data']);
            array_push($arr, $temp);
        }
        if (mysqli_num_rows($result_table) != 0 AND mysqli_num_rows($result_card) != 0) {
            $temp = array("table_number" => $row_table['table_number'], "card_name" => $row_card['card_name']);
            array_push($arr, $temp);
        }
    }
    echo json_encode($arr);
}
?>