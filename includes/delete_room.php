<?php
session_start();
if(!isset($_SESSION['email'])){
    header('Location: login.php');
}
include('db.php');
include('../func/test_input.php');
$id = test_input($_GET['id']);
$email = $_SESSION['email'];
$sql = "select * from user where email=:email";
$run_id = $pdo -> prepare($sql);
$run_id -> bindValue(":email", $email);
$run_id -> execute();
$row_id = $run_id -> fetch();
$user_id = $row_id['id'];
$sql = "select * from room where creator=:user_id and id=:id";
$run_exist = $pdo -> prepare($sql);
$run_exist -> bindValue(":user_id", $user_id);
$run_exist -> bindValue(":id", $id);
$run_exist -> execute();
if($run_exist -> fetchColumn() != 0){
    $sql = "delete from room where id=:id";
    $run_delete_room = $pdo -> prepare($sql);
    $run_delete_room -> bindValue(":id", $id);
    $run_delete_room -> execute();

    $sql = "delete from chat where room_id=:id";
    $run_chat = $pdo -> prepare($sql);
    $run_chat -> bindValue(":id", $id);
    $run_chat -> execute();
    header('Location: index.php');
}else{
    header('Location: index.php');
}
?>