<?php
session_start();
include 'connection.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: user_login.php'); // Redirect to login page if not logged in or not an admin
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);

    // Validate input
    if ($user_id && in_array($role, ['Moderator', 'Regular User'])) {
        // Check if the role update would violate the unique admin constraint
        if ($role === 'Admin') {
            $_SESSION['error_message'] = "Cannot assign role 'Admin'.";
        } else {
            // Update user role
            $update_sql = "UPDATE users SET role = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param('si', $role, $user_id);

            if ($update_stmt->execute()) {
                $_SESSION['success_message'] = "Role updated successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to update role.";
            }
            $update_stmt->close();
        }
    } else {
        $_SESSION['error_message'] = "Invalid input.";
    }

    $conn->close();
    header('Location: admin_dashboard.php');
    exit();
}
