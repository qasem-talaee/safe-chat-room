<?php
header('Content-Type:application/json');
session_start();
if(!isset($_SESSION['email'])){
    header('Location: login.php');
}
if(!isset($_SESSION['key'])){
    header('Location: index.php');
}
include('../includes/db.php');
include('test_input.php');
include('hash_pass.php');
include('encrypt.php');
$email = $_SESSION['email'];
$sql = "select * from user where email=:email";
$run = $pdo -> prepare($sql);
$run -> bindValue(":email", $email);
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
    if($hash_key == $o_key){
        $key = $_SESSION['key'];
        if(isset($_GET['p'])){
            $page = test_input($_GET['p']);
        }else{
            $page = 1;
        }
        $per_page = 30;
        $per_page = $per_page * $page;
        $sql = "(select * from chat where room_id=:id order by date desc limit $per_page) order by date";
        $run = $pdo -> prepare($sql);
        $run -> bindValue(":id", $id);
        $run -> execute();
        $out = array();
        $rows = array();
        while($row = $run -> fetch()){
            $user_id = $row['user_id'];
            $sql = "select * from user where id=:id";
            $run_user_name = $pdo -> prepare($sql);
            $run_user_name -> bindValue(":id", $user_id);
            $run_user_name -> execute();
            $row_user_name = $run_user_name -> fetch();
            $user_name = $row_user_name['name'];
            array_push($rows, $row['user_id']);
            array_push($rows, $user_name);
            array_push($rows, decrypt($row['text'], $key));
            array_push($out, $rows);
            $rows = array();
        }
        echo(json_encode($out));
    }else{
        header('Location: index.php');
    }
}else{
    header('Location: index.php');
}
?>