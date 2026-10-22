<?php
$table=$_REQUEST['table'];
$name=$_REQUEST['name'];
$date=$_REQUEST['date'];

$cols='hours_taught, content, attendance, student_notes, future_notes';
if($table=='private_students'){
    $cols=$cols.', pay_rate, pay_date';
}
$sql="SELECT $cols from $table WHERE name='$name' AND class_date='$date'";

include "connect.php";

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