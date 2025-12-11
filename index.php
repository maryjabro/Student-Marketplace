<?php
require_once "html/db_config.php";

// Fetch 3 most recent listings
$sql = "SELECT title, price, img_path FROM listings ORDER BY listing_id DESC LIMIT 3";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Marketplace</title>
  <link rel="stylesheet" href="html/styles.css" />
</head>
<body>
  <header>
    <div class="navbar">
      <h1 class="logo">🎓 Student Marketplace</h1>
      <nav>
        <ul>
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="html/listings.php">Browse Listings</a></li>
          <li><a href="html/sell.php">Sell Item</a></li>
          <li><a href="html/login.php">Login</a></li>
          <li><a href="html/register.php">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="hero">
    <h2>Buy, Sell, and Trade with Fellow Students</h2>
    <p>Find affordable textbooks, electronics, dorm essentials, and more from your campus community.</p>
    <a href="html/listings.php" class="btn">Start Browsing</a>
  </section>

  <section class="categories">
    <h3>Popular Categories</h3>
    <div class="category-grid">
      <div class="category-card">
        <img src="img/books.png" alt="Books" />
        <h4>Books & Supplies</h4>
      </div>
      <div class="category-card">
        <img src="img/eletronics.png" alt="Electronics" />
        <h4>Electronics</h4>
      </div>
      <div class="category-card">
        <img src="img/furniture.png" alt="Furniture" />
        <h4>Furniture</h4>
      </div>
      <div class="category-card">
        <img src="img/clothing.png" alt="Clothing" />
        <h4>Clothing</h4>
      </div>
    </div>
  </section>

  <section class="featured">
    <h3>Featured Listings</h3>
    <div class="listing-grid">
      <?php
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $title = htmlspecialchars($row['title']);
              $price = number_format((float)$row['price'], 2);
              $imgPath = !empty($row['img_path']) ? $row['img_path'] : 'img/textbook.png';
              $imgPathEscaped = htmlspecialchars($imgPath);
              ?>
              <div class="listing">
                <img src="<?php echo $imgPathEscaped; ?>" alt="<?php echo $title; ?>" />
                <h4><?php echo $title; ?></h4>
                <p>$<?php echo $price; ?></p>
              </div>
              <?php
          }
      } else {
          echo '<p>No listings yet — be the first to post!</p>';
      }
      ?>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Student Marketplace | Built for Students, by Students</p>
  </footer>
</body>
</html>
