<?php

require_once './model/bdd.php';
include './function/function.php';

if(isset($_SESSION['user'])){
    if ($_SESSION['user']['profile'] !== 'student') {
        header('Location: ' . $_SESSION['url'] . '');
        session_destroy();
        die();
    }
}else {
    header('Location: ' . $_SESSION['url'] . '');
    session_destroy();
    die();
}

// if (!isset($_SESSION['user'])) {
//     header('Location: '.$_SESSION['url'].'');
//     die();
// }

date_default_timezone_set('Europe/Paris');
$date = date('d-m-y H:i:s');

$h = date('H');
if ($h > 6 && $h < 18) {
    $l = 'Bonjour';
} else {
    $l = 'Bonsoir';
}

$bdd = new Bdd();

// if($_SESSION['user']['profile'] !== 'student'){
//     header('Location: '.$_SESSION['url'].'');
//     session_destroy();
//     die();
// }

// echo $_SESSION['url'];
// echo realpath('teach-acceuil.php'); 

$id = $_SESSION['user']['id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./view/css/teach-acceuil.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="./js/function.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
</head>

<body>
    <div id="header">
        <img src="./data/logo.png" alt="">
        <div>
            <span>
                <h5><?= $l . ', ' . ucfirst($_SESSION['user']['username']); ?></h5>
            </span>
        </div>
        <div id="log-out">
            <a href="./function/function.php?action=0">Deconnexion</a>
        </div>
    </div>
    <div class="container">
        <div class="col-left" onclick="formation('col-left')">
            <div id="img-left">
                <h5 id="titreImg">Mes formations</h5>
            </div>
            <div id="col-left-container">
            <div id="list">
            <h2>Liste de mes formations</h2>
            
                <?= $bdd->user_sub_formation($id); ?>
                </div>
                <a href="./student-acceuil.php" id="croix"> <span class="material-icons-outlined">
                        close
                    </span></a>
            </div>
        </div>
        <div class="col-right" onclick="formation('col-right')">
            <div id="img-right">
                <h5 id="titreImg">Liste des formations</h5>
            </div>
            <div id="col-right-container">
                <div id="list">
                    <h2>Liste de toutes les formations</h2>
                        <?= $bdd->user_formation($id); ?>
                </div>
                <a href="./student-acceuil.php" id="croix"> <span class="material-icons-outlined">
                        close
                    </span></a>
            </div>

        </div>
    </div>
    <footer>
        <div id="fil">
            <nav>
                <ul>
                    <? filAriane($_SESSION['nav']); ?>
                </ul>
            </nav>
        </div>
    </footer>
</body>

</html>