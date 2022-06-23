<?php
function get_id (){
    include('includes/db.php');
    if(!isset($_SESSION['email'])){
        header('Location: login.php');
    }
    $email = $_SESSION['email'];
    $sql = "select * from user where email=?";
    $result = $pdo -> prepare($sql);
    $result -> execute([$email,]);
    $row = $result -> fetch();
    $id = $row['id'];
    return $id;
}
?>