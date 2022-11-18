<?php
require("pages/includes/sessionParam.php");
var_dump($_SESSION);
session_start();
var_dump($_SESSION);
require("pages/includes/headers.php");
var_dump($_SESSION);
$_SESSION["mon"]= "bonjour";
var_dump($_SESSION);

$files = glob('classes/*.php');
foreach ($files as $file) {
    require($file);
}

$conn = Database::connect();

$page_info = PageListing::getCurrent();
//name, title, connected, root
extract($page_info);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <?php require("pages/includes/linksAndScripts.php") ?>
</head>

<body>
    <div class="mainContent">
        <?php
        PageListing::load($name);
        ?>
    </div>
    <?php
    require("pages/includes/footer.php")
    ?>
</body>

</html>