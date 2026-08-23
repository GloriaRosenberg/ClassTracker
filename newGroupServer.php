<?php
$newGroup=$_REQUEST['newGroup'];

include 'connect.php';
$connection=connect("localhost", "root", "", "class_logs");
$query="CREATE TABLE $newGroup"
?>