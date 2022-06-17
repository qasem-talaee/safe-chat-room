<?php
function get_id (){
    include('includes/db.php');
    if(!isset($_SESSION['email'])){
        header('Location: login.php');
    }
    $email = $_SESSION['email'];
    $get = "select * from user where email='$email'";
    $run = mysqli_query($con, $get);
    $row = mysqli_fetch_array($run);
    $id = $row['id'];
    return $id;
}
?>