<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "topic_talk";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$feedback_message = ''; // Variable to store feedback messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $bio = $_POST['bio'];

    // Handle file upload
    $avatar = NULL;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['avatar']['name']);
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
            $avatar = $uploadFile;
        } else {
            $feedback_message = "Failed to upload avatar.";
        }
    }

    if (empty($feedback_message)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, password_hash, bio, avatar) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param('ssss', $username, $passwordHash, $bio, $avatar);

        if ($stmt->execute()) {
            $feedback_message = "Registration successful.";
        } else {
            $feedback_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* General body styling */
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

        /* Header styling */
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

        /* Container styling */
        .container {
            width: 80%;
            max-width: 500px;
            margin: 80px auto 20px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Content styling */
        .content h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        /* Form styling */
        form {
            display: flex;
            flex-direction: column;
        }

        /* Input fields */
        input[type="text"], input[type="password"], input[type="email"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        input:focus, textarea:focus {
            border-color: #333;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        /* Button styling */
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #55cc43;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #28a745;
            color: #fff;
        }

        /* Feedback message styling */
        .feedback-message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Login link styling */
        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #333;
            text-decoration: none;
            font-size: 16px;
            display: inline-block;
            padding: 10px;
            background-color: #c4babb;
            color: #000;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s, text-decoration 0.3s;
        }

        .login-link a:hover {
            background-color: #b86d75;
            text-decoration: underline;
            text-decoration-color: #28a745;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 95%;
            }

            input[type="text"], input[type="password"], input[type="email"], textarea {
                font-size: 14px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>User Register</h1>
    </header>
    <div class="container">
        <div class="content">
            <h2>Create Your Account</h2>
            <?php if (!empty($feedback_message)): ?>
                <div class="feedback-message <?php echo strpos($feedback_message, 'successful') !== false ? 'success' : 'error'; ?>">
                    <?php echo $feedback_message; ?>
                </div>
            <?php endif; ?>
            <form action="user_register.php" method="POST" enctype="multipart/form-data">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio"></textarea>

                <label for="avatar">Avatar:</label>
                <input type="file" id="avatar" name="avatar">

                <button type="submit">Register</button>
            </form>
            <div class="login-link">
                <a href="./user_login.php">You already have an account?</a>
            </div>
        </div>
    </div>
</body>
</html>
