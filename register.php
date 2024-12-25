<?php
session_start();

$errors = [];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ayushiladani";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['register'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmpassword'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];
    $terms_accepted = isset($_POST['terms']) ? $_POST['terms'] : 0;

    // Validate form inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password) || empty($gender) || empty($status)) {
        $errors[] = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    } else {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $sql = "INSERT INTO users (first_name, last_name, email, password, gender, status, terms_accepted, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssssiss", $first_name, $last_name, $email, $hashed_password, $gender, $status, $terms_accepted, date("Y-m-d h:i:s"), date("Y-m-d h:i:s"));
            if ($stmt->execute()) {
                $_SESSION['registration_message'] = "Success! Your account was created successfully.";
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Error inserting user data: " . $conn->error;
            }
        } else {
            $errors[] = "Error preparing statement: " . $conn->error;
        }
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Registration Form</h1>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="register.php" method="post" enctype="multipart/form-data">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <label for="confirmpassword">Confirm Password:</label>
        <input type="password" name="confirmpassword" id="confirmpassword" required><br><br>

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select><br><br>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="">Select Status</option>
            <option value="enabled">Enabled</option>
            <option value="disabled">Disabled</option>
        </select><br><br>

        <input type="checkbox" name="terms" id="terms" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
        <label for="terms">I accept the terms and conditions</label><br><br>

        <button type="submit" name="register">Register</button>
    </form>
</body>
</html>
