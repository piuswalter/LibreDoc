<?php
  require_once('config.php');

  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // database connection
    try {
      $pdo = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
    } catch (PDOException $ex) {
      echo 'Error while connecting to database.<br />';
      echo $ex;
      exit();
    }
    $pdo->exec('SET NAMES utf8mb4');

    // get all revision from given document
    $sql = $pdo->prepare("SELECT id, revision, heading, status_deprecated, status_need_review, confidential FROM documents WHERE id_document LIKE ?;");
    $sql->execute([$id]);

    // close database connection
    $pdo = NULL;
  }
?>
<!DOCTYPE html>
<html lang="en">
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
          <h3>Revisions</h3>
        </div>

        <div id="sidebar">
          <?php include('sidebar.html'); ?>
        </div>

        <div id="main">
          <hr />
          <p>The following revisions were found</p>
          <?php
            echo '<ul class="search-list">';
            while ($row = $sql->fetch()) {
              echo '<li><span class="ff-monospace">[s:&nbsp;' . ($row['status_need_review'] == 0 ? '-' : 'r') . '' . ($row['status_deprecated'] == 0 ? '-' : 'd') . '' . ($row['confidential'] == 0 ? '-' : '<span class="status-confidential">c</span>') . ']</span> <a href="show.php?id=' . $row['id'] . '">' . htmlspecialchars($row['heading']) . '</a> [Rev. ' . $row['revision'] . ']</li>';
            }
            echo '</ul>';
          ?>
        </div>

        <div id="footer">
          <?php include('footer.html'); ?>
        </div>
      </div>
    </div>
  </body>
</html>
