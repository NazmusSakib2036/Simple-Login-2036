<?php
ob_start();
session_start();
include 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['message'] = "Please log in to edit your profile.";
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    $new_profession = NULL;
    if (isset($_POST['profession_selected'])) {
        $selected_profession = $_POST['profession_selected'];
        if ($selected_profession === 'Other' && isset($_POST['profession_other'])) {
            $new_profession = trim($_POST['profession_other']);
            if (empty($new_profession)) {
                $_SESSION['message'] = "Please specify your profession if you selected 'Other'.";
                header("Location: dashboard.php");
                exit();
            }
        } else if (!empty($selected_profession)) {
            $new_profession = $selected_profession;
        }
    }

    $update_fields = [];
    $bind_types = '';
    $bind_params = [];

    if ($new_username !== $_SESSION['username']) {
        $stmt_check_username = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt_check_username->bind_param("si", $new_username, $user_id);
        $stmt_check_username->execute();
        $result_check_username = $stmt_check_username->get_result();
        if ($result_check_username->num_rows > 0) {
            $_SESSION['message'] = "Username already exists. Please choose a different one.";
            header("Location: dashboard.php");
            exit();
        }
        $stmt_check_username->close();

        $update_fields[] = "username = ?";
        $bind_types .= "s";
        $bind_params[] = $new_username;
        $_SESSION['username'] = $new_username; 
    }

    if ($new_email !== $_SESSION['email']) {
        $stmt_check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt_check_email->bind_param("si", $new_email, $user_id);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();
        if ($result_check_email->num_rows > 0) {
            $_SESSION['message'] = "Email already exists. Please use a different email.";
            header("Location: dashboard.php");
            exit();
        }
        $stmt_check_email->close();

        $update_fields[] = "email = ?";
        $bind_types .= "s";
        $bind_params[] = $new_email;
        $_SESSION['email'] = $new_email; 
    }

    if (!empty($new_password)) {
        if ($new_password !== $confirm_new_password) {
            $_SESSION['message'] = "New passwords do not match!";
            header("Location: dashboard.php");
            exit();
        }
        if (strlen($new_password) < 6) {
            $_SESSION['message'] = "New password must be at least 6 characters long.";
            header("Location: dashboard.php");
            exit();
        }
        $update_fields[] = "password = ?";
        $bind_types .= "s";
        $bind_params[] = $new_password; 
    }

    if ($new_profession !== $_SESSION['profession']) {
        $update_fields[] = "profession = ?";
        $bind_types .= "s";
        $bind_params[] = $new_profession;
        $_SESSION['profession'] = $new_profession; 
    }

    $current_profile_image = $_SESSION['profile_image'];
    $new_profile_image_path = $current_profile_image; 

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['profile_image']['tmp_name'];
        $file_name = $_FILES['profile_image']['name'];
        $file_size = $_FILES['profile_image']['size'];
        $file_type = $_FILES['profile_image']['type'];

        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $_SESSION['message'] = "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
            header("Location: dashboard.php");
            exit();
        }
        if ($file_size > 5 * 1024 * 1024) { 
            $_SESSION['message'] = "File size exceeds limit (5MB).";
            header("Location: dashboard.php");
            exit();
        }

        $unique_file_name = uniqid('profile_edit_', true) . '.' . $file_extension;
        $upload_dir = 'uploads/';
        $target_file = $upload_dir . $unique_file_name;

        if (move_uploaded_file($file_tmp_name, $target_file)) {
            $new_profile_image_path = $unique_file_name;

            if (!empty($current_profile_image) && $current_profile_image !== 'default.png' && file_exists($upload_dir . $current_profile_image)) {
                unlink($upload_dir . $current_profile_image);
            }
            $update_fields[] = "profile_image = ?";
            $bind_types .= "s";
            $bind_params[] = $new_profile_image_path;
            $_SESSION['profile_image'] = $new_profile_image_path;
        } else {
            $_SESSION['message'] = "Error uploading new profile image. Please try again.";
            header("Location: dashboard.php");
            exit();
        }
    }


    if (empty($update_fields)) {
        $_SESSION['message'] = "No changes submitted.";
        header("Location: dashboard.php");
        exit();
    }

    $sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = ?";
    $bind_types .= "i"; 
    $bind_params[] = $user_id; 

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['message'] = "Database error: " . $conn->error;
        header("Location: dashboard.php");
        exit();
    }

    call_user_func_array([$stmt, 'bind_param'], array_merge([$bind_types], $bind_params));

    if ($stmt->execute()) {
        $_SESSION['message'] = "Profile updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: dashboard.php");
    exit();

} else {
    header("Location: dashboard.php");
    exit();
}
?>
<?php
ob_end_flush();
?>