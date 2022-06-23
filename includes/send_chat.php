<?php
session_start();
if(!isset($_SESSION['email'])){
    header('Location: login.php');
}
if(!isset($_SESSION['key'])){
    header('Location: index.php');
}
include('db.php');
include('../func/test_input.php');
include('../func/hash_pass.php');
include('../func/encrypt.php');
$email = $_SESSION['email'];
$sql = "select * from user where email=:email";
$run = $pdo -> prepare($sql);
$run -> bindValue(":email",$email);
$run -> execute();
$row = $run -> fetch();
$user_id = $row['id'];
$id = test_input($_GET['id']);
$sql = "select count(*) from room where id=:id";
$run_exist = $pdo -> prepare($sql);
$run_exist -> bindValue(":id", $id);
$run_exist -> execute();
if($run_exist -> fetchColumn() != 0){
    $hash_key = hash_pass($_SESSION['key']);
    $sql = "select * from room where id=:id";
    $run_exist = $pdo -> prepare($sql);
    $run_exist -> bindValue(":id", $id);
    $run_exist -> execute();
    $row_key = $run_exist -> fetch();
    $o_key = $row_key['seckey'];
    if($hash_key == $o_key && $_GET['chat'] != ''){
        $chat = test_input($_GET['chat']);
        $chat = encrypt($chat, $_SESSION['key']);
        $sql = "insert into chat (room_id,user_id,text,date) values (:id,:user_id,:chat,NOW())";
        $run = $pdo -> prepare($sql);
        $run -> bindValue(":id", $id);
        $run -> bindValue(":user_id", $user_id);
        $run -> bindValue(":chat", $chat);
        $run -> execute();
    }else{
        header('Location: index.php');
    }
}else{
    header('Location: index.php');
}
?>