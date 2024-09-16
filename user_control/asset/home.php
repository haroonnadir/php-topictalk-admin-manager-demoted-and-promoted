<?php
session_start();
include "connection.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php'); // Redirect to login page if not logged in
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, bio, avatar, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($username, $bio, $avatar, $role);
$stmt->fetch();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);

    // Handle file upload
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($_FILES['avatar']['name']);
        $file_type = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_type, $allowed_types)) {
            // Move uploaded file to the target directory
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_file)) {
                $avatar = $upload_file;
            } else {
                $error_message = "Failed to upload avatar.";
            }
        } else {
            $error_message = "Only image files are allowed.";
        }
    }

    $update_sql = "UPDATE users SET bio = ?, avatar = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssi', $bio, $avatar, $user_id);
    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully.";
    } else {
        $error_message = "Failed to update profile.";
    }
    $update_stmt->close();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: user_login.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #003f5c, #665191, #f6b93b);
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 100vh;

        }
        header {
            background: #003f5c;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-info {
            margin-bottom: 20px;
        }
        .profile-info img {
            max-width: 150px;
            border-radius: 50%;
        }
        .profile-info h1 {
            margin-top: 0;
        }
        .profile-info p {
            font-size: 16px;
            line-height: 1.6;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form input[type="text"], form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        form input[type="file"] {
            margin: 10px 0;
        }
        form input:focus, form textarea:focus {
            border-color: #333;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }
        .update-button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #28a745;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .update-button:hover {
            background-color: #218838;
        }
        .logout-button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .logout-button:hover {
            background: #c82333;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        .admin-link {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .admin-link:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    </header>
    <div class="container">
        <div class="profile-info">
            <h1>Your Profile</h1>
            <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>
            <?php if (isset($success_message)): ?>
                <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($bio); ?></textarea>
                <label for="avatar">Avatar:</label>
                <input type="file" id="avatar" name="avatar">
                <button type="submit" class="update-button">Update Profile</button>
            </form>
        </div>
        <a href="?logout" class="logout-button">Logout</a>
        <?php if ($role === 'Admin'): ?>
            <a href="admin_dashboard.php" class="admin-link">Admin Dashboard</a>
        <?php endif; ?>
    </div>
</body>
</html>
