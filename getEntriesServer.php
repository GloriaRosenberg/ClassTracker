<?php
$table=$_REQUEST['table'];
$name=$_REQUEST['name'];
$sql="SELECT class_date FROM $table WHERE name='$name'";
include "connect.php";
$connection=connect("localhost", "root", "", "class_logs");
$query=$connection->stmt_init();
try{
    $query->prepare($sql);
    $query->execute();
    $result=mysqli_stmt_get_result($query);
    $numRows=mysqli_num_rows($result);
    if($numRows<1){
        echo '{"error":"No groups registered yet"}';
    } else {
        while ($data=$result->fetch_assoc()) {
        $rows[]=$data;
        }
        $JSON=json_encode($rows);
        echo $JSON;
    }
} catch(mysqli_sql_exception $e) {
    echo '{"error":"Error retrieving data"}';
}
?>