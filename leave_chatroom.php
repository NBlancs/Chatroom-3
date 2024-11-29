<?php
    session_start();
    if (!isset($_SESSION["userId"])) {
        header("Location: login.php");
        exit();
    }
    require_once "config.php";

    $user_id = $_GET["userId"];
    $roomId = $_GET["roomId"];
    
    $fetch_chatroom = "SELECT * FROM members WHERE userId = $user_id AND chatroomId = $roomId AND status = 1";
    $chatroom = $link->query($fetch_chatroom);


    
    if ($chatroom->num_rows > 0) {
        $user = $chatroom->fetch_assoc();
        $adminStatus = $user["isAdmin"];
        if($adminStatus == 1){
            $fetch_members = "SELECT * FROM members WHERE chatroomId = $roomId AND status = 1 AND isAdmin = 0";
            $members = $link->query($fetch_chatroom);
            if ($members->num_rows > 0) {
                
            } else {
                $deleteChatroom = "DELETE FROM chatrooms WHERE chatroomId = $roomId";
                mysqli_query($link, $deleteChatroom);
            }
            $leave_chatroom_prompt = "UPDATE members SET status= 0 WHERE userId = $user_id AND chatroomId = $roomId";
            mysqli_query($link, $leave_chatroom_prompt);
        
            $fetch_user = "SELECT * FROM users WHERE userId = $user_id";
            $result = $link->query($fetch_user);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $username = $row["username"];
                $updateText_prompt = "INSERT INTO messages(userId, chatroomId, message) VALUES (0,$roomId,'".$username." has left the chat.')";
                mysqli_query($link, $updateText_prompt);
            }
        } else {
            $leave_chatroom_prompt = "UPDATE members SET status= 0 WHERE userId = $user_id AND chatroomId = $roomId";
            mysqli_query($link, $leave_chatroom_prompt);

            $fetch_user = "SELECT * FROM users WHERE userId = $user_id";
            $result = $link->query($fetch_user);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $username = $row["username"];
                $updateText_prompt = "INSERT INTO messages(userId, chatroomId, message) VALUES (0,$roomId,'".$username." has left the chat.')";
                mysqli_query($link, $updateText_prompt);
            }
        }
    }
    
    header("Location: index.php?id=0");
    exit();
?>