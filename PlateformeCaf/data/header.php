<?php

require_once './model/bdd.php';


if (!isset($_SESSION['user'])) {
    header('Location: '.$_SESSION['url'].'');
    die();
}

date_default_timezone_set('Europe/Paris');
$date = date('d-m-y H:i:s');

$h = date('H');
if ($h > 6 && $h < 18) {
    $l = 'Bonjour';
} else {
    $l = 'Bonsoir';
}

$bdd = new Bdd();

?>