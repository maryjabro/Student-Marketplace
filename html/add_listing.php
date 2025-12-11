<?php
// html/add_listing.php

require_once "db_config.php";

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: sell.php");
    exit;
}

// 1. Get and sanitize form fields
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price       = $_POST['price'] ?? '';
$seller_id   = 1; // Temporary hardcoded seller (replace with logged-in user later)

// Validate required fields
if ($title === '' || $description === '' || $price === '') {
    die("Please fill in all required fields. <a href='sell.php'>Go back</a>");
}

if (!is_numeric($price)) {
    die("Price must be a number. <a href='sell.php'>Go back</a>");
}
$price = (float)$price;

// 2. Handle optional image upload
$img_path = null; // The path stored in the database (e.g. img/filename123.png)

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading image. Please try again. <a href='sell.php'>Go back</a>");
    }

    // Directory where images will be saved
    $uploadDir = __DIR__ . '/../img/';

    // Create directory if missing
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Extract file info
    $originalName = $_FILES['image']['name'];
    $tmpPath      = $_FILES['image']['tmp_name'];
    $extension    = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Allowed image types
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($extension, $allowed)) {
        die("Unsupported image type. Allowed: JPG, PNG, GIF, WEBP. <a href='sell.php'>Go back</a>");
    }

    // Sanitize filename + make unique
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
    $newName  = $safeBase . '_' . time() . '.' . $extension;

    // Full filesystem path
    $targetPath = $uploadDir . $newName;

    // Move file from temp to /img/
    if (!move_uploaded_file($tmpPath, $targetPath)) {
        die("Failed to save uploaded image. <a href='sell.php'>Go back</a>");
    }

    // Save relative path for DB
    $img_path = 'img/' . $newName;
}

// 3. Generate today's date for DB
$date_posted = date('Y-m-d');

// 4. Insert into database
$sql = "INSERT INTO listings (title, description, price, img_path, seller_id, date_posted)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters (s = string, d = double, i = int)
$stmt->bind_param(
    "ssdsis",
    $title,
    $description,
    $price,
    $img_path,
    $seller_id,
    $date_posted
);

// Execute insertion
if ($stmt->execute()) {
    // Success HTML response
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Listing Posted</title>
      <link rel="stylesheet" href="styles.css">
    </head>
    <body>
      <section class="form-section" style="text-align:center;">
        <h2>🎉 Your listing has been posted!</h2>

        <p><strong><?php echo htmlspecialchars($title); ?></strong>  
        has been added for  
        <strong>$<?php echo number_format($price, 2); ?></strong>.</p>

        <?php if ($img_path): ?>
            <img src="../<?php echo htmlspecialchars($img_path); ?>" 
                 alt="Uploaded Image" 
                 style="max-width:300px;margin-top:15px;border-radius:8px;">
        <?php endif; ?>

        <div style="margin-top:25px;">
            <a href="listings.php" class="btn">View Listings</a><br><br>
            <a href="sell.php">Post another item</a>
        </div>
      </section>
    </body>
    </html>
    <?php

} else {
    echo "Error inserting listing: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
