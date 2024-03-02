<?php

    require_once('./model/bdd.php');
    include_once('./view/new-password.php');
    
    $title = "Acceuil || Plateforme de formation CAF ardennes";
    $bdd = new Bdd();

    if(isset($_POST['connect'])){
        if(isset($_POST['username']) && isset($_POST['pswd_1'])){
            $bdd->firstConnexion($_POST['username'], $_POST['pswd_1']);
        }
    }

    if(isset($_POST['new-pswd'])){
        if(isset($_POST['pswd']) && isset($_POST['pswd-confirm'])){
            if($_POST['pswd'] === $_POST['pswd-confirm']){
                $bdd->updatePswd($_SESSION['user']['id'], $_POST['pswd']);
            }else {
                exit("<div id=connexion><h2 style='color : red; text-align : center;'>Les mots de passe ne correspondent pas.</h2>");
            }
        }
    }