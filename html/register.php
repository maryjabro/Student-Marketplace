<?php
// --- DATABASE SETTINGS (change if needed) ---
$db_host = "localhost";
$db_name = "student_marketplace";
$db_user = "root";
$db_pass = "password";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed.");
}

$errors = [];
$success = "";

$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name            = trim($_POST["name"] ?? "");
    $email           = trim($_POST["email"] ?? "");
    $password        = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";
    $terms           = isset($_POST["terms"]);

    if ($name === "") $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";
    if (!$terms) $errors[] = "You must agree to the terms.";

    if (empty($errors)) {
        try {
            // ⚠ This matches your table: user_id, name, email, password
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (:name, :email, :password)"
            );

            // NOTE: Your column is varchar(30), so this stores plain text.
            // For a real project, you should change the column to varchar(255)
            // and use password_hash() instead.
            $stmt->execute([
                ":name"     => $name,
                ":email"    => $email,
                ":password" => $password
            ]);

            $success = "Account created successfully!";
            $name = $email = "";
        } catch (PDOException $e) {
            // If you set email as UNIQUE, this will catch duplicates
            if ($e->getCode() == 23000) {
                $errors[] = "This email is already registered.";
            } else {
                $errors[] = "Error creating account.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | Student Marketplace</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .auth-wrapper {
      min-height: calc(100vh - 140px);
      display: flex;
      align-items: center;
      justify-content: center;
      background: #eef3ff;
      padding: 40px 16px;
    }
    .auth-card {
      background: #ffffff;
      max-width: 480px;
      width: 100%;
      padding: 24px 24px 28px;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
    }
    .auth-card h2 {
      margin-bottom: 6px;
      font-size: 1.6rem;
      color: #0f172a;
    }
    .auth-subtitle {
      margin-bottom: 18px;
      color: #4b5563;
      font-size: 0.95rem;
    }
    .auth-message {
      padding: 10px 12px;
      border-radius: 8px;
      font-size: 0.9rem;
      margin-bottom: 14px;
    }
    .auth-error {
      background: #fef2f2;
      color: #b91c1c;
      border: 1px solid #fecaca;
    }
    .auth-success {
      background: #ecfdf3;
      color: #166534;
      border: 1px solid #bbf7d0;
    }
    .auth-form .form-group {
      margin-bottom: 12px;
    }
    .auth-form label {
      display: block;
      margin-bottom: 4px;
      font-weight: 500;
      color: #111827;
      font-size: 0.9rem;
    }
    .auth-form input {
      width: 100%;
      padding: 9px 10px;
      border-radius: 8px;
      border: 1px solid #cbd5e1;
      font-size: 0.9rem;
    }
    .auth-form .form-row {
      display: flex;
      gap: 10px;
    }
    .auth-form .form-row .form-group {
      flex: 1;
      margin-bottom: 0;
    }
    .auth-form .checkbox-group {
      display: flex;
      align-items: center;
      gap: 7px;
      font-size: 0.88rem;
    }
    .auth-switch {
      margin-top: 10px;
      font-size: 0.9rem;
      color: #4b5563;
      text-align: center;
    }
    .auth-switch a {
      color: #0f8bff;
      font-weight: 600;
      text-decoration: none;
    }
    @media (max-width: 600px) {
      .auth-card { padding: 20px 18px 24px; }
      .auth-form .form-row { flex-direction: column; }
    }
  </style>
</head>
<body>
  <header>
    <div class="navbar">
      <h1 class="logo">🎓 Student Marketplace</h1>
      <nav>
        <ul>
          <li><a href="../index.html">Home</a></li>
          <li><a href="listings.php">Browse Listings</a></li>
          <li><a href="sell.php">Sell Item</a></li>
          <li><a href="login.html">Login</a></li>
          <li><a href="register.php" class="active">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="auth-wrapper">
    <section class="auth-card">
      <h2>Create Your Account</h2>
      <p class="auth-subtitle">Join the campus marketplace to buy, sell, and trade with fellow students.</p>

      <?php if (!empty($errors)): ?>
        <div class="auth-message auth-error">
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php elseif ($success): ?>
        <div class="auth-message auth-success">
          <?php echo htmlspecialchars($success); ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="auth-form">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($name); ?>"
            required
          />
        </div>

        <div class="form-group">
          <label for="email">School Email</label>
          <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($email); ?>"
            required
          />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              required
            />
          </div>
          <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input
              type="password"
              id="confirmPassword"
              name="confirmPassword"
              required
            />
          </div>
        </div>

        <div class="form-group checkbox-group">
          <label>
            <input type="checkbox" name="terms" required />
            I agree to the terms and privacy policy.
          </label>
        </div>

        <button type="submit" class="btn">Create Account</button>

        <p class="auth-switch">
          Already have an account?
          <a href="login.html">Log in</a>
        </p>
      </form>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Student Marketplace | Built for Students, by Students</p>
  </footer>
</body>
</html>
