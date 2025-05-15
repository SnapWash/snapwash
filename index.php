<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>snapwash</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
  <link rel="stylesheet" href="assets/css/index.css"/>
</head>
<body>
  <header>
    <h1>Logo</h1>
    <nav style="display: flex; justify-content: center; gap: 1.5rem; flex-grow: 1;">
      <a href="index.php">Home</a>
      <a href="page/pricelist.html">Pricelist</a>
      <a href="page/about-us.html">About Us</a>
      <a href="page/contact.html">Contact</a>
    </nav>
    <div id="authControl">
      <?php
        session_start();
        if (isset($_SESSION['user_id'])) {
          echo '<form method="GET" action="server.php" style="display:inline;">
                  <button type="submit" name="action" value="logout" class="login-nav">Logout</button>
                </form>';
        } else {
          echo '<a href="page/login.html" class="login-nav">Login/Regist</a>';
        }
      ?>
    </div>
  </header>

  <main>
    <div class="main">
        <img src="assets/img/mesin cuci (1).png" alt="">
    <div class="short-info">
        <h1>WELCOME</h1>
        <p>Welcome to our store. wash clean until <br> shiny without any stains.</p>
        <div>
            <a href="page/order.html"><button class="btn">Get Started</button></a>
            <a href=""><button class="btn">See More</button></a>
        </div>
    </div>
    </div>
  </main>
  <script src="assets/js/main.js"></script>
</body>
</html>
