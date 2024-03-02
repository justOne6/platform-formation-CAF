<?php


require_once('./model/bdd.php');
include_once('./view/ask-pswd.php');

$bdd = new Bdd();

if(isset($_POST['ask-pswd'])){
    if(isset($_POST['identifiant'])){
        $bdd->forgotPassword($_POST['identifiant']);
        echo '<meta http-equiv="refresh" content="0; url='. $_SESSION['url'].'/new-password.php">';
    }else {
        exit("<div id=connexion><h2 style='color : red; text-align : center;'>L'identifant ou le mot de passe est incorret.</h2>");
    }
}