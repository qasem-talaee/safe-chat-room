<?php
session_start();
if(!isset($_SESSION['email'])){
    header('Location: login.php');
}
if(!isset($_SESSION['key'])){
    header('Location: index.php');
}
include('func/test_input.php');
include('func/hash_pass.php');
include('func/get_id.php');
include('includes/db.php');
$id = test_input($_GET['id']);
$user_id = get_id();
$sql = "select count(*) from room where id=:id";
$run_exist = $pdo -> prepare($sql);
$run_exist -> bindValue(':id', $id);
$run_exist -> execute();
if($run_exist -> fetchColumn() != 0){
    $hash_key = hash_pass($_SESSION['key']);
    $sql = "select * from room where id=:id";
    $run_exist = $pdo -> prepare($sql);
    $run_exist -> bindValue(':id', $id);
    $run_exist -> execute();
    $row_key = $run_exist -> fetch();
    $o_key = $row_key['seckey'];
    $room_name = $row_key['name'];
    if($hash_key == $o_key){
        #
    }else{
        header('Location: index.php');
    }
}else{
    header('Location: index.php');
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
        <script>
            let page = 1;
            function inpage(){
                page ++;
            }
            setInterval(
                async function (){
                    var xmlHttp = new XMLHttpRequest();
                    xmlHttp.open( "GET", "func/send_chat.php?id=<?php echo($id); ?>&p=" + page, true ); // false for synchronous request
                    xmlHttp.onload = function (e) {
                        let result = JSON.parse(xmlHttp.responseText);
                        let out = '';
                        for (let i = 0; i < result.length; i++){
                            if(result[i][0] == <?php echo($user_id); ?>){
                                out += '<div class="d-flex justify-content-end"> \
                                      <div class="card text-white mt-2 bg-primary chatdiv"> \
                                        <div class="card-header"><b>' + result[i][1] + '</b></div> \
                                            <div class="card-body"> \
                                                <p dir="auto">' + result[i][2] + '</p> \
                                    </div></div></div>';
                            }else{
                                out += '<div class="d-flex justify-content-start"> \
                                      <div class="card text-white mt-2 chatdiv"> \
                                        <div class="card-header"><b>' + result[i][1] + '</b></div> \
                                            <div class="card-body"> \
                                                <p dir="auto">' + result[i][2] + '</p> \
                                    </div></div></div>';
                            }
                        }
                        document.getElementById("chatcontainer").innerHTML = out;
                    };
                    xmlHttp.send( null );
                }, 3000);
        </script>
        <script>
            function send(){
                if(document.getElementById("chat").value.length != 0){
                    chat = document.getElementById("chat").value;
                    var xmlHttp = new XMLHttpRequest();
                    xmlHttp.open( "GET", "includes/send_chat.php?id=<?php echo($id); ?>&chat=" + chat, true ); // false for synchronous request
                    xmlHttp.send( null );
                    document.getElementById("chat").value = '';
                }
            }
        </script>
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
                            <p class="text-dark btn btn-info" aria-current="page">Welcome to <?php echo($room_name); ?> chat room!</p>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-light" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark btn btn-warning" aria-current="page" href="index.php">Exit Fom This Room</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="card text-white mt-2">
            <button class="btn btn-warning" onclick="inpage()">Load More...</button>
            </div>
        </div>

        <div class="container-fluid" id="chatcontainer">


        </div>
    </body>
    <div class="container-fluid">
        <div class="card text-white mt-2">
        </div>
    </div>
    <footer class="container-fluid text-white text-center mt-auto">
        <div class="row">
            <div class="col-sm-9 mt-3 mb-3">
                <textarea name="chat" id="chat" class="form-control" rows='2' placeholder="Type Something" dir="auto"></textarea>
            </div>
            <div class="col-sm-3 mt-3 mb-3">
                <button class="btn btn-warning form-control" style="height:100%;" onclick="send()">Send</button>
            </div>
        </div>
    </footer> 
</html>