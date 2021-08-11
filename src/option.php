<?php
  require_once('config.php');

  if (isset($_GET['id']) && isset($_GET['s']) && $_GET['id'] !== '' && ($_GET['s'] === 'd' || $_GET['s'] === 'nr' || $_GET['s'] === 'c')) {
    $id = $_GET['id'];
    $status = $_GET['s'];

    // database connection
    try {
      $pdo = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME, DATABASE_USERNAME, DATABASE_PASSWORD);
    } catch (PDOException $ex) {
      echo 'Error while connecting to database.<br />';
      echo $ex;
      exit();
    }
    $pdo->exec('SET NAMES utf8mb4');

    // write new status into the database
    if ($status === 'd') {
      $sql = $pdo->prepare("UPDATE documents SET status_deprecated = 1 WHERE id = ?;");
      $sql->execute([$id]);
    } elseif ($status === 'nr') {
      $sql = $pdo->prepare("UPDATE documents SET status_need_review = 1 WHERE id = ?;");
      $sql->execute([$id]);
    } elseif ($status === 'c') {
      $sql = $pdo->prepare("UPDATE documents SET confidential = 1 WHERE id = ?;");
      $sql->execute([$id]);
    }

    // close database connection
    $pdo = NULL;
    
    // go back to the document
    header("Location: show.php?id=" . $id);
    exit();
  } else {
    header("Location: index.php");
    exit();
  }
?>
