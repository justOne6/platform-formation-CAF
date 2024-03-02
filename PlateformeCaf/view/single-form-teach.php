<?php

include './data/header.php';
include './function/function.php';

if ($_SESSION['user']['profile'] !== 'teacher') {
    header('Location: ' . $_SESSION['url'] . '');
    session_destroy();
    die();
}

require_once './controller/single-form-teach.php';
$id_form = $_GET['id-form'];
$data_form = $bdd->get_formation_data($id_form);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./view/css/single-form-teach.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="./js/function.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
</head>

<body>
    <div id="modal">
        <div id="forModal">
            <form method="POST" enctype="multipart/form-data">
                <h3>Ajouter des ficher a la formation</h3>
                <input type="file" name="fichier[]" multiple>
                <input type="submit" name='new' value="Déposer les nouveaux fichiers">
            </form>
            <a href="#" id="croix">
                <span class="material-icons-outlined">
                    close
                </span></a>
        </div>
    </div>
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
        <div class="principal-content">

            <form action="" method="post">
                <h3>Titre de la formation</h3>
                <h2>
                    <input type="text" name="new-title" id="" value="<?= $data_form['title']; ?>">
                </h2>
                <h3>Description</h3>
                <textarea name="new-text" id="" cols="30" rows="10"><?= $data_form['description']; ?></textarea>
                <input type="submit" name="update-informations" value="Modifier">
                <input type="submit" name="suppression-formation" value="Supprimer la formation">
            </form>
            <article id="list-users">
                <h3>Rechercher un utilsateur</h3>
                <form action=""><input type="text" name="" id="search"></form>
                <form action="" method="post">
                    <ul data-search="true" class="list" id="tg"></ul>
                    <input type="submit" name="inscription" value="Inscrire">
                </form>
            </article>
        </div>
        <div class="attachment">
            <nav id="nav-attachment">
                <ul id="list-attachment">
                    <li class="dot" data-type="txt"><a href="#">Document texte</a></li>
                    <li class="dot" data-type="img"><a href="#">Vidéos</a></li>
                    <li class="dot" data-type="pdf"><a href="#">Quizz évaluation</a></li>
                    <li class="dot active-dot" data-type="inscrit"><a href="#">Personnels inscrits</a></li>
                </ul>
            </nav>
            <section id="inscrit" class="tab tab-active">
                <form action="" method="POST">
                    <ul class="list">
                        <?= $bdd->getSubUser($id_form); ?>
                    </ul>
                    <input type="submit" value="Désinscrire" name="unsub">
                </form>
            </section>
            <?= $bdd->get_updatable_tools($id_form, 'img'); ?>
            <?= $bdd->get_updatable_tools($id_form, 'pdf'); ?>
        </div>
    </div>
    <script>
        $('.dot').click(function(e) {
            e.preventDefault();
            var toShow = $(this).data('type');

            $('section').removeClass('tab-active');
            $('#' + toShow).addClass('tab-active');

            $('.dot').removeClass('active-dot');
            $(this).addClass('active-dot');


            if (toShow == "inscrit") {
                $('#newFile').css('display','none');
            }else {
                $('#newFile').css('display','block');
            }
        })

        var url = window.location.href.slice(-2);
        $('#search').keyup(() => {
            var xhr = new XMLHttpRequest(); // création de la requête XMLHttp
            xhr.onreadystatechange = () => {
                if (xhr.readyState == 4 && xhr.status == 200) { // si réponse is Ok 
                    document.getElementById('tg').innerHTML = xhr.responseText; // On écris la réponse
                }
            }
            if ($('#search').val().length < 1) {
                xhr.open("GET", "function/search-bar.php?id="+url+"&action=1", true);
            } else {
                xhr.open("GET", "function/search-bar.php?id="+url+"&action=0&text=" + $('#search').val(), true);
            }

            xhr.send(); // enovie de la requête 
        });

        $('#newFile').click(function(e) {
            e.preventDefault();

            $('#modal').css('display', 'flex');
            $('#modal').css('z-index', '1');
        });

        $('#croix').click(function() {
            $('#modal').css('display', 'none');
        });

        $(document).ready(() => {
            getList();
        });

        function getList() {

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('tg').innerHTML = xhr.responseText;
                }
            }

            // préparation de la requête HTTP (ici la méthode GET, 002.php est la page de traitement où les infos sont envoyés et true signifie mode asynchrone (on n'attend pas la réponse du serveur))
            xhr.open("GET", "function/search-bar.php?id="+url+"&action=1", true);
            // document.getElementById("saisie").value récupère la saisie du input
            // envoi de la requête HTTP
            xhr.send();
        }

    </script>
</body>
<!-- <footer>
    <div id="fil">
        <nav>
            <ul class="fil_arianne">
                <? filAriane($_SESSION['nav']); ?>
            </ul>
        </nav>
    </div>
</footer> -->

</html>