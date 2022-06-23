<?php
session_start();
if(!isset($_SESSION['email'])){
    header('Location: login.php');
}
include('includes/db.php');
include('func/get_id.php');
include('func/test_input.php');
$user_id = get_id();
if(!isset($_GET['p']) || $_GET['p'] == ''){
    $page = 1;
}else{
    $page = test_input($_GET['p']);
}
if(isset($_GET['s']) && $_GET['s'] != ''){
    $search = test_input($_GET['s']);
    $sql_count = "select count(*) from room where creator<>:id and name like :search";
    $result_count = $pdo -> prepare($sql_count);
    $result_count -> bindValue(':search', '%'.$search.'%');
    $result_count -> bindValue(':id', $user_id);
    $result_count -> execute();
    $count_all = $result_count -> fetchColumn();
    $per_page = 20;
    $count_page = (int)($count_all / $per_page) + 1;
    $start = ($page - 1) * $per_page;
    $sql = "select * from room where creator<>:id and name like :search order by id desc limit $start,$per_page";
    $run = $pdo -> prepare($sql);
    $run -> bindValue(':search', '%'.$search.'%');
    $run -> bindValue(':id', $user_id);
    $run -> execute();
}else{
    $sql_count = "select count(*) from room where creator<>?";
    $result_count = $pdo -> prepare($sql_count);
    $result_count -> execute([$user_id]);
    $count_all = $result_count -> fetchColumn();
    $per_page = 20;
    $count_page = (int)($count_all / $per_page) + 1;
    $start = ($page - 1) * $per_page;
    $sql = "select * from room where creator<>? order by id desc limit $start,$per_page";
    $run = $pdo -> prepare($sql);
    $run -> execute([$user_id]);
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
        <script src="js/alert.js"></script>
        <script>
            function delete_room(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this and all chats will delete!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var xmlHttp = new XMLHttpRequest();
                        xmlHttp.open( "GET", "includes/delete_room.php?id=" + id, false ); // false for synchronous request
                        xmlHttp.send( null );
                        location.reload();
                    }
                })
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

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3">
                    <div class="card text-white mt-2">
                        <div class="card-header"><b>Create a chat room</b></div>
                        <div class="card-body">
                            <?php if(isset($_GET['e'])){if($_GET['e'] == 'key'){ ?>
                                <p class="text-dark bg-danger">Key is too short.</p>
                            <?php }} ?>
                            <?php if(isset($_GET['e'])){if($_GET['e'] == 'wrong-create'){ ?>
                                <p class="text-dark bg-danger">Something went wrong.Please try again.</p>
                            <?php }} ?>
                            <?php if(isset($_GET['e'])){if($_GET['e'] == 'name-create'){ ?>
                                <p class="text-dark bg-danger">Name of room is already exist.PLease enter another name.</p>
                            <?php }} ?>
                            <form class="from-group" method="post" action="includes/create_chat.php">
                                <div class="mb-3 mt-3">
                                    <label for="ch_d">Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                                <p class="bg-warning text-dark">You must enter 8 character at least to confirm.</p>
                                <div class="mb-3 mt-3">
                                    <label for="ch_d">Key</label>
                                    <input type="password" class="form-control" id="key" name="key">
                                </div>
                                <div class="mb-3 mt-3">
                                    <input type="submit" class="form-control btn btn-primary" id="sumbit" name="submit" value="Create">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="card text-white mt-2">
                        <div class="card-header"><b>My Chat Room(s)</b></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Enter Key to Enter Room</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <?php
                                $user_id = get_id();
                                $sql_my = "select * from room where creator=?";
                                $result_my = $pdo -> prepare($sql_my);
                                $result_my -> execute([$user_id]);
                                while($row_my_room = $result_my -> fetch()){
                                    $my_room_id = $row_my_room['id'];
                                    $my_room_name = $row_my_room['name'];
                                    ?>
                                        <tr>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <td><?php echo($my_room_name); ?></td>
                                                </div>
                                                <div class="col-sm-7">
                                                    <td><form method="post" action="includes/room_enter.php" class=""><input type="hidden" name="id" value="<?php echo($my_room_id); ?>" ><div class="row"><div class="col-sm-9 mt-1"><input type="password" name="key" class="form-control" ></div><div class="col-sm-3 mt-1"><input class="btn btn-warning" type="submit" name="submit" value="Enter" ></div></div></form></td>
                                                </div>
                                                <div class="col-sm-2">
                                                    <td><button class="btn btn-danger" onclick="delete_room(<?php echo($my_room_id); ?>)">Delete</button></td>
                                                </div>
                                            </div>
                                        </tr>
                                    <?php }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            
                        </div>
                    </div>
                    <div class="card text-white mt-2">
                        <div class="card-header">
                            <form method="get" action="index.php">
                                <div class="row">
                                    <div class="col-sm-2 mt-1">
                                        <b>Chat Rooms List</b>
                                    </div>
                                    <div class="col-sm-6 mt-1">
                                        <input type="text" name="s" id="s" placeholder="Search in rooms" class="form-control">
                                    </div>  
                                    <div class="col-sm-2 mt-1">
                                        <input type="submit" Value="Search" class="form-control btn btn-warning">
                                    </div>
                                    <div class="col-sm-2 mt-1">
                                        <a class="btn btn-warning" href="index.php">Refresh</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                                <?php
                                if($count_all == 0){
                                    echo('Nothing to show!');
                                }else{ ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Enter Key to Enter Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        <?php   while($row = $run -> fetch()){
                                    $room_id = $row['id'];
                                    $room_name = $row['name'];
                                    ?>
                                        <tr>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <td><?php echo($room_name); ?></td>
                                                </div>
                                                <div class="col-sm-7">
                                                    <td><form method="post" action="includes/room_enter.php" class=""><input type="hidden" name="id" value="<?php echo($room_id); ?>" ><div class="row"><div class="col-sm-9 mt-1"><input type="password" name="key" class="form-control" ></div><div class="col-sm-3 mt-1"><input class="btn btn-warning" type="submit" name="submit" value="Enter" ></div></div></form></td>
                                                </div>
                                            </div>
                                        </tr>
                                    <?php } 
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } 
                                    ?>
                        </div>
                        <div class="card-footer">
                            <nav aria-label="...">
                                <ul class="pagination pagination-sm">
                                    <?php
                                    if(isset($_GET['s']) && $_GET['s'] != ''){
                                        echo('<li class="page-item"><a class="page-link" href="index.php?p=1&s='. $search .'">Start</a></li>');
                                    }else{
                                        echo('<li class="page-item"><a class="page-link" href="index.php?p=1">Start</a></li>');
                                    }  
                                    ?>
                                    <?php
                                    for($i=($page - 3);$i<($page + 3);$i++){
                                        if($i >= 1){
                                            if($i <= $count_page){
                                                if($i == $page){ ?>
                                                    <li class="page-item active" aria-current="page">
                                                        <span class="page-link"><?php echo($i); ?></span>
                                                    </li>
                                        <?php   }else{ 
                                                    if(isset($_GET['s']) && $_GET['s'] != ''){ ?>
                                                        <li class="page-item"><a class="page-link" href="index.php?p=<?php echo($i); ?>&s=<?php echo($search); ?>"><?php echo($i); ?></a></li>
                                            <?php   }else{ ?>
                                                        <li class="page-item"><a class="page-link" href="index.php?p=<?php echo($i); ?>"><?php echo($i); ?></a></li>
                                            <?php   } 
                                                    
                                               }
                                            }
                                        }
                                    }
                                    ?>
                                    <?php
                                    if(isset($_GET['s']) && $_GET['s'] != ''){
                                        echo('<li class="page-item"><a class="page-link" href="index.php?p='. $count_page .'&s='. $search .'">End</a></li>');
                                    }else{
                                        echo('<li class="page-item"><a class="page-link" href="index.php?p='. $count_page .'">End</a></li>');
                                    }  
                                    ?>
                                </ul>
                            </nav>
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