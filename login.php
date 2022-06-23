<?php
session_start();
include("includes/db.php");
include("includes/send_email.php");
include("func/hash_pass.php");
include("func/test_input.php");
$flag_login = $flag_register =  0;
if(isset($_POST['login'])){
    if($_POST['passlogin'] == '' || $_POST['emaillogin'] || $_POST['caplogin'] == ''){
        $flag_login = 1;
    }
    $email = test_input($_POST['emaillogin']);
    $pass = test_input($_POST['passlogin']);
    $cap = test_input($_POST['caplogin']);
    if($cap == $_SESSION['cap-login']){
        $pass = hash_pass($pass);

        $sql = "select count(*) from user where email=? and pass=?";
        $result = $pdo->prepare($sql);
        $result->execute([$email, $pass]); 

        if($result -> fetchColumn() != 0){
            $_SESSION['email'] = $email;
            header('Location: index.php');
        }else{
            $flag_login = 1;
        }
    }else{
        $flag_login = 1;
    }
}
if(isset($_POST['register'])){
    if($_POST['passregister'] == '' || $_POST['emailregister'] || $_POST['capregister'] == '' || $_POST['passregister'] == ''){
        $flag_register = 1;
    }
    $name = test_input($_POST['nameregister']);
    $email = test_input($_POST['emailregister']);
    $pass = test_input($_POST['passregister']);
    $pass_again = test_input($_POST['pass_againregister']);
    $cap = test_input($_POST['capregister']);
    if($cap == $_SESSION['cap-register']){
        if(strlen($pass) < 8){
            $flag_register = 1;
        }else{
            if($pass == $pass_again){
                $pass = hash_pass($pass);

                $sql = "select count(*) from user where email=?";
                $result = $pdo->prepare($sql);
                $result->execute([$email]); 

                if($result -> fetchColumn() == 0){
                    $sql = "insert into user (id,name,email,pass,status) values (NULL,?,?,?,'user')";
                    $result = $pdo->prepare($sql);

                    if($result->execute([$name, $email, $pass])){
                        $flag_register = 2;
                    }else{
                        $flag_register = 1;
                    }
                }else{
                    $flag_register = 1;
                }
            }else{
                $flag_register = 1;
            }
        }
    }else{
        $flag_register = 1;
    }
}
if(isset($_POST['forget'])){
    if($_POST['emailforget'] == ''){
        $flag_register = 1;
    }
    $cap = test_input($_POST['capforget']);
    $email = test_input($_POST['emailforget']);
    if($cap == $_SESSION['cap-pass']){
        $get = "select * from user where email='$email'";
        $run = mysqli_query($con, $get);
        if(mysqli_num_rows($run) != 0){
            $row = mysqli_fetch_array($run);
            $user_id = $row['id'];
            $user_name = $row['name'];
            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*+=?/:;{}[]()';
            $pass = array();
            $alphaLength = strlen($alphabet) - 1;
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            $gen_pass = implode($pass);
            $gen_pass_hash = hash_pass($gen_pass);
            $update = "update user set pass='$gen_pass_hash' where id='$user_id'";
            $run = mysqli_query($con, $update);
            #send email
            $message = "<p>This email send from Safe Chat Room web site.<br>Your password was changed successfully.<br>Your new password is : <h3><b>$gen_pass</b></h3><br>Please login.Thank you.</p>";
            $from = $reply_to = 'no-reply@gmail.com';
            send_email($email, $user_name, 'Change Password', $message, $from, 'Safe Chat Room', $reply_to, 'no-reply');
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Safe Chat Rooms</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/bootstrap.min.css" rel="stylesheet" />
        <link href="css/mystyle.css" rel="stylesheet" />
        <script src="js/bootstrap.bundle.min.js"></script>
    </head>
    <body class="d-flex flex-column min-vh-100">

        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand text-white">Secret Chat Rooms</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                </div>
            </div>
        </nav>

        <div class="container">

            <?php if($flag_login == 1){ ?>
            <div class="card text-white mt-2">
                <div class="card-header"><b>Error</b></div>
                <div class="card-body">
                    <p class="text-dark bg-danger">Something went wrong in login.Please try again!</p>
                </div>
            </div>
            <?php } ?>

            <?php if($flag_register == 1){ ?>
            <div class="card text-white mt-2">
                <div class="card-header"><b>Error</b></div>
                <div class="card-body">
                    <p class="text-dark bg-danger">Something went wrong in register.Please try again!</p>
                </div>
            </div>
            <?php } ?>

            <?php if($flag_register == 2){ ?>
            <div class="card text-white mt-2">
                <div class="card-header"><b>Success</b></div>
                <div class="card-body">
                    <p class="text-dark bg-success">Your account created successfully.Please login now.</p>
                </div>
            </div>
            <?php } ?>

            <div class="card text-white mt-2">
                <div class="card-header"><b>If you already have a account, please login.</b></div>
                <div class="card-body">
                    <form class="from-group" method="post" action="login.php">
                        <input type="hidden" name="login">
                        <div class="mb-3 mt-3">
                            <label for="ch_d">Email</label>
                            <input type="email" class="form-control" id="email" name="emaillogin">
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="ch_d">Password</label>
                            <input type="password" class="form-control" id="pass" name="passlogin">
                        </div>
                        <div class="mb-3 mt-3">
                            <img src="includes/captcha.php?login" /><br />
                            <input type="text" class="form-control" id="cap" name="caplogin">
                        </div>
                        <div class="mb-3 mt-3">
                            <input type="submit" class="form-control btn btn-primary" id="sumbit" name="submit" value="Login">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card text-white mt-2">
                <div class="card-header"><b>If you don't have a account, please register first.</b></div>
                <div class="card-body">
                    <form class="from-group" method="post" action="login.php">
                        <input type="hidden" name="register">
                        <div class="mb-3 mt-3">
                            <label for="ch_d">Name</label>
                            <input type="text" class="form-control" id="name" name="nameregister">
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="ch_d">Email</label>
                            <input type="email" class="form-control" id="email" name="emailregister">
                        </div>
                        <p class="bg-warning text-dark">You must enter 8 character at least to confirm.</p>
                        <div class="mb-3 mt-3">
                            <label for="ch_d">Password</label>
                            <input type="password" class="form-control" id="pass" name="passregister">
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="ch_d">Password again</label>
                            <input type="password" class="form-control" id="pass_again" name="pass_againregister">
                        </div>
                        <div class="mb-3 mt-3">
                            <img src="includes/captcha.php?register" /><br />
                            <input type="text" class="form-control" id="cap" name="capregister">
                        </div>
                        <div class="mb-3 mt-3">
                            <input type="submit" class="form-control btn btn-primary" id="sumbit" name="submit" value="Register">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card text-white mt-2">
                <div class="card-header"><b>If you forget your password, please enter your email.</b></div>
                <div class="card-body">
                    <form class="from-group" method="post" action="login.php">
                        <input type="hidden" name="forget">
                        <div class="mb-3 mt-3">
                            <label for="ch_d">Email</label>
                            <input type="email" class="form-control" id="email" name="emailforget">
                        </div>
                        <div class="mb-3 mt-3">
                            <img src="includes/captcha.php?pass" /><br />
                            <input type="text" class="form-control" id="cap" name="capforget">
                        </div>
                        <div class="mb-3 mt-3">
                            <input type="submit" class="form-control btn btn-primary" id="sumbit" name="submit" value="Rebuild Password">
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </body>
    <div class="container-fluid">
        <div class="card text-white mt-2">
        </div>
    </div>
    <footer class="container-fluid text-white text-center mt-auto">
        <p><p>Developed with <span class="text-danger">&#10084;</span> by <a class="btn btn-warning" href="https://github.com/qasem-talaee" target="_blank">Qasem Talaee</a> (<script>const d = new Date();let year = d.getFullYear(); document.write("2021 - " + year);</script>)</p></p>
    </footer> 
</html>