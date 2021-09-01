<?php
  require_once('config.php');
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
    <?php
      if (isset($_POST['heading']) && isset($_POST['description'])) {
        $heading = $_POST['heading'];
        $description = $_POST['description'];

        if ($heading !== '') {
          // database connection
          try {
            $pdo = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
          } catch (PDOException $ex) {
            echo 'Error while connecting to database.<br />';
            echo $ex;
            exit();
          }
          $pdo->exec('SET NAMES utf8mb4');

          // get last document id and generate next one
          $sql = "SELECT MAX(id_document) AS id_document_max FROM documents;";
          foreach ($pdo->query($sql) as $row) $idDocumentMax = $row['id_document_max'];
          $idDocumentMax++;

          // get actual date and time
          $dateCreated = date('Y-m-d');
          $timeCreated = date('H:i:s');

          // insert document into database
          $sql = $pdo->prepare("INSERT INTO documents (id, id_document, id_author, revision, date_created, time_created, heading, description, status_deprecated, status_need_review, confidential) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
          $sql->execute([$idDocumentMax, 1, 0, $dateCreated, $timeCreated, $heading, $description, 0, 0, 0]);
          $addedDocumentId = $pdo->lastInsertId();

          // close database connection
          $pdo = NULL;

          // forward to created document
          header('Location: show.php?id=' . $addedDocumentId);
          exit();
        }
      }
    ?>
    <div id="header-line"></div>

    <div id="content">
      <div id="screen">
        <div id="header">
          <br />
          <br />
          <h2>LibreDoc</h2>
          <h3>Add new document</h3>
        </div>

        <div id="sidebar">
          <?php include('sidebar.html'); ?>
        </div>

        <div id="main">
          <hr />
          <form action="add.php" method="post">
            <p>Heading</p>
            <input name="heading" type="text" autocomplete="off" required="required" class="bs-bb w-100" />
            <br />
            <br />
            <p>Description</p>
            <textarea name="description" class="bs-bb w-100 h-400p"></textarea>
            <br />
            <br />
            <input type="submit" value="Add new document" />
          </form>
        </div>

        <div id="footer">
          <?php include('footer.html'); ?>
        </div>
      </div>
    </div>
  </body>
</html>
