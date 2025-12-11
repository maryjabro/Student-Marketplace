<?php
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

$fullName = "";
$email = "";
$school = "";
$major = "";
$gradYear = "";
$accountType = "buyer_seller";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";
    $school = trim($_POST["school"] ?? "");
    $major = trim($_POST["major"] ?? "");
    $gradYear = trim($_POST["gradYear"] ?? "");
    $accountType = trim($_POST["accountType"] ?? "buyer_seller");
    $terms = isset($_POST["terms"]);

    if ($fullName === "") $errors[] = "Full name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";
    if (!$terms) $errors[] = "You must agree to the terms.";

    $gradYearValue = null;
    if ($gradYear !== "") {
        if (ctype_digit($gradYear)) {
            $gradYearValue = (int)$gradYear;
        } else {
            $errors[] = "Graduation year must be a number.";
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (full_name, email, password_hash, school, major, grad_year, account_type)
                 VALUES (:full_name, :email, :password_hash, :school, :major, :grad_year, :account_type)"
            );

            $stmt->execute([
                ":full_name" => $fullName,
                ":email" => $email,
                ":password_hash" => password_hash($password, PASSWORD_DEFAULT),
                ":school" => $school ?: null,
                ":major" => $major ?: null,
                ":grad_year" => $gradYearValue,
                ":account_type" => $accountType
            ]);

            $success = "Account created successfully!";
            $fullName = $email = $school = $major = $gradYear = "";
            $accountType = "buyer_seller";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Email is already registered.";
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
    .auth-form input,
    .auth-form select {
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
          <label for="fullName">Full Name</label>
          <input
            type="text"
            id="fullName"
            name="fullName"
            value="<?php echo htmlspecialchars($fullName); ?>"
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

        <div class="form-group">
          <label for="school">School (Optional)</label>
          <input
            type="text"
            id="school"
            name="school"
            value="<?php echo htmlspecialchars($school); ?>"
          />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="major">Major (Optional)</label>
            <input
              type="text"
              id="major"
              name="major"
              value="<?php echo htmlspecialchars($major); ?>"
            />
          </div>
          <div class="form-group">
            <label for="gradYear">Graduation Year (Optional)</label>
            <select id="gradYear" name="gradYear">
              <option value="">Select</option>
              <option value="2025" <?php if ($gradYear === "2025") echo "selected"; ?>>2025</option>
              <option value="2026" <?php if ($gradYear === "2026") echo "selected"; ?>>2026</option>
              <option value="2027" <?php if ($gradYear === "2027") echo "selected"; ?>>2027</option>
              <option value="2028" <?php if ($gradYear === "2028") echo "selected"; ?>>2028</option>
              <option value="2029" <?php if ($gradYear === "2029") echo "selected"; ?>>2029</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="accountType">Account Type</label>
          <select id="accountType" name="accountType">
            <option value="buyer_seller" <?php if ($accountType === "buyer_seller") echo "selected"; ?>>Buyer &amp; Seller</option>
            <option value="buyer" <?php if ($accountType === "buyer") echo "selected"; ?>>Buyer Only</option>
            <option value="seller" <?php if ($accountType === "seller") echo "selected"; ?>>Seller Only</option>
          </select>
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
