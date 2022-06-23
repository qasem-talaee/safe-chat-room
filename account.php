<?php
session_start();
if(!isset($_SESSION['email'])){
    header('Location: login.php');
}
include('includes/db.php');
include('func/hash_pass.php');
include('func/test_input.php');
$email = $_SESSION['email'];

$sql = "select * from user where email=?";
$result = $pdo -> prepare($sql);
$result -> execute([$email,]);
$row = $result -> fetch();

$id = $row['id'];
$name = $row['name'];
$pass = $row['pass'];
$flag = 0;
if(isset($_POST['submit'])){
    if($_POST['name'] == ''){
        $flag = 1;
    }
    if($_POST['email'] == ''){
        $flag = 1;
    }
    if($flag == 0){
        $name = test_input($_POST['name']);
        $email = test_input($_POST['email']);
        if($_POST['old_pass'] == ''){
            $sql = "update user set name=?, email=? where id=?";
            $result = $pdo -> prepare($sql);

            if($result -> execute([$name, $email, $id])){
                header('Location: index.php');
            }else{
                $flag = 1;
            }
        }else{
            $old_pass = test_input($_POST['old_pass']);
            $new_pass = test_input($_POST['new_pass']);
            $new_pass_again = test_input($_POST['new_pass_again']);
            if(strlen($new_pass) < 8){
                $flag = 1;
            }else{
                if(hash_pass($old_pass) == $pass){
                    if($new_pass == $new_pass_again){
                        $new_pass = hash_pass($new_pass);
                        
                        $sql = "update user set name=?,email=?,pass=? where id=?";
                        $result = $pdo -> prepare($sql);

                        if($result -> execute([$name, $email, $new_pass, $id])){
                            header('Location: logout.php');
                        }else{
                            $flag = 1;
                        }
                    }else{
                        $flag = 1;
                    }
                }else{
                    $flag = 1;
                }
            }
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
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <p class="text-dark btn btn-info" aria-current="page">Welcome <?php echo($_SESSION['email']); ?> !</p>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-light" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-light" aria-current="page" href="account.php">Account</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark btn btn-warning" aria-current="page" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

            
        <div class="container-fluid text-black">
            <div class="row">
                <div class="col-12">
                    <div class="card text-white mt-2">
                        <div class="card-header"><b>Acoount Information</b></div>
                        <div class="card-body">
                            <?php
                            if($flag == 1){ ?>
                                <p class="text-dark bg-danger">Something went wrong.Please try again!</p>
                            <?php } ?>
                            <form method="post" action="account.php">
                                <div class="mb-3 mt-3">
                                    <label for="">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo($name); ?>">
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo($email); ?>">
                                </div>
                                <p class="bg-warning text-dark">If you don't want to change your password, Please left these inputs empty.</p>
                                <p class="bg-warning text-dark">You must enter 8 character at least to confirm.</p>
                                <div class="mb-3 mt-3">
                                    <label for="">Old Password</label>
                                    <input type="password" class="form-control" id="old_pass" name="old_pass">
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="">New Password</label>
                                    <input type="password" class="form-control" id="new_pass" name="new_pass">
                                </div>
                                <div class="mb-3 mt-3">
                                    <label for="">New Password Again</label>
                                    <input type="password" class="form-control" id="new_pass_again" name="new_pass_again">
                                </div>
                                <div class="mb-3 mt-3">
                                    <input type="submit" class="form-control btn btn-primary" id="submit" name="submit" value="Update">
                                </div>
                            </form>
                        </div>
                    </div>
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