<?php
  define('INSTALLER', 'install.php');

  if (!file_exists('config.php')) {
    header('Location: ' . INSTALLER);
    exit();
  } elseif (file_exists(INSTALLER) && is_writable(INSTALLER)) {
    unlink(INSTALLER);
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
            if (file_exists(INSTALLER)) {
              echo '<p class="status-error">Your LibreDoc instance is installed. Delete the file <em>' . INSTALLER . '</em> for security reasons.</p><br/>';
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
