<?php
session_start();
if(!isset($_SESSION['email'])){
    header('Location: ../login.php');
}
if(!isset($_POST['submit'])){
    header('Location: ../index.php');
}
include('../func/test_input.php');
include('../func/hash_pass.php');
include('../includes/db.php');
if(!isset($_POST['id'])){
    header('Location: ../index.php');
}
if(!isset($_POST['key'])){
    header('Location: ../index.php');
}
$id = test_input($_POST['id']);
$get_exist = "select * from room where id='$id'";
$run_exist = mysqli_query($con, $get_exist);
if(mysqli_num_rows($run_exist) != 0){
    $key = test_input($_POST['key']);
    $hash_key = hash_pass($key);
    $row_key = mysqli_fetch_array($run_exist);
    $o_key = $row_key['seckey'];
    if($hash_key == $o_key){
        $_SESSION['key'] = $key;
        header('Location: ../room.php?id=' . $id);
    }else{
        header('Location: ../index.php');
    }
}else{
    header('Location: ../index.php');
}
?>