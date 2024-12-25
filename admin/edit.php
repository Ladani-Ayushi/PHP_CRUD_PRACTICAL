<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ayushiladani";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$target_dir = "../assests/images/";
$url_dir = "assests/images/";

if(isset($_POST['submit'])) 
{
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $status = $_POST['status'];

    $old_image_path = '';
    $sql_old_image = "SELECT profile_image FROM users WHERE id=$id";

    $result_old_image = $conn->query($sql_old_image);
    if ($result_old_image->num_rows > 0) {
        $row_old_image = $result_old_image->fetch_assoc();
        $old_image_path = $row_old_image['profile_image'];
    }

    if(isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $file_name = $url_dir . basename($_FILES["profile_image"]["name"]);

        $allowed_types = array('jpg', 'jpeg', 'png');
        $file_ext = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        if(!in_array($file_ext, $allowed_types)) {
            echo "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
            exit; 
        }

        $file_size = $_FILES["profile_image"]["size"];
        if($file_size > 2097152) {
            echo "File too large. The file must be less than 2 megabytes.";
            exit; 
        }

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $image_path = $file_name;

            $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', password='$password', gender='$gender', status='$status', profile_image='$image_path' WHERE id=$id";

            if ($conn->query($sql) === TRUE) {
              
                if (!empty($old_image_path) && file_exists('../'.$old_image_path)) {
                    unlink('../'.$old_image_path);
                }
                echo "Success! User updated successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        $sql = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', password='$password', gender='$gender', status='$status' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo "Success! User updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM users WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Information</title>
</head>
<body>
    <h1>Edit User Information</h1>
    <form action="edit.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?php echo $row['first_name']; ?>" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?php echo $row['last_name']; ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo $row['email']; ?>" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" value="<?php echo $row['password']; ?>" required><br><br>

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="male" <?php if($row['gender'] == 'male') echo 'selected'; ?>>Male</option>
            <option value="female" <?php if($row['gender'] == 'female') echo 'selected'; ?>>Female</option>
        </select><br><br>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="enabled" <?php if($row['status'] == 'enabled') echo 'selected'; ?>>Enabled</option>
            <option value="disabled" <?php if($row['status'] == 'disabled') echo 'selected'; ?>>Disabled</option>
        </select><br><br>

        <label for="image">Image:</label>
        <input type="file" name="profile_image" id="profile_image"><br><br>

        <button type="submit" name="submit">Update</button>
    </form>
</body>
</html>
<?php
    } else {
        echo "User not found";
    }
}

$conn->close();
?>
