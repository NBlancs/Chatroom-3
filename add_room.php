<?php
session_start();
if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}
require_once "config.php";
date_default_timezone_set('Asia/Manila');

$join = $_GET["join"];
$userId = $_SESSION["userId"];

if(!($join == 0) && !($join == 1)){
    header("Location: index.php");
    exit();
}

if(isset($_POST['create'])){
    $roomname = $_POST['roomname'];
    // Handle file upload
    $photo = "chatroomLogos/aclc-blue.png";
    $targetDir = "chatroomLogos/";
    $fileName = basename($_FILES["photo"]["name"]);
    date_default_timezone_set('Asia/Manila');
    $uniqueFileName = time() . '_' . $fileName;
    $targetFilePath = $targetDir . $uniqueFileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    if (!empty($_FILES["photo"]["name"])) {
        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
                $photo = $targetFilePath;
            } else {
                $photo = "chatroomLogos/aclc-blue.png";
            }
        } else {
            $photo = "chatroomLogos/aclc-blue.png";
        }
    } else {
        $photo = "chatroomLogos/aclc-blue.png";
    }
    
    // Array to store generated codes
    $codes = array();
    $fetch_all_chatrooms_prompt = "SELECT * FROM chatrooms ORDER BY lastActive DESC";
    $fetch_all_chatrooms = mysqli_query($link, $fetch_all_chatrooms_prompt);
    while($chatroom = mysqli_fetch_assoc($fetch_all_chatrooms)){
        $roomCode = $chatroom['chatroomCode'];
        $codes[] = $roomCode;
    }
    // Length of the random string
    $roomCode = "";
    $length = 10;
    
    // Define characters
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    
    // Loop until a unique code is generated
    while (true) {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
    
        // Check if the generated code already exists in the array
        if (!in_array($randomString, $codes)) {
            // If not, add it to the array and break the loop
            $roomCode = $randomString;
            break;
        }
    }
    
    $createRoomSql = "INSERT INTO chatrooms(chatroomCode, chatroomName, logo) VALUES ('$roomCode','$roomname','$photo')";
    mysqli_query($link, $createRoomSql);
    
    $getRoomSql = "SELECT * FROM chatrooms WHERE chatroomCode='$roomCode'";
    $result = mysqli_query($link, $getRoomSql);
    while($chatroom = mysqli_fetch_assoc($result)){
        $roomId = $chatroom["chatroomId"];
        $newMemberSql = "INSERT INTO members(isAdmin, userId, chatroomId) VALUES (1, $userId, $roomId)";
        mysqli_query($link, $newMemberSql);
        $fetch_user = "SELECT * FROM users WHERE userId = $userId";
        $result = $link->query($fetch_user);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $username = $row["username"];
            $updateText_prompt = "INSERT INTO messages(userId, chatroomId, message) VALUES (0,$roomId,'".$username." has created the chatroom.')";
            mysqli_query($link, $updateText_prompt);
        }
        header("Location: index.php?id=$roomId");
    }
}

if(isset($_POST['join'])){
    $roomCode = $_POST['roomcode'];
    $invcode = $_POST['invcode'];
    
    if(!empty($invcode)){
        $sql = "SELECT * FROM invites WHERE inviteCode = $invcode";
        $result = $link->query($sql);

        if ($result->num_rows > 0) {
            // Fetch the datetime value from the result
            $row = $result->fetch_assoc();
            $codeCreatedAt = new DateTime($row["createdAt"]);
            
            // Get the current datetime
            $currentDateTime = new DateTime();
            
            // Calculate the difference in days
            $interval = $currentDateTime->diff($codeCreatedAt);
            $daysDifference = $interval->days;
            
            // Check if it's been a day since the last update
            if ($daysDifference >= 1) {
                echo    "<script>
                            alert('Invalid Code! Please ask an admin for a new code');
                        </script>";
            } else {
                $roomId = $row["chatroomId"];
                $fetch_room = "SELECT * FROM members WHERE userId = $userId AND chatroomId = $roomId AND status = 1";
                $result = $link->query($fetch_room);
                if ($result->num_rows > 0) {
                    echo    "<script>
                                alert('Already In Room.');
                                window.location.href='index.php?id=$roomId';
                            </script>";
                } else {
                    $newMemberSql = "INSERT INTO members(userId, chatroomId) VALUES ($userId, $roomId)";
                    mysqli_query($link, $newMemberSql);
                    $currentDateTime = date('Y-m-d H:i:s');
                
                    $newLastUpdatedValue = $currentDateTime;
                    $fetch_user = "SELECT * FROM users WHERE userId = $userId";
                    $result = $link->query($fetch_user);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $username = $row["username"];
                        $updateText_prompt = "INSERT INTO messages(userId, chatroomId, message) VALUES (0,$roomId,'".$username." has entered the chat.')";
                        mysqli_query($link, $updateText_prompt);
                    }
                
                    $sql2 = "UPDATE chatrooms SET lastActive = '$newLastUpdatedValue' WHERE chatroomId = $roomId";
                    mysqli_query($link, $sql2);
                    header("Location: index.php?id=$roomId");
                }
                
            }
        } else {
            echo    "<script>
                        alert('Invalid Code!');
                    </script>";
        }
    } else if(!empty($roomCode)){
        $sql = "SELECT * FROM chatrooms WHERE chatroomCode = '$roomCode'";
        $result = $link->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $roomId = $row["chatroomId"];

            // Checks if user is already in this chatroom.
            $fetch_room = "SELECT * FROM members WHERE userId = $userId AND chatroomId = $roomId AND status = 1";
            $result = $link->query($fetch_room);
            if ($result->num_rows > 0) {
                echo    "<script>
                            alert('Already In Room.');
                            window.location.href='index.php?id=$roomId';
                        </script>";
            } else {
                $newMemberSql = "INSERT INTO members(userId, chatroomId) VALUES ($userId, $roomId)";
                mysqli_query($link, $newMemberSql);
                $currentDateTime = date('Y-m-d H:i:s');
            
                $newLastUpdatedValue = $currentDateTime;
            
                $fetch_user = "SELECT * FROM users WHERE userId = $userId";
                $result = $link->query($fetch_user);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $username = $row["username"];
                    $updateText_prompt = "INSERT INTO messages(userId, chatroomId, message) VALUES (0,$roomId,'".$username." has entered the chat.')";
                    mysqli_query($link, $updateText_prompt);
                }
                $sql2 = "UPDATE chatrooms SET lastActive = '$newLastUpdatedValue' WHERE chatroomId = $roomId";
                mysqli_query($link, $sql2);
                header("Location: index.php?id=$roomId");
            }
        } else {
            echo    "<script>
                        alert('Invalid Code!');
                    </script>";
        }
    } else{
        echo    "<script>
                    alert('Please input a room code or invite code!');
                </script>";
    }
}
?>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="style.css">

    <link rel="icon" href="Favicon.png">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    
    <title>New Room</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
        <div class="container">
            <a class="navbar-brand" href="#">Add New Chatroom</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <?php
                        if($join == 0){
                            echo    "<li class='nav-item'>
                                        <a class='nav-link' href='add_room.php?join=0' style='font-weight:bold; color:black; text-decoration:underline'>Create Room</a>
                                    </li>
                                    <li class='nav-item'>
                                        <a class='nav-link' href='add_room.php?join=1'>Join Room</a>
                                    </li>
                                    <li class='nav-item'>
                                        <a class='nav-link' href='index.php'>Return</a>
                                    </li>";
                        }
                        else{
                            echo    "<li class='nav-item'>
                                        <a class='nav-link' href='add_room.php?join=0'>Create Room</a>
                                    </li>
                                    <li class='nav-item'>
                                        <a class='nav-link' href='add_room.php?join=1' style='font-weight:bold; color:black; text-decoration:underline'>Join Room</a>
                                    </li>
                                    <li class='nav-item'>
                                        <a class='nav-link' href='index.php'>Return</a>
                                    </li>";
                        }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="login-form">
        <div class="cotainer">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <?php
                            if($join == 0){
                                echo "<div class='card-header'>CREATE ROOM</div>";
                            }
                            else{
                                echo "<div class='card-header'>JOIN ROOM</div>";
                            }
                        ?>
                        <div class="card-body">
                            <?php
                                if($join == 0){
                                    echo    '<form action="#" method="POST" name="create" enctype="multipart/form-data">
                                                <div class="form-group row">
                                                    <label for="roomname" class="col-md-4 col-form-label text-md-right">Room Name</label>
                                                    <div class="col-md-6">
                                                        <input type="text" id="roomname" class="form-control" name="roomname" required autofocus>
                                                    </div>
                                                </div>
                
                                                <div class="form-group row">
                                                    <label for="photo" class="col-md-4 col-form-label text-md-right">Photo</label>
                                                    <div class="col-md-6">
                                                        <input type="file" class="form-control-file" name="photo" id="photo">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 offset-md-4">
                                                    <input type="submit" value="Create" name="create">
                                                </div>
                                            </form>';
                                }
                                else{
                                    echo    '<form action="#" method="POST" name="join" enctype="multipart/form-data">
                                                <div class="form-group row">
                                                    <label for="roomcode" class="col-md-4 col-form-label text-md-right">Room Code</label>
                                                    <div class="col-md-6">
                                                        <input type="text" id="roomcode" class="form-control" name="roomcode" autofocus>
                                                    </div>
                                                </div>
                
                                                <div class="form-group row">
                                                    <label for="invcode" class="col-md-4 col-form-label text-md-right">Invite Code</label>
                                                    <div class="col-md-6">
                                                        <input type="text" id="invcode" class="form-control" name="invcode" autofocus>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 offset-md-4">
                                                    <input type="submit" value="Join" name="join">
                                                </div>
                                            </form>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>