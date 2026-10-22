<?php
$group_name=$_REQUEST['group_name'];
$url=$_REQUEST['url'];
$description=$_REQUEST['description'];

include 'connect.php';
$sql="INSERT INTO links (group_name, url, description) VALUES (?,?,?)";
$stmt=mysqli_stmt_init($connection);
$stmt->prepare($sql);
$stmt->bind_param('sss', $group_name, $url, $description);
try{
    $stmt->execute();
    $stmt->close();
    $result['success']="Link inserted correctly";
}catch(mysqli_sql_exception $e){
    $result['error']="Error: ".$e->get_message();
}
$json=json_encode($result);
echo $json;
?>