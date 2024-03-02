<?php

// if(! isset($_SESSION['user'])){
//     header('Location: ./index.php');
//     die();
// }
$title = "Accueil || Personnel en formation";
session_start();
if (!in_array($_SESSION['url'] . basename($_SERVER['PHP_SELF']), array_column($_SESSION['nav'], 'link'))) {
    $row = array(
        'link'=>$_SESSION['url'] . basename($_SERVER['PHP_SELF']),
        'title'=>$title
    );
    array_push($_SESSION['nav'], $row);
}else {
    $last = count($_SESSION['nav']);
    unset($_SESSION['nav'][$last-1]);
}

require_once './model/bdd.php';
require_once './view/student-acceuil.php';