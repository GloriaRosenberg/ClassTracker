<?php
include 'connect.php';
$table=$_REQUEST['clientGroup'];
$sql="SELECT DISTINCT name FROM $table";
$query=$connection->stmt_init();
$query->prepare($sql);
try {
    $query->execute();
    $result=mysqli_stmt_get_result($query);
    $numRows=mysqli_num_rows($result);
    if($numRows<1){
        echo '{"noData":"No groups registered yet"}';
    } else {
        while ($data=$result->fetch_assoc()) {
        $rows[]=$data;
        }
        $JSON=json_encode($rows);
        echo $JSON;
    }
} catch (msqli_sql_exception $e) {
    echo '{"error":"Error retrieving data"}';
}
?>