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
    if($hash_key == $o_key){
        $key = $_SESSION['key'];
        if(isset($_GET['p'])){
            $page = test_input($_GET['p']);
        }else{
            $page = 1;
        }
        $per_page = 30;
        $per_page = $per_page * $page;
        $get = "(select * from chat where room_id='$id' order by date desc limit $per_page) order by date";
        $run = mysqli_query($con, $get);
        $out = array();
        $rows = array();
        while($row = mysqli_fetch_assoc($run)){
            $user_id = $row['user_id'];
            $get_user_name = "select * from user where id='$user_id'";
            $run_user_name = mysqli_query($con, $get_user_name);
            $row_user_name = mysqli_fetch_array($run_user_name);
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