<h1 align="center">
    <a href="#"><img src="https://raw.githubusercontent.com/piuswalter/LibreDoc/main/logo.svg" alt="LibreDoc" width="50"></a>
    <br />
    LibreDoc
    <br />
</h1>
<div align="center">
    <small>Your Open Source Intranet Documentation Software</small>
</div>

---

![GitHub last commit (branch)](https://img.shields.io/github/last-commit/piuswalter/LibreDoc/development)
[![GitHub issues](https://img.shields.io/github/issues/piuswalter/LibreDoc)](https://github.com/piuswalter/LibreDoc/issues)
![GitHub language count](https://img.shields.io/github/languages/count/piuswalter/LibreDoc)
![Lines of code](https://img.shields.io/tokei/lines/github/piuswalter/LibreDoc)

LibreDoc is an open source documentation software for your intranet. It offers the possibility to create and update documents. LibreDoc takes care of creating the necessary revisions for auditability.

## 🖥️ LibreDoc demo

The LibreDoc demo shows you all the functionalities on the latest version. Here you can test before you host your own instance.

You can find it [here](https://demo.libredoc.eu/).

## ⚒️ Setup your own LibreDoc instance

Below is the procedure on how to set up your own LibreDoc instance.

### Prerequisites

Before you start, you need already installed software. To be specific

- PHP ([www.php.net/manual/en/install.php](https://www.php.net/manual/en/install.php))
- MySQL ([dev.mysql.com/downloads/](https://dev.mysql.com/downloads/)) or
- MariaDB ([mariadb.org/download/](https://mariadb.org/download/))

There is also software like [XAMPP](https://www.apachefriends.org/de/index.html) that already contains all the necessary components. Caution, this should only be used for testing purposes or for the instance on your own computer due to security reasons.

### Installing LibreDoc

Move all files from `src/` to the folder that can be accessed from your webserver.

Call the LibreDoc directory, you should be redirected directly to `install.php`.

Follow the installation steps as described in the LibreDoc installer. At the end of the setup you should be redirected to your LibreDoc instance and the file `install.php` should be deleted automatically. If this did not happen due to an error, be sure to delete it manually.

### Run the application

If your web server is running on your local computer, you can access your LibreDoc instance via [localhost](http://localhost/).

## ⚙️ Running on HTML, CSS, PHP and MySQL (or MariaDB)

[![HTML5](https://img.shields.io/badge/-HTML5-333333?logo=HTML5)](https://www.w3.org/TR/html52/)
[![CSS3](https://img.shields.io/badge/-CSS3-333333?logo=CSS3)](https://www.w3.org/TR/CSS/#css)
[![PHP](https://img.shields.io/badge/-PHP-333333?logo=PHP)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/-MySQL-333333?logo=MySQL)](https://www.mysql.com/)
[![MariaDB](https://img.shields.io/badge/-MariaDB-333333?logo=MariaDB)](https://mariadb.org/)

- [HTML5](https://www.w3.org/TR/html52/) - The structure for the frontend
- [CSS3](https://www.w3.org/TR/CSS/#css) - Brings the style to the fronend
- [PHP](https://www.php.net/) - The logic for the backend
- [MySQL](https://www.mysql.com/) - The database to store your documents
- [MariaDB](https://mariadb.org/) - Same here but a MySQL fork


## 📜 License

[![GitHub license](https://img.shields.io/github/license/piuswalter/LibreDoc)](https://github.com/piuswalter/LibreDoc/blob/main/LICENSE)

This project is licensed under the AGPL-3.0 License - see the [LICENSE](LICENSE) file for details
