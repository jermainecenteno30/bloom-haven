<?php
session_start();
include("config/db.php");

$error = "";
$success = "";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    if(empty($name) || empty($email) || empty($password)){
        $error = "All fields are required.";
    } else {

        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $error = "Email already registered.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if($stmt->execute()){
                $success = "Account created successfully! <a href='login.php'>Login here</a>.";
            } else {
                $error = "Registration failed.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Bloom Haven</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
<h1>Bloom Haven</h1>
</header>

<h2>Create Account</h2>

<?php if($error != ""): ?>
<p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if($success != ""): ?>
<p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="post" action="">
<input type="text" name="name" placeholder="Full Name" required><br><br>
<input type="email" name="email" placeholder="Email" required><br><br>
<input type="password" name="password" placeholder="Password" required><br><br>
<button type="submit">Create Account</button>
</form>

<footer>
<p>© 2026 Bloom Haven</p>
</footer>

</body>
</html>