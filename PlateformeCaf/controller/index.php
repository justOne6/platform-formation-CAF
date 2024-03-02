<?php
$title = "Acceuil || Plateforme de formation CAF ardennes";
session_start();

require_once('./model/bdd.php');
include_once('./view/index.php');

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$_SESSION['url'] = $url;

$_SESSION['nav'] = array(
    array(
        'link' => $url,
        'title' => 'Accueil de la plateforme'
    )
);


$bdd = new Bdd();

if (isset($_POST['submitform'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $profil = $bdd->login($_POST['username'], $_POST['password']);
        if ($profil === 'admin') {
            echo '<meta http-equiv="refresh" content="1; url=' . $url . 'admin.php">';
        } elseif ($profil === 'teacher') {
            echo '<meta http-equiv="refresh" content="1; url=' . $url . 'teach-acceuil.php">';
        }elseif ($profil === 'student') {
            echo '<meta http-equiv="refresh" content="1; url=' . $url . 'student-acceuil.php">';
        }
    }
}
