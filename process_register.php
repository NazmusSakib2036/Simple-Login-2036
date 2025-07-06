<?php
ob_start();
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $profession = NULL;

    if (isset($_POST['profession_selected'])) {
        $selected_profession = $_POST['profession_selected'];
        if ($selected_profession === 'Other' && isset($_POST['profession_other'])) {
            $profession = trim($_POST['profession_other']);
            if (empty($profession)) {
                $_SESSION['message'] = "Please specify your profession if you selected 'Other'.";
                header("Location: register.php");
                exit();
            }
        } else if (!empty($selected_profession)) {
            $profession = $selected_profession;
        }
    }

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }

    if (strlen($password) < 6) {
        $_SESSION['message'] = "Password must be at least 6 characters long.";
        header("Location: register.php");
        exit();
    }

    $plain_password = $password;

    $profile_image_path = 'default.png'; 

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, profession, profile_image) VALUES (?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("sssss", $username, $email, $plain_password, $profession, $profile_image_path);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful! You can now login.";
        header("Location: index.html");
        exit();
    } else {
        if ($conn->errno == 1062) {
            $_SESSION['message'] = "Username or Email already exists.";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
        header("Location: register.php");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: register.php");
    exit();
}
?>
<?php
ob_end_flush();
?>