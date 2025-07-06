<?php
ob_start();
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p class="message">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        ?>
        <form action="process_register.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="input-group">
                <label for="profession_select">Profession:</label>
                <select id="profession_select" name="profession_selected">
                    <option value="">Select your profession</option>
                    <option value="Student">Student</option>
                    <option value="Teacher">Teacher</option>
                    <option value="Professor">Professor</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Engineer">Engineer</option>
                    <option value="Web Developer">Web Developer</option>
                    <option value="App Developer">App Developer</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="input-group" id="other_profession_group" style="display: none;">
                <label for="other_profession_input">Specify Profession:</label>
                <input type="text" id="other_profession_input" name="profession_other" placeholder="e.g., Designer">
            </div>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="index.html">Login here</a></p>
    </div>

    <script>
        const password = document.getElementById('password');
        const confirm_password = document.getElementById('confirm_password');

        function validatePassword(){
            if(password.value !== confirm_password.value) {
                confirm_password.setCustomValidity("Passwords Don't Match");
            } else {
                confirm_password.setCustomValidity('');
            }
        }

        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;

        const professionSelect = document.getElementById('profession_select');
        const otherProfessionGroup = document.getElementById('other_profession_group');
        const otherProfessionInput = document.getElementById('other_profession_input');

        professionSelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                otherProfessionGroup.style.display = 'block';
                otherProfessionInput.setAttribute('required', 'required');
            } else {
                otherProfessionGroup.style.display = 'none';
                otherProfessionInput.removeAttribute('required');
                otherProfessionInput.value = '';
            }
        });
    </script>
</body>
</html>
<?php
ob_end_flush();
?>