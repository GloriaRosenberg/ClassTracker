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

include 'connect.php';
$stmt = $connection->stmt_init();
if($table=='private_students'){
    $sql="INSERT INTO $table (class_date, name, student_notes, 
    content, attendance, future_notes, hours_taught, pay_rate, pay_date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE student_notes = ?, content = ?, attendance = ?, 
    future_notes = ?, hours_taught = ?, pay_rate = ?, pay_date = ?";
    $stmt->prepare($sql);
    $stmt->bind_param('ssssssddsssssdds', $class_date, $name, $student_notes, $content, $attendance, $future_notes,
    $hours_taught, $pay_rate, $pay_date, $student_notes, $content, $attendance, $future_notes, $hours_taught, $pay_rate, 
    $pay_date);
} else {
    $sql="INSERT INTO $table (class_date, name, student_notes, 
    content, attendance, future_notes, hours_taught)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE student_notes = ?, content = ?, attendance = ?, 
    future_notes = ?, hours_taught = ?";
    $stmt->prepare($sql);
    $stmt->bind_param('ssssssdssssd', $class_date, $name, $student_notes, $content, $attendance, $future_notes,
    $hours_taught, $student_notes, $content, $attendance, $future_notes, $hours_taught);
}
try{
    $stmt->execute();
    $json["success"]="Class record for $name on $class_date updated successfully";
} catch (mysqli_sql_exception $e) {
    $json["error"]="error adding class record";
}
$send=json_encode($json);
echo $send;
?>