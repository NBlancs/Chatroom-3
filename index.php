<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';
date_default_timezone_set('Asia/Manila');


$userId = $_SESSION["userId"];

$fetch_user = "SELECT * FROM users WHERE userId = $userId";
$result = $link->query($fetch_user);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $status = $row["status"];
    if($status == 0){
        $sql = "DELETE FROM users WHERE userId = $userId";
        mysqli_query($link, $sql);
        ?>
            <script>
                alert("<?php echo "User does not Exist"?>");
                window.location.replace('logout.php');
            </script>
        <?php
    }
}
$currentRoom = "";

$myRoomIds = array();

if(isset($_GET["id"])){
    $currentRoom = $_GET["id"];
    $fetch_my_chatrooms_prompt = "SELECT * FROM members WHERE userId = $userId and status = 1";
    $fetch_my_chatrooms = mysqli_query($link, $fetch_my_chatrooms_prompt);
    while($chatroom = mysqli_fetch_assoc($fetch_my_chatrooms)){
        $roomId = $chatroom['chatroomId'];
        $myRoomIds[] = $roomId;
    }
    if(!in_array($currentRoom, $myRoomIds) && !$currentRoom == 0){
        header("Location: index.php?id=0");
        exit();
    }
} else{
    header("Location: index.php?id=0");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    // Handle file upload
    $targetDir = "messageUploads/";
    $fileName = basename($_FILES["photo"]["name"]);
    $uniqueFileName = time() . '_' . $fileName;
    $targetFilePath = $targetDir . $uniqueFileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    if (!empty($_FILES["photo"]["name"])) {
        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf');
        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
                $photo = $targetFilePath;
            } else {
                $photo = "";
            }
        } else {
            $photo = "";
        }
    } else {
        $photo = "";
    }

    // Insert message and photo into the database
    if(empty(trim($message)) && empty(trim($photo))){
        echo    "<script>
                    alert('No Messages Detected');
                </script>";
    } else if(empty(trim($message))){
        $sql = "INSERT INTO messages (userId, chatroomId, uploadFilePath) VALUES ($userId, $currentRoom, '$photo')";
        mysqli_query($link, $sql);
    
        $currentDateTime = date('Y-m-d H:i:s');
    
        $newLastUpdatedValue = $currentDateTime;
    
        $updateChatroom = "UPDATE chatrooms SET lastActive = '$newLastUpdatedValue' WHERE chatroomId = $currentRoom";
        mysqli_query($link, $updateChatroom);
    } else if(empty(trim($photo))){
        $sql = "INSERT INTO messages (userId, chatroomId, message) VALUES ($userId, $currentRoom, '$message')";
        mysqli_query($link, $sql);
    
        $currentDateTime = date('Y-m-d H:i:s');
    
        $newLastUpdatedValue = $currentDateTime;
    
        $updateChatroom = "UPDATE chatrooms SET lastActive = '$newLastUpdatedValue' WHERE chatroomId = $currentRoom";
        mysqli_query($link, $updateChatroom);

    } else{
        $sql = "INSERT INTO messages (userId, chatroomId, message, uploadFilePath) VALUES ($userId, $currentRoom, '$message', '$photo')";
        mysqli_query($link, $sql);
    
        $currentDateTime = date('Y-m-d H:i:s');
    
        $newLastUpdatedValue = $currentDateTime;
    
        $updateChatroom = "UPDATE chatrooms SET lastActive = '$newLastUpdatedValue' WHERE chatroomId = $currentRoom";
        mysqli_query($link, $updateChatroom);
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Chat Rooms</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                margin: 0;
                font-family: "Lato", sans-serif;
                height: 100%;
                width: 100%;
            }
            #side-nav {
                position: fixed;
                height: 100%;
                width: 6%;
                background-color: #4C5B61;
                overflow-x: hidden;
                overflow-y: scroll;
            }
            #side-nav::-webkit-scrollbar {display: none;}
            #side-nav #contacts {
                position: sticky;
                top: 0;
                border-bottom: 3px solid black;
                background-color: transparent;
                padding-top: 1px;
                z-index: 9999;}
            #side-nav .inactive {
                margin: 10px 0px 10px 0px;
                height: 64px;
                width: 64px;
                border-radius: 50%;
                overflow: hidden;
                transition: all 0.3s ease-in-out;
            }
            #side-nav .inactive:hover {border-radius: 20%;}
            #side-nav .active {
                margin: 10px 0px 10px 0px;
                height: 64px;
                width: 64px;
                border-radius: 20%;
                overflow: hidden;
            }
            #side-nav .profile-pic {
                height: 100%;
                background-color: white;
            }
            #side-nav #exit{
                position: sticky;
                bottom: 0;
                background-color: transparent;
                border-top: 3px solid black;
                padding-bottom: 1px;
                z-index: 9999; 
            }

            #sidebar {
                position: fixed;
                margin-left: 6%;
                height: 100%;
                width: 15%;
                background-color: #829191;
                overflow-x: hidden;
                overflow-y: scroll;
            }
            #sidebar::-webkit-scrollbar {display: none;}
            #sidebar #current-room-logo{
                background-color: white;
                width: 100%;
                border-bottom: 3px solid black;
            }
            #sidebar .member-dropdown {
                display: flex; /* Use flexbox */
                justify-content: space-between;
                align-items: center; /* Vertically center items */
                border-top: 3px solid black;
                border-bottom: 3px solid black;
                padding: 5px;
            }
            #sidebar .member-dropdown.hidden {display: none}
            #sidebar .member-dropdown p {
                cursor: context-menu;
                font-size: 24px;
                margin: 0; /* Reset margin to ensure accurate centering */
            }
            #sidebar .dropdown-icon{
                width: 30px;
            }
            #sidebar .dropdown-icon-flipped {
                width: 30px;
                transform: scaleY(-1); /* Flip the image vertically */
            }
            .member-details {
                position: relative;
                width: 100%;
                display: inline-block;
                border-bottom: 3px solid black;
            }

            .member-details-btn {
                background-color: transparent;
                border: none;
                cursor: pointer;
                display: flex; /* Make button a flex container */
                align-items: center; /* Vertically center align */
            }

            .member-details-btn img {
                width: 32px;
                height: auto;
                border-radius: 50%;
                margin-right: 5px; /* Adjust margin to separate image from text */
                align-self: center;
            }

            .member-details.disabled .member-details-btn {
                pointer-events: none; /* Disable pointer events */
            }
            .blueText{
                color: blue; /* Change text color to blue */
            }
            .blkText{
                color: black;
            }

            .member-details-content {
                display: none;
                position: absolute;
                background-color: #f9f9f9;
                min-width: 160px;
                box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
                z-index: 1;
            }

            .member-details-content.show {
                display: block;
            }

            .member-details-content ul {
                list-style-type: none;
                padding: 0;
                margin: 0;
            }

            .member-details-content a {
                color: black;
                padding: 12px 16px;
                text-decoration: none;
                display: block;
            }

            .member-details-content a:hover {
                background-color: #f1f1f1;
            }

            .member-details.hidden{
                display: none;
            }
            .member-details.disabled .member-details-content {
                display: none; /* Hide content when dropdown is disabled */
            }
            #sidebar .hidden-member-sub-group{display: none}
            #sidebar .member-sub-group{
                text-align: left;
                border-top: 3px solid black;
                border-bottom: 3px solid black;
                background-color: red;
            }
            #sidebar #leave-chat-button{
                display: block;
                margin: 10px 0px 10px 0px;
                border-top: 3px solid black;
                border-bottom: 3px solid black;
                text-align: center;
                text-decoration: none;
                color: red;
            }

            #chat-area {
                position: fixed;
                margin-left: 21%;
                height: 100%;
                width: 79%;
                background-color: #949B96;
            }
            #chat-area #chat-box {
                position: fixed;
                height: 90%;
                width: 79%;
                overflow-x: hidden;
                overflow-y: scroll;
            }
            #chat-area #chat-box::-webkit-scrollbar {display: none;}
            #chat-area #chat-box h1.friendless{
                text-align: center;
                position: absolute;
                bottom: 0;
            }
            .sender-info {
                display: inline-block;
                vertical-align: middle;
                margin-right: 10px;
                margin-bottom: 10px;
            }

            .sender-profile {
                width: 32px; /* Adjusted width */
                height: 32px; /* Adjusted height */
                border-radius: 50%; /* Make the image circular */
                object-fit: cover; /* Maintain aspect ratio and cover entire area */
                vertical-align: middle;
                margin-right: 10px;
            }

            .sender-info p {
                display: inline-block;
                vertical-align: middle;
                margin: 0;
            }
            #chat-area #chat-box .message-block{
                margin-top: 5px;
                padding-left: 5px;
                padding-top: 5px;
                width: 100%;
                display: inline-block;
                background-color: transparent;
            }

            #chat-area #chat-box .message-block .sender-message-box{
                right: 0;
                background-color: #C5C5C5;
                border-radius: 5px;
                padding-top: 3px;
                padding-bottom: 3px;
                padding-left: 5px;
                max-width: fit-content;
            }
            #chat-area #chat-box .my-message {
                margin-right: 10px;
                max-width: fit-content;
            }
            #chat-area #chat-box .message img {margin-top: 5px; margin-bottom:5px}
            #chat-area #chat-box .message-photo{
                height:250px;
                width:auto;
                margin-bottom:5px;
                margin-right: 5px;
            }

            #chat-area #message-form{
                position: fixed;
                bottom: 0;
                padding: 10px;
                width: 79%;
                background-color: rgba(0, 0, 0, 0);
                height: 10%;
                overflow-y: hidden;
            }
            #chat-area #message-form::-webkit-scrollbar {display: none;}
            .centered-grey-text {
                color: black; /* Grey text color */
                width: 100%; /* Full width of parent div */
                margin: 0; /* Remove default margin */
            }
            .reset-file.inactive{display: none}
        </style>
    </head>
    <body>
        <div id="side-nav">
            <center>
            <div id="contacts">
                <?php
                    if($currentRoom == 0){
                        echo    '<div class="active" id="0" onclick="redirectTo(id)">
                                    <img src="chatroomLogos/aclc-blue.png" alt="avatar" class="profile-pic" title="Profile">
                                </div>';
                    } else{
                        echo    '<div class="inactive" id="0" onclick="redirectTo(id)">
                                    <img src="chatroomLogos/aclc-blue.png" alt="avatar" class="profile-pic" title="Profile">
                                </div>';
                    }
                ?>
            </div>
            <?php
                $fetch_all_chatrooms_prompt = "SELECT * FROM chatrooms ORDER BY lastActive DESC";
                $fetch_all_chatrooms = mysqli_query($link, $fetch_all_chatrooms_prompt);
                while($chatroom = mysqli_fetch_assoc($fetch_all_chatrooms)){
                    $roomId = $chatroom['chatroomId'];
                    if (in_array($roomId, $myRoomIds)) {
                        $roomName =  $chatroom['chatroomName'];
                        $logo = $chatroom['logo'];
                        if($currentRoom == $roomId){
                            echo    '<div class="active" id="'.$roomId.'" onclick="redirectTo(id)">
                                        <img src="'.$logo.'" alt="avatar" class="profile-pic" title="'.$roomName.'">
                                    </div>';
                        } else{
                            echo    '<div class="inactive" id="'.$roomId.'" onclick="redirectTo(id)">
                                        <img src="'.$logo.'" alt="avatar" class="profile-pic" title="'.$roomName.'">
                                    </div>';
                        }
                    }
                }
            ?>
            
            <div id="exit">
                <div class="inactive" onclick="location.href='add_room.php?join=0'">
                    <img src="chatroomLogos/add.jpg" alt="avatar" class="profile-pic" title="Add Room">
                </div>
                <div class="inactive" onclick="location.href='settings.php'">
                    <img src="chatroomLogos/settings2.png" alt="avatar" class="profile-pic" title="Settings">
                </div>
                <div class="inactive" onclick="location.href='logout.php'">
                    <img src="chatroomLogos/logout.jpg" alt="avatar" class="profile-pic" title="Log Out">
                </div>
            </div>
            </center>
        </div>
        <div id="sidebar">
            <?php
            if($currentRoom == 0){
                $fetch_user = mysqli_query($link, "SELECT * FROM users WHERE userId = $userId");
                $user = mysqli_fetch_assoc($fetch_user);
                $profilePic = $user["imagePath"];
                $username = $user["username"];

                echo "<img src='$profilePic' id='current-room-logo'>";
                echo "<p style= 'font-size: 24px; margin: 5px'>$username</p>";
            } else{
                $fetch_room = mysqli_query($link, "SELECT * FROM chatrooms WHERE chatroomId = $currentRoom");
                $room = mysqli_fetch_assoc($fetch_room);
                $chatroom_logo = $room["logo"];
                $roomName = $room["chatroomName"];
                $roomCode = $room["chatroomCode"];

                $fetch_member = mysqli_query($link, "SELECT * FROM members WHERE chatroomId = $currentRoom AND userId = $userId AND status = 1");
                $member = mysqli_fetch_assoc($fetch_member);
                $isAdmin = $member["isAdmin"];
                $tempId = "member-dropdown-icon";

                $fetch_members_prompt = "SELECT * FROM members WHERE chatroomId = $currentRoom AND status = 1";
                $fetch_members = mysqli_query($link, $fetch_members_prompt);
                if($isAdmin == 1){
                    echo "<img src='$chatroom_logo' id='current-room-logo'>";
                    echo "<p style= 'font-size: 24px; margin: 5px'>$roomName</p>";
                    echo "<p style= 'font-size: 16px; color: `grey`; margin: 5px'>Roomcode: $roomCode</p>";
                    echo '<div class="member-dropdown" onclick="toggleDropdownIcon()">';
                    echo '    <p>Members</p>'; // Removed inline styles for simplicity
                    echo '    <img src="chatroomLogos/dropdown.png" id="member-dropdown-icon" class="dropdown-icon">';
                    echo '</div>';
                    while ($members = mysqli_fetch_assoc($fetch_members)) {
                        $memberId = $members["userId"];
                        $fetch_member_info = "SELECT * FROM users WHERE userId = $memberId";
                        $member_info = mysqli_fetch_assoc(mysqli_query($link, $fetch_member_info));
                        $member_img = $member_info["imagePath"];
                        $member_name = $member_info["username"];
                        $memberId = $member_info["userId"];
                        if($memberId == $userId){
                            echo    '<div class="member-details hidden disabled">
                                        <button class="member-details-btn blueText">
                                            <img src="'.$member_img.'" alt="Icon"> '.$member_name.'
                                        </button>
                                    </div>';
                        } else {
                            echo    '<div class="member-details hidden">
                                        <button class="member-details-btn blkText">
                                            <img src="'.$member_img.'" alt="Icon"> '.$member_name.' ▼
                                        </button>
                                        <div class="member-details-content">
                                            <ul>
                                                <li><a href="kick.php?memberId='.$memberId.'&roomId='.$currentRoom.'">Kick</a></li>
                                            </ul>
                                        </div>
                                    </div>';
                        }
                    }
                } else{
                    echo "<img src='$chatroom_logo' id='current-room-logo'>";
                    echo "<p style= 'font-size: 24px; margin: 5px'>$roomName</p>";
                    echo '<div class="member-dropdown" onclick="toggleDropdownIcon()">';
                    echo '    <p>Members</p>'; // Removed inline styles for simplicity
                    echo '    <img src="chatroomLogos/dropdown.png" id="member-dropdown-icon" class="dropdown-icon">';
                    echo '</div>';
                    while ($members = mysqli_fetch_assoc($fetch_members)) {
                        $memberId = $members["userId"];
                        $fetch_member_info = "SELECT * FROM users WHERE userId = $memberId";
                        $member_info = mysqli_fetch_assoc(mysqli_query($link, $fetch_member_info));
                        $member_img = $member_info["imagePath"];
                        $member_name = $member_info["username"];
                        if($memberId == $userId){
                            echo    '<div class="member-details hidden hidden disabled">
                                        <button class="member-details-btn blueText">
                                            <img src="'.$member_img.'" alt="Icon"> '.$member_name.'
                                        </button>
                                    </div>';
                        } else{
                            echo    '<div class="member-details hidden hidden disabled">
                                        <button class="member-details-btn blkText">
                                            <img src="'.$member_img.'" alt="Icon"> '.$member_name.'
                                        </button>
                                    </div>';
                        }
                    }
                }
                
                echo "<a id='leave-chat-button' href='leave_chatroom.php?userId=$userId&roomId=$currentRoom'>Leave Chat</a>";
            }
            ?>
        </div>
        
        <div id="chat-area">
            <div id="chat-box">
                <?php
                    if($currentRoom == 0){
                        echo    "<h1 class='friendless'>Start by adding a new conversation.</h1>";
                    }
                    else{
                        $messages = array();
                        $fetch_messages_prompt = "SELECT * FROM messages WHERE chatroomId=$currentRoom ORDER BY dateSent DESC LIMIT 50";
                        $fetch_messages = mysqli_query($link, $fetch_messages_prompt);
                        while ($message = mysqli_fetch_assoc($fetch_messages)) {
                            $messageContent = "";

                            $senderId = $message['userId'];
                            if ($senderId == 0){
                                $messages[] = "<div class='message-block'><p class='centered-grey-text'>".$message['message']."</p></div>";
                            } else {
                                $fetch_sender_prompt = "SELECT * FROM users WHERE userId=$senderId";
                                $fetch_sender = mysqli_fetch_assoc(mysqli_query($link, $fetch_sender_prompt));
                                
                                $messageContent .= "<div class='sender-info'>";
                                $messageContent .= "<img class='sender-profile' src='" . $fetch_sender['imagePath'] . "' alt='Sender Profile Image'>";
                                $messageContent .= "<p>" . $fetch_sender['username'] . "</p>";
                                $messageContent .= "<p style='color: grey; margin-left: 20px'>" . $message["dateSent"] . "</p>";
                                $messageContent .= "</div>";
                                
                                $messageContent .= "<div class='sender-message-box'>";
                                // Check if the message contains text
                                if (!empty($message['message'])) {
                                    $messageContent .= "<p class='my-message'>" . $message['message'] . "</p>";
                                }
                                
                                // Check if the message contains a photo
                                if (!empty($message['uploadFilePath'])) {
                                    $messageContent .= "<img src='" . $message['uploadFilePath'] . "' alt='photo' class='message-photo'>";
                                }
                                $messageContent .= "</div>";
                            
                                // Add the message content to the array
                                if ($senderId === $_SESSION['userId']) {
                                    $messages[] = "<div class='message-block'>$messageContent</div>";
                                } else {
                                    $messages[] = "<div class='message-block'>$messageContent</div>";
                                }
                            }
                        }

                        foreach (array_reverse($messages) as $message) {
                            echo $message;
                        }
                    }
                ?>
            </div>
            <?php
                if(!$currentRoom == 0){
                    echo    '<form id="message-form" method="post" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="col-7">
                                        <input id="messageInput" type="text" class="form-control" name="message" placeholder="Input your message here...">
                                    </div>
                                    <div class="col">
                                        <input id="photoInput" type="file" class="form-control-file" name="photo" id="photo">
                                    </div>
                                    <div class="col">
                                        <button type="button" id="fileResetBtn" class="reset-file inactive">Remove File</button>
                                        <button id="submitButton" type="submit" class="btn btn-primary" disabled>Send</button>
                                    </div>
                                </div>
                            </form>';
                }
            ?>
        </div>
    </body>
</html>
<script>
    function redirectTo(id){
        let mainUrl = "index.php?id=";
        let redirectUrl = mainUrl.concat(id);
        location.replace(redirectUrl);
    }
    var chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;

    function toggleDropdownIcon() {
        var dropdownIcon = document.getElementById("member-dropdown-icon");
        if (dropdownIcon.classList.contains("dropdown-icon")) {
            dropdownIcon.classList.remove("dropdown-icon");
            dropdownIcon.classList.add("dropdown-icon-flipped");
            toggleMemberDetails();
        } else if (dropdownIcon.classList.contains("dropdown-icon-flipped")) {
            dropdownIcon.classList.remove("dropdown-icon-flipped");
            dropdownIcon.classList.add("dropdown-icon");
            toggleMemberDetails();
        }
    }

    function toggleMemberDetails() {
        var memberDetails = document.getElementsByClassName("member-details");
        for (var i = 0; i < memberDetails.length; i++) {
            if (memberDetails[i].classList.contains("hidden")) {
                memberDetails[i].classList.remove("hidden");
            } else {
                memberDetails[i].classList.add("hidden");
            }
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        var memberDetails = document.querySelectorAll('.member-details');

        memberDetails.forEach(function(element) {
            element.addEventListener('click', function() {
                var content = this.querySelector('.member-details-content');
                if (content.style.display === 'block') {
                    content.style.display = 'none';
                } else {
                    content.style.display = 'block';
                }
            });
        });
    })
    function checkInputs() {
        var messageValue = document.getElementById('messageInput').value.trim();
        var photoValue = document.getElementById('photoInput').value.trim();
        
        if (messageValue !== '' || photoValue !== '') {
            document.getElementById('submitButton').removeAttribute('disabled');
        } else {
            document.getElementById('submitButton').setAttribute('disabled', 'disabled');
        }
    }

    // Add event listeners to message and photo inputs
    document.getElementById('messageInput').addEventListener('input', checkInputs);
    document.getElementById('photoInput').addEventListener('change', checkInputs);

    document.getElementById('photoInput').addEventListener('change', function() {
        var filename = this.value.split('\\').pop(); // Get the filename without the full path
        var resetBtn = document.getElementById('fileResetBtn');

        if (filename) {
            resetBtn.classList.remove('inactive'); // Remove 'inactive' class
        }
    });

    document.getElementById('fileResetBtn').addEventListener('click', function() {
        // Reset the value of the file input field
        document.getElementById('photoInput').value = '';
        // Add 'inactive' class to reset button
        this.classList.add('inactive');
        // Update submit button state
        checkInputs();
    });
</script>