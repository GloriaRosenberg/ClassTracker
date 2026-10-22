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
$old_date=$_REQUEST['old_date'];

include 'connect.php';
$stmt = $connection->stmt_init();
if($table=='private_students'){
    $sql="UPDATE $table 
    SET class_date = ?, student_notes = ?, 
    content = ?, attendance = ?, future_notes = ?, hours_taught = ?, pay_rate = ?, pay_date = ?
    WHERE class_date = ? AND name = ?";
    $stmt->prepare($sql);
    $stmt->bind_param('sssssddsss', $class_date, $student_notes, $content, $attendance, $future_notes,
    $hours_taught, $pay_rate, $pay_date, $old_date, $name);
} else {
    $sql="UPDATE $table 
    SET class_date = ?, student_notes = ?, 
    content = ?, attendance = ?, future_notes = ?, hours_taught = ?
    WHERE class_date = ? AND name = ?";
    $stmt->prepare($sql);
    $stmt->bind_param('sssssdss', $class_date, $student_notes, $content, $attendance, $future_notes,
    $hours_taught, $old_date, $name);
}
try{
    $stmt->execute();
    if($table=='private_students'){
        if($pay_date==null){
            $message = "pay date null";
        } else {
            $message = "mystery";
        }
    } else {
        $message = "";
    }
    $json["success"]="Class record for $name on $class_date $message modified successfully";
} catch (mysqli_sql_exception $e) {
    $json["error"]="error adding class record: ".$e;
}
$send=json_encode($json);
echo $send;
?>