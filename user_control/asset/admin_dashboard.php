<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php'); // Redirect to login page if not logged in or not an admin
    exit();
}

include "connection.php";

// Fetch user count
$user_count_sql = "SELECT COUNT(*) FROM users";
$user_count_result = $conn->query($user_count_sql);
$user_count = $user_count_result->fetch_row()[0];

// Fetch latest user registrations
$latest_users_sql = "SELECT username, created_at FROM users ORDER BY created_at DESC LIMIT 5";
$latest_users_result = $conn->query($latest_users_sql);

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
    </header>
    <div class="container">
        <div class="stats">
            <h2>Statistics</h2>
            <p><strong>Total Users:</strong> <?php echo htmlspecialchars($user_count); ?></p>
        </div>
        <div class="latest-users">
            <h2>Latest Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $latest_users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="?logout" class="logout-button">Logout</a>
    </div>
</body>
</html>
