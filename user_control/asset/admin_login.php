<?php
session_start();
include 'connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: user_login.php'); // Redirect to login page if not logged in or not an admin
    exit();
}

// Fetch all users except the current admin and those with the "Admin" role
$sql = "SELECT id, username FROM users WHERE role <> 'Admin'";
$result = $conn->query($sql);
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #003f5c, #665191, #f6b93b);
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 100vh;

        }
        /* Header Styles */
        header {
            background: #003f5c;
            color: #fff;
            padding: 15px;
            text-align: center;
        }

        header h1 {
            margin: 0;
        }

        header nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        /* Main Content Styles */
        main {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        main h2 {
            margin-top: 0;
            font-size: 24px;
            border-bottom: 2px solid #003f5c;
            padding-bottom: 10px;
            color: #003f5c;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }

        form label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        form select {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        form button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        form button:hover {
            background: #0056b3;
        }
    </style>
    <script>
        function showRoleAlert() {
            var roleSelect = document.getElementById('role');
            var selectedRole = roleSelect.options[roleSelect.selectedIndex].text;
            var roleMessage = selectedRole === 'Moderator' ? 'The user has been promoted to Moderator.' : 'The user has been demoted to Regular User.';
            alert(roleMessage);
        }
    </script>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="home.php">Profile</a> | <a href="user_login.php?logout">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Manage Users</h2>
        <form action="update_role.php" method="post" onsubmit="showRoleAlert()">
            <label for="user_id">Select User:</label>
            <select id="user_id" name="user_id">
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo htmlspecialchars($user['id']); ?>">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="role">Change Role:</label>
            <select id="role" name="role">
                <option value="Moderator">Moderator</option>
                <option value="Regular User">Regular User</option>
            </select>
            <button type="submit">Update Role</button>
        </form>
    </main>
</body>
</html>
