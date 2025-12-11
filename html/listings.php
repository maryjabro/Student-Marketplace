<?php
session_start();
?>

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

          <li class="user-badge">
            <span class="user-icon">👤</span>
            <span class="user-name">
              <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['email']); ?>
            </span>
          </li>

          <li><a href="logout.php">Logout</a></li>

        <?php else: ?>

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
      $title       = htmlspecialchars($row['title'], ENT_QUOTES);
      $description = htmlspecialchars($row['description'], ENT_QUOTES);
      $price       = number_format((float)$row['price'], 2);
      $date        = htmlspecialchars($row['date_posted']);

      if (!empty($row['img_path'])) {
        $imgPath = '../' . ltrim($row['img_path'], '/');
      } else {
        $imgPath = '../img/textbook.png';
      }
      $imgPathEscaped = htmlspecialchars($imgPath);

      echo '<div class="listing"
              onclick="openModal(
                \'' . $title . '\',
                \'' . $description . '\',
                \'' . $price . '\',
                \'' . $imgPathEscaped . '\'
              )">';

      echo '  <img src="' . $imgPathEscaped . '" alt="' . $title . '">';
      echo '  <h4>' . $title . '</h4>';
      echo '  <p>$' . $price . ' · Posted ' . $date . '</p>';
      echo '</div>';
    }
  }

  $conn->close();
?>
</section>

<!-- 🔹 MODAL (added) -->
<div id="listingModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>

    <img id="modalImage" alt="" class="modal-image">
    <h2 id="modalTitle"></h2>
    <p id="modalDescription"></p>
    <p><strong>Price:</strong> $<span id="modalPrice"></span></p>
  </div>
</div>

<footer>
  <p>&copy; 2025 Student Marketplace | Built for Students, by Students</p>
</footer>

<!-- 🔹 MODAL SCRIPT (added) -->
<script>
function openModal(title, description, price, imgPath) {
  document.getElementById("modalTitle").textContent = title;
  document.getElementById("modalDescription").textContent = description;
  document.getElementById("modalPrice").textContent = price;
  document.getElementById("modalImage").src = imgPath;

  document.getElementById("listingModal").style.display = "block";
}

function closeModal() {
  document.getElementById("listingModal").style.display = "none";
}

window.onclick = function(event) {
  const modal = document.getElementById("listingModal");
  if (event.target === modal) {
    modal.style.display = "none";
  }
};
</script>

</body>
</html>
