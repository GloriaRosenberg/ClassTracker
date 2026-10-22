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
    $pay_date=$_REQUEST['pay_date'];
    if($pay_date=="null"|| $pay_date=="0000-00-00" || $pay_date==""){
        $pay_date=null;
    }
}

include 'connect.php';
$stmt = $connection->stmt_init();
if($table=='private_students'){
    $sql="INSERT INTO $table (class_date, name, student_notes, 
    content, attendance, future_notes, hours_taught, pay_rate, pay_date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt->prepare($sql);
    $stmt->bind_param('ssssssdds', $class_date, $name, $student_notes, $content, $attendance, $future_notes,
    $hours_taught, $pay_rate, $pay_date);
} else {
    $sql="INSERT INTO $table (class_date, name, student_notes, 
    content, attendance, future_notes, hours_taught)
    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt->prepare($sql);
    $stmt->bind_param('ssssssd', $class_date, $name, $student_notes, $content, $attendance, $future_notes,
    $hours_taught);
}
try{
    $stmt->execute();
    $json["success"]="Class record for $name on $class_date added successfully";
} catch (mysqli_sql_exception $e) {
    $json["error"]="error adding class record: ".$e;
}
$send=json_encode($json);
echo $send;
?>