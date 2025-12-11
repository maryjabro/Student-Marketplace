<?php
// html/login.php

session_start();
require_once "db_config.php";

// For showing errors in the form
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        // Look up the user by email
$sql = "SELECT user_id, email, full_name, password FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $error = "Something went wrong. Please try again later.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user   = $result->fetch_assoc();

            if ($user) {
                $storedPassword = $user['password'];
                $passwordMatch  = false;

                // Case 1: password was stored using password_hash()
                if (password_verify($password, $storedPassword)) {
                    $passwordMatch = true;
                }
                // Case 2: plain-text storage (for simple class projects)
                elseif (hash_equals($storedPassword, $password)) {
                    $passwordMatch = true;
                }

                if ($passwordMatch) {
                    // Successful login — set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email']   = $user['email'];
$_SESSION['full_name'] = $user['full_name'];

                    // Redirect after login (success case)
                    header("Location: listings.php");
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Student Marketplace</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <div class="navbar">
    <h1 class="logo">🎓 Student Marketplace</h1>

    <nav>
      <ul>
        <li><a href="../index.html" class="active">Home</a></li>
        <li><a href="listings.php">Browse Listings</a></li>
        <li><a href="sell.php">Sell Item</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          
          <!-- USER BADGE (icon + name) -->
          <li class="user-badge">
            <span class="user-icon">👤</span>
            <span class="user-name">
              <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['email']); ?>
            </span>
          </li>

          <!-- LOGOUT BUTTON -->
          <li><a href="logout.php">Logout</a></li>

        <?php else: ?>
          
          <!-- SHOW THESE ONLY WHEN LOGGED OUT -->
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>

        <?php endif; ?>
      </ul>
    </nav>

  </div>
</header>


  <section class="form-section">
    <h2>Login to Your Account</h2>

    <?php if ($error): ?>
      <p class="error" style="color:red; margin-bottom: 10px;">
        <?php echo htmlspecialchars($error); ?>
      </p>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="login.php">
      <label for="email">Email:</label>
      <input
        type="email"
        id="email"
        name="email"
        placeholder="student@school.edu"
        required
        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
      >

      <label for="password">Password:</label>
      <input
        type="password"
        id="password"
        name="password"
        placeholder="Enter your password"
        required
      >

      <button type="submit" class="btn">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
  </section>

  <footer>
    <p>&copy; 2025 Student Marketplace | Built for Students, by Students</p>
  </footer>
</body>
</html>
