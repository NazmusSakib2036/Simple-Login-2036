<?php
ob_start(); // আউটপুট বাফারিং শুরু
session_start(); // সেশন শুরু
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <!-- Google Sign-In Platform Library -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p class="message">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        ?>
        <form action="process_login.php" method="POST">
            <div class="input-group">
                <label for="username_email">Username or Email:</label>
                <input type="text" id="username_email" name="username_email" required>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>

        <div class="social-login-divider">OR</div>

        <!-- Google Sign-In Button -->
        <div id="g_id_onload"
             data-client_id="950408503495-s96v40fehs80r4q6li7jqof47q8gtof9.apps.googleusercontent.com"
             data-callback="handleGoogleSignIn"
             data-auto_prompt="false">
        </div>
        <div class="g_id_signin"
             data-type="standard"
             data-size="large"
             data-theme="outline"
             data-text="sign_in_with"
             data-shape="rectangular"
             data-logo_alignment="left">
        </div>
    </div>

    <script>
        function handleGoogleSignIn(response) {
            const id_token = response.credential;

            fetch('google_callback.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_token=' + id_token
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = 'dashboard.php';
                } else {
                    alert('Google Sign-In failed: ' + data.message);
                    window.location.href = 'index.php';
                }
            })
            .catch(error => {
                console.error('Error during Google Sign-In fetch:', error);
                alert('An error occurred during Google Sign-In.');
                window.location.href = 'index.php';
            });
        }
    </script>
</body>
</html>
<?php
ob_end_flush(); // আউটপুট বাফারিং শেষ
?>