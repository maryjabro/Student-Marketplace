<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Listings | Student Marketplace</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
  <div class="navbar">
    <h1 class="logo">🎓 Student Marketplace</h1>

    <nav>
      <ul>
        <li><a href="../index.php" class="active">Home</a></li>
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

          <!-- LOGOUT -->
          <li><a href="logout.php">Logout</a></li>

        <?php else: ?>

          <!-- SHOWN ONLY WHEN LOGGED OUT -->
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>

        <?php endif; ?>
      </ul>
    </nav>

  </div>
</header>


  <section class="hero">
    <h2>All Listings</h2>
    <p>Explore items your classmates are selling on campus.</p>
  </section>

  <section class="listing-grid">
    <?php
      require_once "db_config.php";

      // Get all listings, newest first
      $sql = "SELECT listing_id, title, description, price, img_path, date_posted 
              FROM listings
              ORDER BY listing_id DESC";

      $result = $conn->query($sql);

      if (!$result) {
        echo "<p>Failed to load listings. Please try again later.</p>";
      } elseif ($result->num_rows === 0) {
        echo "<p class=\"empty-message\">No listings yet. Be the first to <a href=\"sell.php\">list an item</a>!</p>";
      } else {
        while ($row = $result->fetch_assoc()) {
          $title       = htmlspecialchars($row['title']);
          $description = htmlspecialchars($row['description']);
          $price       = number_format((float)$row['price'], 2);
          $date        = htmlspecialchars($row['date_posted']);

          // Build image path:
          // Assume img_path is like "img/laptop.png" or "img/desk.png"
          if (!empty($row['img_path'])) {
            $imgPath = '../' . ltrim($row['img_path'], '/');
          } else {
            // Fallback image
            $imgPath = '../img/textbook.png';
          }
          $imgPathEscaped = htmlspecialchars($imgPath);

          echo '<div class="listing">';
          echo '  <img src="' . $imgPathEscaped . '" alt="' . $title . '">';
          echo '  <h4>' . $title . '</h4>';
          echo '  <p>$' . $price . ' · Posted ' . $date . '</p>';
          echo '</div>';
        }
      }

      $conn->close();
    ?>
  </section>

  <footer>
    <p>&copy; 2025 Student Marketplace | Built for Students, by Students</p>
  </footer>
</body>
</html>
