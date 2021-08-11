<?php
  if (!isset($_GET['install'])) {
?>
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
          <h3>Install</h3>
        </div>

        <div id="sidebar">
          <ul class="sidebar">
            <li><strong>Quick links</strong></li>
            <li><a target="_blank" href="https://libredoc.eu/">LibreDoc Website</a></li>
            <li><a target="_blank" href="https://demo.libredoc.eu/">LibreDoc Demo</a></li>
            <li><a target="_blank" href="https://code.libredoc.eu/">LibreDoc Code</a></li>
          </ul>
        </div>

        <div id="main">
          <hr />
          <form action="install.php?install" method="post">
            <h4>Database</h4>
            <p>Enter the data for connecting the database here.</p>
            <table>
              <tr>
                <td width="100px"><label for="database-host">Host</label></td>
                <td><input id="database-host" name="database-host" type="text" value="localhost" /></td>
              </tr>
              <tr>
                <td><label for="database-name">Name</label></td>
                <td><input id="database-name" name="database-name" type="text" value="libredoc" /></td>
              </tr>
              <tr>
                <td><label for="database-username">Username</label></td>
                <td><input id="database-username" name="database-username" type="text" value="libredoc" /></td>
              </tr>
              <tr>
                <td><label for="database-password">Password</label></td>
                <td><input id="database-password" name="database-password" type="password" /></td>
              </tr>
            </table>
            <p>All required tables will be force created. That meens if they exist before, they will be deleted.</p>
            <h4>LibreDoc</h4>
            <p>Enter the name of the standard author here. This will be displayed at the end of each created document.</p>
            <table>
              <tr>
                <td width="100px"><label for="author">Author</label></td>
                <td><input id="author" name="author" type="text" /></td>
              </tr>
            </table>
            <p>Assign a secure password here to access and edit confidential documents.</p>
            <table>
              <tr>
                <td width="100px"><label for="password">Password</label></td>
                <td><input id="password" name="password" type="text" autocomplete="off" /></td>
              </tr>
            </table>
            <p><input type="submit" value="Install LibreDoc" /></p>
          </form>
        </div>

        <div id="footer">
          <?php include('footer.html'); ?>
        </div>
      </div>
    </div>
  </body>
</html>
<?php
  } elseif (isset($_GET['install']) && isset($_POST['database-host'])     && $_POST['database-host'] !== ''
                                    && isset($_POST['database-name'])     && $_POST['database-name'] !== ''
                                    && isset($_POST['database-username']) && $_POST['database-username'] !== ''
                                    && isset($_POST['database-password']) && $_POST['database-password'] !== ''
                                    && isset($_POST['author'])            && $_POST['author'] !== ''
                                    && isset($_POST['password'])          && $_POST['password'] !== '') {
    $databaseHost     = $_POST['database-host'];
    $databaseName     = $_POST['database-name'];
    $databaseUsername = $_POST['database-username'];
    $databasePassword = $_POST['database-password'];
    $author           = $_POST['author'];
    $password         = $_POST['password'];

    // database connection
    try {
      $pdo = new PDO('mysql:host=' . $databaseHost . ';dbname=' . $databaseName, $databaseUsername, $databasePassword);
    } catch (PDOException $ex) {
      echo 'Error while connecting to database.<br />';
      echo $ex;
      exit();
    }
    $pdo->exec('SET NAMES utf8mb4');

    try {
      // drop all required tables
      $sql = 'DROP TABLE IF EXISTS `authors`';
      $pdo->exec($sql);
      $sql = 'DROP TABLE IF EXISTS `documents`';
      $pdo->exec($sql);

      // create table for authors
      $sql = 'CREATE TABLE `authors`(
                `id` INT(255) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                PRIMARY KEY(`id`)
              ) ENGINE = INNODB;';
      $pdo->exec($sql);

      // insert given author
      $sql = $pdo->prepare("INSERT INTO `authors` (`id`, `name`) VALUES (NULL, ?);");
      $sql->execute([$author]);

      // create table for documents
      $sql = 'CREATE TABLE `documents`(
                `id` INT(255) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_document` INT(255) UNSIGNED NOT NULL,
                `id_author` INT(255) UNSIGNED NOT NULL,
                `revision` INT(255) UNSIGNED NOT NULL,
                `date_created` DATE NOT NULL,
                `time_created` TIME NOT NULL,
                `heading` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `status_deprecated` BOOLEAN NOT NULL,
                `status_need_review` BOOLEAN NOT NULL,
                `confidential` BOOLEAN NOT NULL,
                PRIMARY KEY(`id`)
              ) ENGINE = INNODB;';
      $pdo->exec($sql);
    } catch (\Throwable $th) {
      throw $th;
    }

    $configFile = "<?php
  /**
   * Automatically generated configuration file through setup
   */

  if (basename(__FILE__) === basename(\$_SERVER['PHP_SELF'])) {
    header('Location: ./');
    exit();
  }

  // database connection
  define('DATABASE_HOST', '$databaseHost');
  define('DATABASE_NAME', '$databaseName');
  define('DATABASE_USERNAME', '$databaseUsername');
  define('DATABASE_PASSWORD', '$databasePassword');

  // password for confidential documents
  define('CONFIDENTIAL_PASSWORD', '$password')
?>
";

    file_put_contents('config.php', $configFile);

    header('Location: index.php');
    exit();
  } else {
?>
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
          <h3>Install</h3>
        </div>

        <div id="sidebar">
          <?php include('sidebar.html'); ?>
        </div>

        <div id="main">
          <hr />
          <h4>Installation error</h4>
          <p>All input fields must be filled in to complete the installation.</p>
          <a href="install.php">Start again</a>
        </div>

        <div id="footer">
          <?php include('footer.html'); ?>
        </div>
      </div>
    </div>
  </body>
</html>
<?php
  }
?>
