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
$get = "select * from user where email='$email'";
$run = mysqli_query($con, $get);
$row = mysqli_fetch_array($run);
$user_id = $row['id'];
$id = test_input($_GET['id']);
$get_exist = "select * from room where id='$id'";
$run_exist = mysqli_query($con, $get_exist);
if(mysqli_num_rows($run_exist) != 0){
    $hash_key = hash_pass($_SESSION['key']);
    $row_key = mysqli_fetch_array($run_exist);
    $o_key = $row_key['seckey'];
    if($hash_key == $o_key && $_GET['chat'] != ''){
        $chat = test_input($_GET['chat']);
        $chat = encrypt($chat, $_SESSION['key']);
        $insert = "insert into chat (room_id,user_id,text,date) values ('$id','$user_id','$chat',NOW())";
        $run = mysqli_query($con, $insert);
    }else{
        header('Location: index.php');
    }
}else{
    header('Location: index.php');
}
?>