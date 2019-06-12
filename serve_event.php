<?php
if ($_POST['serve_event'] == "new_event")
{
    include_once "database.php";
    $result = mysqli_query($db, "INSERT INTO servetime(employee, time) VALUES('$_POST[employee]', '$_POST[time]')");
}
if ($_POST['serve_event'] == "get_events") {
    include_once "database.php";
    $result = mysqli_query($db, "SELECT * FROM servetime WHERE employee = '$_POST[employee]'");
    $arr = [['', $_POST['employee']]];
    for ($i = 0; $i < mysqli_num_rows($result); ++$i) {
        $row = mysqli_fetch_array($result);
        list($hours, $mins, $secs) = explode(':', $row['time']); //преобразовываем в секунды
        $_seconds=($hours * 3600 ) + ($mins * 60 ) + $secs;
        $temp = array($i+1, $_seconds);
        array_push($arr, $temp);
    }
    echo json_encode($arr);
}