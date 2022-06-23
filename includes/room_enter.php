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
$sql = "select count(*) from room where id=:id";
$run_exist = $pdo -> prepare($sql);
$run_exist -> bindValue(":id", $id);
$run_exist -> execute();
if($run_exist -> fetchColumn() != 0){
    $key = test_input($_POST['key']);
    $hash_key = hash_pass($key);
    $sql = "select * from room where id=:id";
    $run_exist = $pdo -> prepare($sql);
    $run_exist -> bindValue(":id", $id);
    $run_exist -> execute();
    $row_key = $run_exist -> fetch();
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