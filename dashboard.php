<?php
ob_start();
session_start();
include 'db_config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

$profile_image_src = "uploads/default.png";
if (!empty($_SESSION['profile_image'])) {
    $profile_image_src = "uploads/" . htmlspecialchars($_SESSION['profile_image']);
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$current_user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, profession, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

if (!$user_data) {
    header("Location: logout.php");
    exit();
}

$_SESSION['username'] = $user_data['username'];
$_SESSION['email'] = $user_data['email'];
$_SESSION['profession'] = $user_data['profession'];
$_SESSION['profile_image'] = $user_data['profile_image'];

$profile_image_src = "uploads/default.png";
if (!empty($_SESSION['profile_image'])) {
    $profile_image_src = "uploads/" . htmlspecialchars($_SESSION['profile_image']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 20px auto; 
            border: 4px solid #007bff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-content h3 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 1.8em;
        }

        .modal-content .input-group {
            margin-bottom: 15px;
        }

        .modal-content button[type="submit"] {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to your Dashboard, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <img src="<?php echo $profile_image_src; ?>" alt="Profile Image" class="profile-img" id="profileImageDisplay">
        <p>Your Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <?php if (!empty($_SESSION['profession'])): ?>
            <p>Profession: <?php echo htmlspecialchars($_SESSION['profession']); ?></p>
        <?php endif; ?>

        <button onclick="openEditModal()" class="edit-profile-btn">Edit Profile</button>
        <p><a href="logout.php">Logout</a></p>
    </div>


    <div id="editProfileModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditModal()">&times;</span>
            <h3>Edit Profile</h3>
            <form action="process_edit_profile.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="edit_username">Username:</label>
                    <input type="text" id="edit_username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_password">New Password (leave blank to keep current):</label>
                    <input type="password" id="edit_password" name="new_password">
                </div>
                <div class="input-group">
                    <label for="edit_confirm_password">Confirm New Password:</label>
                    <input type="password" id="edit_confirm_password" name="confirm_new_password">
                </div>
                <div class="input-group">
                    <label for="edit_profession_select">Profession:</label>
                    <select id="edit_profession_select" name="profession_selected">
                        <option value="">Select your profession</option>
                        <option value="Student" <?php echo ($_SESSION['profession'] == 'Student') ? 'selected' : ''; ?>>Student</option>
                        <option value="Teacher" <?php echo ($_SESSION['profession'] == 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
                        <option value="Professor" <?php echo ($_SESSION['profession'] == 'Professor') ? 'selected' : ''; ?>>Professor</option>
                        <option value="Doctor" <?php echo ($_SESSION['profession'] == 'Doctor') ? 'selected' : ''; ?>>Doctor</option>
                        <option value="Engineer" <?php echo ($_SESSION['profession'] == 'Engineer') ? 'selected' : ''; ?>>Engineer</option>
                        <option value="Web Developer" <?php echo ($_SESSION['profession'] == 'Web Developer') ? 'selected' : ''; ?>>Web Developer</option>
                        <option value="App Developer" <?php echo ($_SESSION['profession'] == 'App Developer') ? 'selected' : ''; ?>>App Developer</option>
                        <option value="Other" <?php echo (!in_array($_SESSION['profession'], ['Student', 'Teacher', 'Professor', 'Doctor', 'Engineer', 'Web Developer', 'App Developer']) && !empty($_SESSION['profession'])) ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="input-group" id="edit_other_profession_group" style="display: <?php echo (!in_array($_SESSION['profession'], ['Student', 'Teacher', 'Professor', 'Doctor', 'Engineer', 'Web Developer', 'App Developer']) && !empty($_SESSION['profession'])) ? 'block' : 'none'; ?>;">
                    <label for="edit_other_profession_input">Specify Profession:</label>
                    <input type="text" id="edit_other_profession_input" name="profession_other" placeholder="e.g., Designer" value="<?php echo (!in_array($_SESSION['profession'], ['Student', 'Teacher', 'Professor', 'Doctor', 'Engineer', 'Web Developer', 'App Developer']) && !empty($_SESSION['profession'])) ? htmlspecialchars($_SESSION['profession']) : ''; ?>">
                </div>
                <div class="input-group">
                    <label for="edit_profile_image">New Profile Image (Optional):</label>
                    <input type="file" id="edit_profile_image" name="profile_image" accept="image/*">
                </div>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        const editProfileModal = document.getElementById('editProfileModal');
        const profileImageDisplay = document.getElementById('profileImageDisplay'); 
        const editProfessionSelect = document.getElementById('edit_profession_select');
        const editOtherProfessionGroup = document.getElementById('edit_other_profession_group');
        const editOtherProfessionInput = document.getElementById('edit_other_profession_input');
        const editPassword = document.getElementById('edit_password');
        const editConfirmPassword = document.getElementById('edit_confirm_password');

        function openEditModal() {
            editProfileModal.style.display = 'flex'; 
            editPassword.value = '';
            editConfirmPassword.value = '';
            editPassword.setCustomValidity('');
            editConfirmPassword.setCustomValidity('');
        }

        function closeEditModal() {
            editProfileModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == editProfileModal) {
                editProfileModal.style.display = 'none';
            }
        }

        function validateEditPassword(){
            if(editPassword.value !== editConfirmPassword.value) {
                editConfirmPassword.setCustomValidity("Passwords Don't Match");
            } else {
                editConfirmPassword.setCustomValidity('');
            }
        }
        editPassword.onchange = validateEditPassword;
        editConfirmPassword.onkeyup = validateEditPassword;

        editProfessionSelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                editOtherProfessionGroup.style.display = 'block';
                editOtherProfessionInput.setAttribute('required', 'required');
            } else {
                editOtherProfessionGroup.style.display = 'none';
                editOtherProfessionInput.removeAttribute('required');
                editOtherProfessionInput.value = '';
            }
        });

        window.addEventListener('load', function() {
            editProfessionSelect.dispatchEvent(new Event('change'));
        });

    </script>
</body>
</html>
<?php
ob_end_flush();
?>