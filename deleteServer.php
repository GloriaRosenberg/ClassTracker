<?php
$class_date=$_REQUEST['class_date'];
$name=$_REQUEST['name'];
$table=$_REQUEST['table'];

include 'connect.php';
$connection=connect("localhost", "root", "", "class_logs");
$query=$connection->stmt_init();
$query->prepare("DELETE FROM $table WHERE class_date = '$class_date' AND name = '$name'");
try {
    $query->execute();
    echo '{"success":"Entry deleted successfully"}';
} catch (mysqli_sql_exception $e){
    echo '{"error":"There was an error deleting the entry."}';
}
?>