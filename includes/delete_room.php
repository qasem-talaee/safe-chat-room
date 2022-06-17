<?php
session_start();
if(!isset($_SESSION['email'])){
    header('Location: login.php');
}
include('db.php');
include('../func/test_input.php');
$id = test_input($_GET['id']);
$email = $_SESSION['email'];
$get_id = "select * from user where email='$email'";
$run_id = mysqli_query($con, $get_id);
$row_id = mysqli_fetch_array($run_id);
$user_id = $row_id['id'];
$get_exist = "select * from room where creator='$user_id' and id='$id'";
$run_exist = mysqli_query($con, $get_exist);
if(mysqli_num_rows($run_exist) != 0){
    $delete_room = "delete from room where id='$id'";
    $run_delete_room = mysqli_query($con, $delete_room);
    $get_chat = "delete from chat where room_id='$id'";
    $run_chat = mysqli_query($con, $get_chat);
    header('Location: index.php');
}else{
    header('Location: index.php');
}
?>