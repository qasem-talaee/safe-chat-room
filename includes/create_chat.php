<?php
session_start();
include('db.php');
include('../func/test_input.php');
include('../func/hash_pass.php');
if(!isset($_SESSION['email'])){
    header('Location: login.php');
}
$email = $_SESSION['email'];
$flag = 0;
if(isset($_POST['submit'])){
    if($_POST['name'] == ''){
        $flag = 1;
    }
    if($_POST['key'] == ''){
        $flag = 1;
    }
    if($flag != 1){
        $name = test_input($_POST['name']);
        $key = test_input($_POST['key']);
        if(strlen($key) < 8){
            header('Location: ../index.php?e=key');
        }else{
            $key = hash_pass($key);
            $get = "select * from room where name='$name'";
            $run = mysqli_query($con, $get);
            if(mysqli_num_rows($run) == 0){
                $get_id = "select * from user where email='$email'";
                $run_id = mysqli_query($con, $get_id);
                $row_id = mysqli_fetch_array($run_id);
                $id = $row_id['id'];
                $insert = "insert into room (id,name,creator,seckey) values (NULL,'$name','$id','$key')";
                $run_insert = mysqli_query($con, $insert);
                if($run){
                    header('Location: ../index.php');
                }else{
                    header('Location: ../index.php?e=wrong-create');
                }
            }else{  
                header('Location: ../index.php?e=name-create');
            }
        }
    }else{
        header('Location: ../index.php');
    }
}
?>