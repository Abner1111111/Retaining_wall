<?php
require "back/db_configs.php";
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'back/vendor/autoload.php';

function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

$otpSent = false;
$errors = [];
$email = '';
$password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (isset($_POST['send_otp'])) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $otp = generateOTP();
                $_SESSION['login_otp'] = $otp;
                $_SESSION['login_email'] = $email;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['otp_timestamp'] = time();
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'johngarzon933@gmail.com';
                    $mail->Password = 'ignj faab eqpt dkhj';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('RetainingWall@gmail.com', 'Retaining Wall');
                    $mail->addAddress($email, $user['first_name'] . ' ' . $user['last_name']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Login OTP Verification';
                    $mail->Body = <<<HTML
                            <!DOCTYPE html>
                            <html lang="en">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            </head>
                            <body style="margin: 0; padding: 0; font-family: Arial, sans-serif;">
                                <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                                    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <h2 style="color: #333333; text-align: center; margin-bottom: 20px;">Verification Code</h2>
                                        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center;">
                                            <p style="font-size: 16px; color: #666666; margin-bottom: 15px;">Your verification code is:</p>
                                            <h1 style="color: #73877b; letter-spacing: 5px; margin: 20px 0; font-size: 32px;">{$otp}</h1>
                                            <p style="font-size: 14px; color: #999999;">This code will expire in 5 minutes</p>
                                        </div>
                                        <div style="text-align: center; margin-top: 20px;">
                                            <p style="font-size: 13px; color: #999999;">If you didn't request this code, please ignore this email.</p>
                                            <p>© 2025 Engineering Solutions. All rights reserved.</p>
                                        </div>
                                    </div>
                                </div>
                            </body>
                            </html>
                            HTML;
                    $mail->send();

                    $otpSent = true;
                } catch (Exception $e) {
                    $errors[] = "Failed to send OTP email. Please try again.";
                }
            } else {
                $errors[] = "Invalid email or password";
            }
        } catch (PDOException $e) {
            $errors[] = "Login failed: " . $e->getMessage();
        }
    } elseif (isset($_POST['verify_otp'])) {
        if (time() - $_SESSION['otp_timestamp'] > 300) {
            $errors[] = "OTP has expired. Please try again.";
            session_destroy();
        } else {
            $entered_otp = trim($_POST['otp']);
            $stored_otp = $_SESSION['login_otp'];

            if ($entered_otp === $stored_otp) {
                unset($_SESSION['login_otp']);
                unset($_SESSION['otp_timestamp']);
                $_SESSION['authenticated'] = true;
                header("Location: Questionnaire.php");
                exit();
            } else {
                $errors[] = "Invalid OTP. Please try again.";
                $otpSent = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="Css/index.css">
</head>
<body>
<div class="spinner-overlay" id="spinnerOverlay">
        <div class="spinner">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <div class="login-container">
        <h2>Login</h2>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p class='error-message'>$error</p>";
            }
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <ion-icon name="mail-outline"></ion-icon>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($email); ?>" 
                       placeholder="Enter your email" required 
                       <?php echo $otpSent ? 'readonly' : ''; ?>>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" id="password" name="password" 
                       value="<?php echo htmlspecialchars($password); ?>" 
                       placeholder="Enter your password" required 
                       <?php echo $otpSent ? 'readonly' : ''; ?>>
            </div>
            
            <?php if (!$otpSent): ?>
            <button type="submit" name="send_otp">Login</button>
            <?php endif; ?>

            <div class="otp-section <?php echo $otpSent ? 'visible' : ''; ?>" id="otpSection">
                <div class="form-group">
                    <label for="otp">Enter OTP</label>
                    <input type="text" id="otp" name="otp" class="otp-input" maxlength="6" pattern="\d{6}" 
                           title="Please enter 6 digits" placeholder="******" 
                           <?php echo $otpSent ? 'required' : ''; ?>>
                </div>
                <button type="submit" name="verify_otp">Verify OTP</button>
            </div>

            <div class="signup-link">
                <a href="signup.php">Don't have an account? <i><u>Sign up here</u></i></a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.getElementById('spinnerOverlay').style.display = 'flex';
        });
    </script>
</body>
</html>