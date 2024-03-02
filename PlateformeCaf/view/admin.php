<?php

include './data/header.php';

if ($_SESSION['user']['profile'] !== 'admin') {
    header('Location: ' . $_SESSION['url'] . '');
    session_destroy();
    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="./view/css/admin.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <script src="./js/function.js"></script>
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
            <a href=<?= $_SESSION['url'] . 'function/function.php?action=0'; ?>>Deconnexion</a>
        </div>
    </div>
    <div class="container-admin">
        <div class="menu-profil">
            <article class="teacher-btn" data-active="formateurs" class="tab-nav-active">
                <a href="#" id="menu" class="tab-nav-active active-dot" data-to="teacher">Formateurs</a>
            </article>
            <article class="kids-btn" data-active="student">
                <a href="#" id="menu" class="tab-nav-active" data-to="student">Personnel en formation</a>
            </article>
            <article class="new-profile-btn" data-active="new-profile">
                <a href="#" id="menu" class="tab-nav-active" data-to="new-profile">Ajouter un utilisateur</a>
            </article>
        </div>
        <div class="list">
            <section id="teacher" class="tab tab-active"><?= $bdd->show_users('teacher'); ?></section>
            <section id="student" class="tab"><?= $bdd->show_users('student'); ?></section>
            <section id="new-profile" class="tab">
                <form action="" method="post">
                    <label for="">Nom</label>
                    <input type="text" name="lastname">
                    <label for="">Prénom</label>
                    <input type="text" name="firstname">
                    <label for="">Adresse-email</label>
                    <input type="email" name="email">
                    <select name="profil" id="profil">
                        <option selected>Choisissez le profil du nouvel utilisateur</option>
                        <option value="teacher">Enseignant</option>
                        <option value="student">Élèves</option>
                    </select>
                    <input type="submit" value="Finaliser l'inscription" name="new-mate">
                </form>
            </section>
        </div>
    </div>

    <script>
        $('a#menu').click(function(e) {
            e.preventDefault();
            console.log(this);
            var toShow = $(this).data('to');
            console.log(toShow);

            $('section').removeClass('tab-active');
            $('#' + toShow).addClass('tab-active');


            if (toShow == "teacher") {
                var active = $("a#menu")[0];
                $('a#menu').removeClass('active-dot');
                $(active).addClass('active-dot');
            }
            if (toShow == "student") {
                var active = $("a#menu")[1];
                $('a#menu').removeClass('active-dot');
                $(active).addClass('active-dot');

            }
            if (toShow == "new-profile") {
                var active = $("a#menu")[2];
                $('a#menu').removeClass('active-dot');
                $(active).addClass('active-dot');

            }
        })
    </script>
</body>

</html>