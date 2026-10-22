<?php
$table=$_REQUEST['table'];
$group=$_REQUEST['group'];

require 'connect.php';

$query=$connection->stmt_init();
$query->prepare("DELETE from $table where name='$group'");
$message;
try{
    $query->execute();
    $query->close();
    $connection->close();
    $message["success"]="Class $group deleted successfully";
} catch (mysqli_sql_exception $e){
    $message["error"]="Error executing SQL: ".$e->get_message();
}
$json=json_encode($message);
echo $json;
?>