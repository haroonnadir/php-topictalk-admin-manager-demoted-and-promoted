<?php
include "connection.php";

session_start();

$login_error = ""; // Initialize login error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if ($username && $password) {
        // Prepare and execute SQL query
        $sql = "SELECT id, password_hash, role FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $passwordHash, $role);
                $stmt->fetch();

                // Verify password
                if (password_verify($password, $passwordHash)) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['role'] = $role;
                    header('Location: home.php'); // Redirect to home page
                    exit(); // Ensure the script stops after redirect
                } else {
                    $login_error = "Invalid username or password.";
                }
            } else {
                $login_error = "Invalid username or password.";
            }

            $stmt->close();
        } else {
            $login_error = "Database error: Could not prepare statement.";
        }
    } else {
        $login_error = "Please enter both username and password.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body, h1, h2, p, form, input, button {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #003f5c, #665191, #f6b93b);
            background-repeat: no-repeat;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        header {
            color: #fff;
            padding: 15px 0;
            text-align: center;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        header h1 {
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 500px;
            padding-top: 60px;
        }

        .content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"], input[type="password"], input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        input:focus, textarea:focus {
            border-color: #333;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        .buttons-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .buttons-container button, .buttons-container .register-link a {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 0 5px;
        }

        .buttons-container button {
            background-color: #28a745;
            color: #fff;
        }

        .buttons-container button:hover {
            background-color: #218838;
        }

        .buttons-container .register-link a {
            background-color: #c4babb;
            color: #000;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s, text-decoration 0.3s;
        }

        .buttons-container .register-link a:hover {
            background-color: #b86d75;
            text-decoration: underline;
            text-decoration-color: #28a745;
        }

        /* General button styling */
        button {
            width: 95%;
            margin: 12px;
            padding: 10px 20px;
            background-color: red;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            align-items: center;
        }

        /* Link styling within button */
        button a {
            text-decoration: none;
            color: #fff;
            font-size: 1rem;
            font-weight: bold;
            display: inline-block;
        }

        /* Hover effect */
        button:hover {
            background-color: orange;
        }

        button:hover a {
            color: #fff;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
        }

        @media (max-width: 480px) {
            .content {
                padding: 15px;
            }

            input[type="text"], input[type="password"], input[type="email"], textarea {
                font-size: 14px;
            }

            .buttons-container button, .buttons-container .register-link a {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>User Login</h1>
    </header>
    <div class="container">
        <div class="content">
            <h2>Login to Your Account</h2>
            <?php if (!empty($login_error)) { echo '<p style="color: red; text-align: center;">' . htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8') . '</p>'; } ?>
            <form action="" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required aria-label="Username">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required aria-label="Password">
                <div class="buttons-container">
                    <button type="submit">Login</button>
                    <div class="register-link">
                        <a href="./user_register.php">Register</a>
                    </div>
                </div>
                <div>
                    <button type="button"><a href="../user.html">Go-Back</a></button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
