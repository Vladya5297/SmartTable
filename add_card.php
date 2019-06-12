<?php
if (isset($_POST['add_card']))
{
    include_once "database.php";
    $result = mysqli_query($db, "INSERT INTO cards(card_id, card_name) VALUES('$_POST[card_id]', '$_POST[card_name]')");
}