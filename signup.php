<?php
require "back/db_configs.php";
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'back/vendor/autoload.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $errors = [];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Email already registered";
    }
    
    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);
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
                $mail->addAddress($email, $firstName . ' ' . $lastName);
                $mail->isHTML(true);
                $mail->Subject = 'Welcome to Retaining Wall Failure Assessment Web-based Tool';
                $mail->Body = file_get_contents('back/email.html');
                $mail->Body = str_replace('{$firstName}', $firstName, $mail->Body);
                $mail->Body = str_replace('{$email}', $email, $mail->Body);
                $mail->send();
                $success = "Registration successful! Please check your email for confirmation.";
                

                header("Location: index.php?registered=true");
                exit();
            } catch (Exception $e) {
                $errors[] = "Email could not be sent. Registration completed, but notification failed.";
            }
        } catch (PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="Css/signup.css">
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<p class='error-message'>$error</p>";
            }
        }
        if (isset($success)) {
            echo "<p class='success-message'>$success</p>";
        }
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <ion-icon name="person-outline"></ion-icon>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <ion-icon name="person-outline"></ion-icon>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <ion-icon name="mail-outline"></ion-icon>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <ion-icon name="lock-closed-outline"></ion-icon>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <button type="submit">Sign Up</button>
            <div class="login-link">
                <a href="index.php">Already have an account? <i><u> Login here</u></i></a>
            </div>
        </form>
    </div>
</body>
</html>