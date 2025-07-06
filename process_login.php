<?php
ob_start();
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = $_POST['username_email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, email, password, profession, profile_image FROM users WHERE username = ? OR email = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['profession'] = $user['profession'];
            $_SESSION['profile_image'] = $user['profile_image'];

            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = "Invalid username/email or password.";
            header("Location: index.html");
            exit(); 
        }
    } else {
        $_SESSION['message'] = "Invalid username/email or password.";
        header("Location: index.html");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.html");
    exit();
}
?>
<?php
ob_end_flush();
?>