<?php

include './data/header.php';
if ($_SESSION['user']['profile'] !== 'student') {
    header('Location: ' . $_SESSION['url'] . '');
    session_destroy();
    die();
}

require_once './controller/single-form.php';
$id_form = $_GET['id-form'];
$data_form = $bdd->get_formation_data($id_form);
$id_user = $_SESSION['user']['id'];

$data = $bdd->get_sub_info($_SESSION['user']['id'], $id_form);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./view/css/single-form.css">
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
        <div class="formation">
            <h3>Titre de la formation</h3>
            <h2 id="titre">
                <?= $data_form['title']; ?>
            </h2>
            <h3>Description</h3>
            <span id="description"><?= $data_form['description']; ?></span>
            <form method="post">
                <input type="submit" id="validate" name="valider-form" value="Valider la formation">
            </form>
        </div>
        <div class="attachment">
            <nav>
                <h3>
                    <li><a href="#" id="nav-att" class="dot active-dot" data-type="txt">Document texte</a></li>
                </h3>
                <h3>
                    <li><a href="#" id="nav-att" class="dot" data-type="img">Vidéos</a></li>
                </h3>
                <h3>
                    <li><a href="#" id="nav-att" class="dot" data-type="pdf">Quizz évaluation</a></li>
                </h3>
            </nav>
            <!-- <section id="txt" class='tab tab-active'>
                
            </section> -->
            <!-- <section id="img" class="tab"> -->
            <?= $bdd->get_formation_tools($id_form, 'img'); ?>
            <?= $bdd->get_formation_tools($id_form, 'pdf'); ?>
            <!-- </section> -->
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
        })

        if ($('#txt').length === 0) {
            $('*[data-type=txt]').css('display', 'none');
        }

        $(document).ready(() => {
            if ($('#txt').length === 0) {
                $('*[data-type=txt]').css('display', 'none');
            }
            if ($('#img').length === 0) {
                $('*[data-type=img]').css('display', 'none');
            }
            if ($('#pdf').length === 0) {
                $('*[data-type=qcm]').css('display', 'none');
            }

            if($('#inscrit').data('sub') === 'non'){
                $('#validate').css('display','none');
                $("#validate").attr("disabled", true);
            }
        });
    </script>
</body>

</html>