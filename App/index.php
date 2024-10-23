<?php
session_start();
include 'db.php'; // Database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin' || $user['role'] == 'janitor') {
                header("Location: mainpage.php");
                exit();
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="RajaG.css"> <!-- Link to your CSS file -->
</head>
<body>

    <div class="container">
        <div class="form-container sign-in">
            <form action="index.php" method="POST">
                <h2>Sign in</h2>
                <?php if (isset($error)) echo '<p style="color:red;">' . $error . '</p>'; ?>
                
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                
                <button type="submit">Login</button>
                
            </form>
        </div>
        
        <!-- Add a toggle panel if you want to include a Sign-Up option or additional content -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h2>Hello, UMN Staff!</h2>
                    <p>Please Enter Your Login Credentials To Access The Main Page</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
