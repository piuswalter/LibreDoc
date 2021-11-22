<?php
  require_once('config.php');

  session_start();

  if (isset($_GET['id']) && isset($_POST['heading']) && isset($_POST['description'])) {
    $id = $_GET['id'];
    $heading = $_POST['heading'];
    $description = $_POST['description'];

    if ($heading != '') {
      // database connection
      try {
        $pdo = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
      } catch (PDOException $ex) {
        echo 'Error while connecting to database.<br />';
        echo $ex;
        exit();
      }
      $pdo->exec('SET NAMES utf8mb4');

      // get document confidential status from database 
      $sql = $pdo->prepare("SELECT confidential FROM documents WHERE id = ?;");
      $sql->execute([$id]);
      while ($row = $sql->fetch()) {
        $isConfidential = $row['confidential'];
      }

      // check if document is confidential and session is set
      if ($isConfidential && !isset($_SESSION['loggedIn'])) {
        header('Location: change.php?id=' . $id);
        exit();
      }

      // get last revision and document id of document
      $sql = $pdo->prepare("SELECT id_document, MAX(revision) AS revision_max FROM documents WHERE id_document = (SELECT id_document FROM documents WHERE id = ?);");
      $sql->execute([$id]);
      while ($row = $sql->fetch()) {
        $idDocument = $row['id_document'];
        $revisionMax = $row['revision_max'];
      }
      $revisionMax++;

      // get actual date and time
      $dateCreated = date('Y-m-d');
      $timeCreated = date('H:i:s');

      // insert document into database
      $sql = $pdo->prepare("INSERT INTO documents (id, id_document, id_author, revision, date_created, time_created, heading, description, status_deprecated, status_need_review, confidential) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
      $sql->execute([$idDocument, 1, $revisionMax, $dateCreated, $timeCreated, $heading, $description, 0, 0, 0]);
      $addedDocumentId = $pdo->lastInsertId();

      // close database connection
      $pdo = NULL;

      // forward to updated document
      header('Location: show.php?id=' . $addedDocumentId);
      exit();
    } else {
      header('Location: change.php?id=' . $id);
      exit();
    }
  } else {
    if (isset($_GET['id']) && is_numeric($_GET['id']) && (intval($_GET['id']) > 0)) {
      $id = $_GET['id'];

      // check if logout is pressed
      if (isset($_GET['logout'])) {
        session_destroy();

        header('Location: change.php?id=' . $id);
        exit();
      }

      // database connection
      try {
        $pdo = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
      } catch (PDOException $ex) {
        echo 'Error while connecting to database.<br />';
        echo $ex;
        exit();
      }
      $pdo->exec('SET NAMES utf8mb4');

      // read document from database
      $sql = $pdo->prepare("SELECT heading, description, confidential FROM documents WHERE id LIKE ?;");
      $sql->execute([$id]);
      while ($row = $sql->fetch()) {
        $heading = $row["heading"];
        $description = $row["description"];
        $isConfidential = $row["confidential"];
      }

      // close database connection
      $pdo = NULL;

      if ($isConfidential) {
        // check password and show confidential content
        $wrongCredentials = false;
        if (isset($_POST['password']) && ($_POST['password'] === CONFIDENTIAL_PASSWORD)) {
          $_SESSION['loggedIn'] = true;
        } elseif (isset($_POST['password'])) {
          $wrongCredentials = true;
        }

        // add confidential required styles
        echo '
          <style>
            .confidential-logo { height: 30px; }
            .wrong-credentials { color: DarkRed; }
            #header { height: 190px; }
            #header-line { background-color: DarkRed; }
          </style>
        ';
      }
    } else {
      header("Location: index.php");
      exit();
    }
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
          <?php echo !$isConfidential ? '' : '<div class="confidential-logo"><?xml version="1.0" encoding="utf-8"?><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="40px" viewBox="0 0 111.811 122.88" enable-background="new 0 0 111.811 122.88" xml:space="preserve"><g><path fill="DarkRed" fill-rule="evenodd" clip-rule="evenodd" d="M55.713,0c20.848,13.215,39.682,19.467,55.846,17.989 c2.823,57.098-18.263,90.818-55.63,104.891C19.844,109.708-1.5,77.439,0.083,17.123C19.058,18.116,37.674,14.014,55.713,0L55.713,0 z M41.458,56.508h1.438v-3.331c0-3.689,1.454-7.051,3.797-9.494c2.361-2.462,5.622-3.991,9.214-3.991s6.854,1.529,9.214,3.991 c2.343,2.443,3.797,5.805,3.797,9.494v3.331h1.438c1.167,0,2.123,0.955,2.123,2.123v22.434c0,1.168-0.956,2.123-2.123,2.123H41.458 c-1.168,0-2.123-0.955-2.123-2.123V58.631C39.334,57.463,40.29,56.508,41.458,56.508L41.458,56.508z M54.164,71.286l-2.291,6h8.066 l-2.122-6.082c1.347-0.693,2.268-2.097,2.268-3.716c0-2.308-1.871-4.179-4.179-4.179s-4.178,1.871-4.178,4.179 C51.728,69.173,52.726,70.625,54.164,71.286L54.164,71.286z M46.916,56.508h17.981v-3.331c0-2.623-1.021-4.999-2.666-6.715 c-1.627-1.697-3.866-2.751-6.325-2.751c-2.458,0-4.698,1.054-6.324,2.751c-1.646,1.716-2.666,4.092-2.666,6.715V56.508 L46.916,56.508z"/></g></svg></div>'; ?>
          <h2>LibreDoc</h2>
          <h3>Edit document<h3>
        </div>

        <div id="sidebar">
          <?php include('sidebar.html'); ?>
          <?php echo !isset($_SESSION['loggedIn']) ? '' : '<ul class="sidebar"><br /><li><b>Active session</b></li><li><a href="change.php?id=' . $id . '&logout"><input type="button" value="Logout" /></a></li></ul>'; ?>
        </div>

        <div id="main">
          <hr />
          <?php
            if ($isConfidential && !isset($_SESSION['loggedIn'])) {
          ?>
          <br />
          <form action="change.php?id=<?=$id?>" method="post">
            <p>Enter secret password to edit this confidential document</p>
            <?php echo !$wrongCredentials ? '' : '<p class="wrong-credentials">Your entered password is wrong!</p>'; ?>
            <input name="password" type="password" />
            <br />
            <br />
            <input type="submit" value="Request approval" />
          </form>
          <?php
            } else {
          ?>
          <form action="change.php?id=<?=$id?>" method="post">
            <p>Heading</p>
            <input name="heading" type="text" autocomplete="off" required="required" value="<?php echo htmlspecialchars($heading); ?>" class="bs-bb w-100" />
            <br />
            <br />
            <p>Description</p>
            <textarea name="description" class="bs-bb w-100 h-400p"><?php echo htmlspecialchars($description); ?></textarea>
            <br />
            <br />
            <input type="submit" value="Update document" />
          </form>
          <?php
            }
          ?>
        </div>

        <div id="footer">
          <?php include('footer.html'); ?>
        </div>
      </div>
    </div>
  </body>
</html>
