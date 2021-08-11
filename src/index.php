<?php
  if (!file_exists('config.php')) {
    header('Location: install.php');
    exit();
  } elseif (file_exists('install.php')) {
    unlink('install.php');
    if (file_exists('install.php')) {
      $deleteInstaller = true;
    }
  } else {
    $deleteInstaller = false;
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title>LibreDoc</title>

    <meta charset="utf-8" />

    <link rel="icon" href="logo.svg" />

    <link rel="stylesheet" type="text/css" href="style.css" media="screen" />
  </head>
  <body>
    <div id="header-line"></div>

    <div id="content">
      <div id="screen">
        <div id="header">
          <br />
          <br />
          <h2>LibreDoc</h2>
          <h3>Home</h3>
        </div>

        <div id="sidebar">
          <?php include('sidebar.html'); ?>
        </div>

        <div id="main">
          <hr />
          <?php
            if ($deleteInstaller) {
              echo '<p class="status-error">Your LibreDoc instance is installed. Delete the file <em>install.php</em> for security reasons.</p><br/>';
            }
          ?>
          <form action="search.php" method="get">
            <p>Use search function:
              <input type="text" name="search" />
              <input type="submit" value="Search" />
            </p>
          </form>
          <br />
          <pre><?php include('help.html'); ?></pre>
        </div>

        <div id="footer">
          <?php include('footer.html'); ?>
        </div>
      </div>
    </div>
  </body>
</html>
