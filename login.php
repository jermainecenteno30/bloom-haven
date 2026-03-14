<?php
session_start();
include("config/db.php");

$error = "";

// Check if form submitted
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // raw password for verification

   $stmt = $conn->prepare("SELECT user_id, password, fullname FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 1){
    $user = $result->fetch_assoc();

    if(password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['fullname'];
        header("Location: index.php");
        exit;
    
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Bloom Haven</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
<h1>Bloom Haven</h1>
</header>

<h2>Login</h2>

<?php if($error != ""): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post" action="">
<input type="email" name="email" placeholder="Email" required><br><br>
<input type="password" name="password" placeholder="Password" required><br><br>
<button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register Here</a></p>

<footer>
<p>© 2026 Bloom Haven</p>
</footer>

</body>
</html>