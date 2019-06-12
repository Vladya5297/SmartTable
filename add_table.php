<?php
if (isset($_POST['add_table']))
{
    include_once "database.php";
    $result = mysqli_query($db, "INSERT INTO tables(coreid, table_number) VALUES('$_POST[coreid]', '$_POST[table_number]')");
}