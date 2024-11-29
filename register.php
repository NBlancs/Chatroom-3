<?php
session_start();
if (isset($_SESSION['userId'])) {
    header("Location: index.php");
    exit();
}

require_once "config.php";

if(isset($_POST["register"])){
    
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password2 = $_POST["password2"];

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
                    $mail->Password='fftecchjthlqapse';
    
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
                            window.location.replace('verification.php');
                        </script>
                        <?php
                    }
                }
            }
        } else {
            echo    "<script>
                        alert('Email is Already Taken.');
                    </script>";
        }
    }
        
}

?>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="style.css">

    <link rel="icon" href="Favicon.png">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

    <title>Register Page</title>

    <style>
    /* Style for the disabled register button */
    #registerButton:disabled {
        background-color: #f8f9fa; /* Light grey background */
        color: #6c757d; /* Dark grey text color */
        cursor: not-allowed; /* Disable pointer events */
    }
</style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-laravel">
        <div class="container">
            <a class="navbar-brand" href="#">Register Form</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php" >Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php" style="font-weight:bold; color:black; text-decoration:underline">Register</a>
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
                        <div class="card-header">Register</div>
                        <div class="card-body">
                            <form action="#" method="POST" name="register" id="registrationForm">
                                <div class="form-group row">
                                    <label for="username" class="col-md-4 col-form-label text-md-right">Username</label>
                                    <div class="col-md-6">
                                        <input type="text" id="username" class="form-control" name="username" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">Campus Email</label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <input type="text" id="email" name="email" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                    <div class="col-md-6">
                                        <input type="password" id="password" class="form-control" name="password" required>
                                        <i class="bi bi-eye-slash" id="togglePassword"></i>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password2" class="col-md-4 col-form-label text-md-right">Confirm Password</label>
                                    <div class="col-md-6">
                                        <input type="password" id="password2" class="form-control" name="password2" required>
                                        <i class="bi bi-eye-slash" id="togglePassword2"></i>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                    <input type="submit" value="Register" name="register">
                                    </div>
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
<script>
    const toggle = document.getElementById('togglePassword');
    const toggle2 = document.getElementById('togglePassword2');
    const password = document.getElementById('password');
    const password2 = document.getElementById('password2');
    
    toggle.addEventListener('click', function(){
        if(password.type === "password"){
            password.type = 'text';
        }else{
            password.type = 'password';
        }
        this.classList.toggle('bi-eye');
    });
    toggle2.addEventListener('click', function(){
        if(password2.type === "password"){
            password2.type = 'text';
        }else{
            password2.type = 'password';
        }
        this.classList.toggle('bi-eye');
    });

</script>
