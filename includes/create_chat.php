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
            $sql = "select * from room where name=:name";
            $run = $pdo -> prepare($sql);
            $run -> bindValue(":name", $name);
            $run -> execute();
            if($run -> fetchColumn() == 0){
                $sql = "select * from user where email=:email";
                $run_id = $pdo -> prepare($sql);
                $run_id -> bindValue(":email", $email);
                $run_id -> execute();
                $row_id = $run_id -> fetch();
                $id = $row_id['id'];
                $sql = "insert into room (id,name,creator,seckey) values (NULL,?,?,?)";
                $run = $pdo -> prepare($sql);
                if($run -> execute([$name, $id, $key])){
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