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
