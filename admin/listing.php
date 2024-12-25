<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ayushiladani";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$url = parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST); 
$base_url = rtrim($url, '/').'/ayushiladani';

if(isset($conn)) {
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>";

        while($row = $result->fetch_assoc()) {
            $status_bg_color = ($row['status'] == 'enabled') ? 'green' : 'red';

            echo "<tr style='background-color: $status_bg_color'>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['first_name']}</td>";
            echo "<td>{$row['last_name']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['password']}</td>";
            echo "<td>{$row['gender']}</td>";
            echo "<td>{$row['status']}</td>";
            echo '<td><img src="'.$base_url.'/'.$row['profile_image'].'" alt="profile_image" width="100"></td>';

            echo "<td>
                    <a href='edit.php?id={$row['id']}'>Edit</a>
                    <form method='post'>
                        <input type='hidden' name='user_id' value='{$row['id']}'>
                        <button type='submit' name='delete'>Delete</button>
                    </form>
                </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No Users";
    }

    if (isset($_POST['delete']) && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
                                                   
        $sql_fetch_image = "SELECT profile_image FROM users WHERE id = ?";
        $stmt_fetch_image = $conn->prepare($sql_fetch_image);
        $stmt_fetch_image->bind_param("i", $user_id);
        $stmt_fetch_image->execute();
        $stmt_fetch_image->bind_result($profile_image);
        $stmt_fetch_image->fetch();
        $stmt_fetch_image->close();
       
        $sql_delete_user = "DELETE FROM users WHERE id = ?";
        $stmt_delete_user = $conn->prepare($sql_delete_user);
        $stmt_delete_user->bind_param("i", $user_id);
    
        if ($stmt_delete_user->execute()) {
        
            if (!empty($profile_image) && file_exists('../' . $profile_image)) {
                if (unlink('../' . $profile_image)) {
                    $_SESSION['delete_success'] = true;
                } else {
                    echo "Error unlinking image.";
                }
            } else {
                $_SESSION['delete_success'] = true;
            }
        } else {
            echo "Error deleting record: " . $stmt_delete_user->error;
        }
    
        $stmt_delete_user->close();
    }
   
    $conn->close();
}
?>
                                         