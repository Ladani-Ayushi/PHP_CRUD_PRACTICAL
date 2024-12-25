<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_SESSION['registration_message'])) {
    echo "<p>{$_SESSION['registration_message']}</p>";
    unset($_SESSION['registration_message']);
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ayushiladani";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['status'] == 'disabled') {
            $_SESSION['error_message'] = "Error! Your account is disabled.";
        } else {
            $_SESSION['user_id'] = $row['id'];
            header("Location: dashboard.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Email and password do not match.";
    }
}

if (isset($_SESSION['registration_success'])) {
    echo "<p>Registration success message: {$_SESSION['success_message']}</p>";
    $_SESSION['registration_success'] = true;
    $_SESSION['success_message'] = "Success! Your account was created successfully. Please wait until your account becomes active.";
    unset($_SESSION['registration_success']);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Log in</title>
</head>

<body>
<h1>Log in Form</h1>
<?php

if(isset($_SESSION['error_message'])) {
    echo "<p>{$_SESSION['error_message']}</p>";

    unset($_SESSION['error_message']);

    if(isset($_SESSION['success_message'])) {
        echo "<p>{$_SESSION['success_message']}</p>";
        unset($_SESSION['success_message']);
    }
}
?>
<form method="post" action="login.php">

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required><br><br>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required><br><br>

    <button type="submit">Login</button>
</form>
</body>
</html>
