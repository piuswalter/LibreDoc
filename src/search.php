<?php
  require_once('config.php');
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
          <h3>Search</h3>
        </div>

        <div id="sidebar">
          <?php include('sidebar.html'); ?>
        </div>

        <div id="main">
          <hr />
          <form action="search.php" method="get">
            <p>Use search:
              <input type="text" name="search" />
              <input type="submit" value="Search" />
              <br />
              The wildcard operator <strong>%</strong> is available.
            </p>
          </form>
          <br />
          <?php
            // check if search value is set and save it 
            if (isset($_GET['search']) && $_GET['search'] !== '') {
              $emptySearch = false;
              $search = $_GET['search'];
            } else {
              $emptySearch = true;
              $search = '%';
            }

            // check if a correct status is given
            if (isset($_GET['s']) && ($_GET['s'] === 'd' || $_GET['s'] === 'nr')) {
              $status = $_GET['s'];
            } else {
              $status = '';
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

            // calculate amount of filtered documents
            $sql = $pdo->prepare("SELECT COUNT(id) AS amount FROM documents WHERE id IN (SELECT MAX(id) AS id FROM documents GROUP BY id_document) AND description LIKE ? AND status_deprecated = 0 ORDER BY heading;");
            $sql->execute([$search]);
            while ($row = $sql->fetch()) $amount = $row['amount'];

            // select the right sql statement for reading documents
            if (!$emptySearch) {
              $searchParameter = true;
              echo '<p>The following <strong>' . $amount . '</strong> results were found for <em>' . htmlspecialchars($search) . '</em></p>';
              $sql = $pdo->prepare("SELECT id, revision, heading, status_deprecated, status_need_review, confidential FROM documents WHERE id IN (SELECT MAX(id) AS id FROM documents GROUP BY id_document) AND description LIKE ? AND status_deprecated = 0 ORDER BY heading;");
            } elseif ($status === 'd') {
              $searchParameter = false;
              echo '<p>The following documents are deprecated</p>';
              $sql = "SELECT id, revision, heading, status_deprecated, status_need_review, confidential FROM documents WHERE id IN (SELECT MAX(id) AS id FROM documents GROUP BY id_document) AND status_deprecated = 1 ORDER BY heading;";
            } elseif ($status === 'nr') {
              $searchParameter = false;
              echo '<p>The following documents need review</p>';
              $sql = "SELECT id, revision, heading, status_deprecated, status_need_review, confidential FROM documents WHERE id IN (SELECT MAX(id) AS id FROM documents GROUP BY id_document) AND status_need_review = 1 ORDER BY heading;";
            } else {
              echo "<p>Empty search. You can search in <strong>$amount</strong> documents.</p>";
            }
            
            // print search results if search or status is not empty 
            if(!$emptySearch || $status !== '') {
              echo '<ul class="search-list">';
              if ($searchParameter) {
                $sql->execute([$search]);
                while ($row = $sql->fetch()) {
                  echo '<li><span class="ff-monospace">[s:&nbsp;' . ($row['status_need_review'] == 0 ? '-' : 'r') . '' . ($row['status_deprecated'] == 0 ? '-' : 'd') . '' . ($row['confidential'] == 0 ? '-' : '<span class="status-confidential">c</span>') . ']</span> <a href="show.php?id=' . $row['id'] . '">' . htmlspecialchars($row['heading']) . '</a> [Rev. ' . $row['revision'] . ']</li>';
                }
              } else {
                foreach ($pdo->query($sql) as $row) {
                  echo '<li><span class="ff-monospace">[s:&nbsp;' . ($row['status_need_review'] == 0 ? '-' : 'r') . '' . ($row['status_deprecated'] == 0 ? '-' : 'd') . '' . ($row['confidential'] == 0 ? '-' : '<span class="status-confidential">c</span>') . ']</span> <a href="show.php?id=' . $row['id'] . '">' . htmlspecialchars($row['heading']) . '</a> [Rev. ' . $row['revision'] . ']</li>';
                } 
              }
              echo '</ul>';
            }

            // close database connection
            $pdo = NULL;
          ?>
        </div>

        <div id="footer">
          <?php include('footer.html'); ?>
        </div>
      </div>
    </div>
  </body>
</html>
