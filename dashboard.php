<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(isset($_GET['logout']) && $_GET['logout'] == 1) {

    $_SESSION = array();

    session_destroy();

    // Redirect to the login page
    header("Location: login.php");
    exit(); 
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
echo "Connected successfully";

// Check if user is logged in
/*if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}*/

$user_id = $_SESSION['user_id'];
$sql = "SELECT id, first_name, last_name, email, password,  gender, status, profile_image, created_at, updated_at FROM users WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    header("Location: login.php");
    exit();
}

$sql_all_users = "SELECT id, first_name, last_name, email, password, gender, status, profile_image, created_at, updated_at FROM users";
$result_all_users = $conn->query($sql_all_users);
$all_users_data = array();

if ($result_all_users->num_rows > 0) {

    while ($row = $result_all_users->fetch_assoc()) {
        $all_users_data[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>
<body>

    <h2>Welcome, <?php echo $user_data['first_name']; ?>!</h2>
    <p>Email: <?php echo $user_data['email']; ?></p>

    <h3>All Users Data</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Password</th>
            <th>Gender</th>
            <th>Status</th>
            <th>Profile Image</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
        <?php foreach ($all_users_data as $user) : ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['first_name']; ?></td>
                <td><?php echo $user['last_name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['password']; ?></td>
                <td><?php echo $user['gender']; ?></td>
                <td><?php echo $user['status']; ?></td>
                <td><?php echo $user['profile_image']; ?></td>
                <td><?php echo $user['created_at']; ?></td>
                <td><?php echo $user['updated_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table><br><br>

    <a href="?logout=1">Logout</a>

</body>
</html>
