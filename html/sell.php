<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sell Item | Student Marketplace</title>
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
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="hero">
    <h2>List an Item for Sale</h2>
    <p>Fill out the form below to post your listing.</p>
  </section>

  <section class="form-section">
    <!-- Upload-enabled form -->
    <form class="sell-form" action="add_listing.php" method="POST" enctype="multipart/form-data">

      <label for="title">Item Name:</label>
      <input type="text" id="title" name="title" required>

      <label for="description">Description:</label>
      <textarea id="description" name="description" rows="4" required></textarea>

      <label for="price">Price ($):</label>
      <input type="number" id="price" name="price" min="0" step="0.01" required>

      <label for="image">Upload Image (optional):</label>
      <input type="file" id="image" name="image" accept="image/*">

      <button type="submit" class="btn">Post Listing</button>

    </form>
  </section>

  <footer>
    <p>&copy; 2025 Student Marketplace | Built for Students, by Students</p>
  </footer>
</body>
</html>
