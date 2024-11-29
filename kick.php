<?php
    session_start();
    if (!isset($_SESSION["userId"])) {
        header("Location: login.php");
        exit();
    }
    require_once "config.php";

    $roomId = $_GET["roomId"];
    $memberId = $_GET["memberId"];
    $leave_chatroom_prompt = "UPDATE members SET status= 0 WHERE userId = $memberId AND chatroomId = $roomId";
    mysqli_query($link, $leave_chatroom_prompt);
    $fetch_user = "SELECT * FROM users WHERE userId = $memberId";
    $result = $link->query($fetch_user);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row["username"];
        $updateText_prompt = "INSERT INTO messages(userId, chatroomId, message) VALUES (0,$roomId,'".$username." has been kicked from the chat.')";
        mysqli_query($link, $updateText_prompt);
    }
    
    header("Location: index.php?id=$roomId");
    exit();
?>