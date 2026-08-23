<?php
$table=$_REQUEST['table'];
$class_date=$_REQUEST['class_date'];
$name=$_REQUEST['name'];
$content=$_REQUEST['content'];
$attendance=$_REQUEST['attendance'];
$student_notes=$_REQUEST['student_notes'];
$future_notes=$_REQUEST['future_notes'];
$hours_taught=$_REQUEST['hours_taught'];
if($table=='private_students'){
    $pay_rate=$_REQUEST['pay_rate'];
    if(isset($_REQUEST['pay_date'])){
        $pay_date=$_REQUEST['pay_date'];
    } else{
        $pay_date=null;
    }
}

include '../connect.php';
$connection=connect("localhost", "root", "", "class_logs");
if($table=='private_students'){
    $sql="INSERT INTO $table (class_date, name, student_notes, 
    content, attendance, future_notes, hours_taught, pay_rate, pay_date)
    VALUES ('$class_date', '$name', '$student_notes', '$content', '$attendance', '$future_notes',
    $hours_taught, $pay_rate, '$pay_date')";
} else {
    $sql="INSERT INTO $table (class_date, name, student_notes, 
    content, attendance, future_notes, hours_taught)
    VALUES ($class_date, $name, $student_notes, $content, $attendance, $future_notes,
    $hours_taught)";
}
$stmt = $connection->stmt_init();
    $stmt->prepare($sql);
    try{
        $stmt->execute();
        echo '{"success":"Class record added successfully"}';
    } catch (mysqli_sql_exception $e) {
        echo '{"error":"error adding class record"}';
    }
?>