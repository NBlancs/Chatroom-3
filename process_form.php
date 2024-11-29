<?php
session_start(); // Start or resume the session

require_once "config.php";
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the "Register" button was clicked
    if(isset($_POST["register"])){
        if(!isset($_SESSION["otp"])){
            echo    "<script>
                        alert('No OTP code Found.');
                        window.location.href = 'register.php';
                    </script>";
        }
        $otp = $_POST["otp"];
        if($otp == $_SESSION["otp"]){
            $username = $_POST["username"];
            $email = $_POST["email"] . '@gmail.com';
            $password = $_POST["password"];
            $password2 = $_POST["password2"];
            echo    "<script>
                        alert('Username is Already Taken.');
                    </script>";
        
            $is_valid_username = "SELECT * FROM users WHERE username = '$username'";
            $filtered_username_list = mysqli_query($link, $is_valid_username);
        
            if(mysqli_num_rows($filtered_username_list) == 1){
                echo    "<script>
                            alert('Username is Already Taken.');
                        </script>";
            } else{
                $is_valid_email = "SELECT * FROM users WHERE email = '$email'";
                $filtered_email_list = mysqli_query($link, $is_valid_email);
                if (!(mysqli_num_rows($filtered_email_list) == 1)){
                    if($password != $password2){
                        $error_msg = "Passwords Don't Match.";
                        echo    "<script>
                                    alert('Passwords Must Match.');
                                </script>";
                        $_POST["password"] = "";
                        $_POST["password2"] = "";
                    } else{
                        $password_hash = password_hash($password, PASSWORD_BCRYPT);
                        
                        $insert_sql = "INSERT INTO users(username, email, password) VALUES('$username', '$email', '$password_hash')";
                        $insert_query = mysqli_query($link, $insert_sql);
            
                        $new_user = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM users WHERE email = '$email'"));
                        
                        if($insert_query){
                            $_SESSION["userId"] = $new_user["userId"];
                            header("Location: index.php");
                            exit();
                        } else{
                            echo    "<script>
                                        alert('An Error has Occured, Please Try again Later.');
                                    </script>";
                            header("Location: login.php");
                            exit();
                        }
                    }
                }
            }
            
        } else{
            echo    "<script>
                        alert('OTP code is Incorrect.');
                        window.location.href = 'register.php';
                    </script>";
        }
            
    }

    // Check if the "Send OTP" button was clicked
    if (isset($_POST["sendOTPButton"])) {
        $email = $_POST["email"] . '@gmail.com';
        $otp = rand(100000,999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['mail'] = $email;
        require "Mail/phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host='smtp.gmail.com';
        $mail->Port=587;
        $mail->SMTPAuth=true;
        $mail->SMTPSecure='tls';

        $mail->Username='mandbvcf@gmail.com';
        $mail->Password='flmzrgsepdqreqem';

        $mail->setFrom('mandbvcf@gmail.com', 'OTP Verification');
        $mail->addAddress($_POST["email"]);

        $mail->isHTML(true);
        $mail->Subject="Your verify code";
        $mail->Body="<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3>
        <br><br>
        <p>With regards,</p>
        <b></b>
        ";

        if(!$mail->send()){
            ?>
                <script>
                    alert("<?php echo "Register Failed, Invalid Email "?>");
                </script>
            <?php
        }else{
            ?>
            <script>
                alert("<?php echo "Register Successfully, OTP sent to " . $email ?>");
            </script>
            <?php
        }
    }
}
?>
