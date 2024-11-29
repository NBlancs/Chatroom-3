<?php
session_start();
if (!isset($_SESSION["userId"])) {
    header("Location: login.php");
    exit();
}
require_once "config.php";
date_default_timezone_set('Asia/Manila');

$userId = $_SESSION["userId"];

if(isset($_POST['update'])){
    // Handle file upload
    $photo = "";
    $targetDir = "userImages/";
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
                $photo .= $targetFilePath;
            } else {
                $photo .= "userImages/default.jpg";
            }
        } else {
            $photo .= "userImages/default.jpg";
        }
    } else {
        $photo .= "userImages/default.jpg";
    }
    
    $createRoomSql = "UPDATE users SET imagePath = '$photo' WHERE userId = $userId";
    mysqli_query($link, $createRoomSql);
    
    header("Location: index.php?id=0");
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
    
    <title>User Settings</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
        <div class="container">
            <a class="navbar-brand" href="#">User Settings</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class='nav-item'>
                        <a class='nav-link' href='index.php'>Return</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="login-form">
        <div class="cotainer">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class='card-header'>User Settings</div>
                        <div class="card-body">
                            <form action="#" method="POST" name="update" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <label for="photo" class="col-md-4 col-form-label text-md-right">User Profile</label>
                                    <div class="col-md-6">
                                        <input type="file" class="form-control-file" name="photo" id="photo">
                                    </div>
                                </div>
                                <div class="col-md-6 offset-md-4">
                                    <input type="submit" value="Update" name="update">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>