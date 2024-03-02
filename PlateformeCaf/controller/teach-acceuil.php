<?php

// if(! isset($_SESSION['user'])){
//     header('Location: ./index.php');
//     die();
// }
$title = "Accueil || Formateurs";
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
require_once './view/teach-acceuil.php';


$bdd = new Bdd();

if (isset($_POST['depot'])) {
    if (isset($_POST['title']) && isset($_POST['category']) && isset($_FILES['fichier'])) {
        $subcategory = find_id_sub($_POST['category']);

        if (strlen($subcategory) < 1) {
            $subcategory = 'null';
        }

        $category = find_id_category($_POST['category']);
        $title = $_POST['title'];

        $link = array();

        require_once './function/function.php';
        $files = reArrayFiles($_FILES['fichier']);

        for ($i = 0; $i < count($files); $i++) {

            $name = $files[$i]['name'];
            $tmpName = $files[$i]['tmp_name'];
            $type = $files[$i]['type'];

            $url = './data/formation/' . $name;

            move_uploaded_file($tmpName, $url);

            $row = array(
                'url' => $url,
                't_type' => $type
            );

            array_push($link, $row);
        }

        $description = $_POST['description'];

        $data = array(
            $title,
            $description,
            $category,
            $subcategory,
        );

        $bdd->new_formation($data, $link);


        // if (is_authorized($type)) {
        //     if ($type == 'application/pdf') {
        //         move_uploaded_file($tmpName, './Data/formation/pdf/' . $name);
        //         $url = './Data/formation/pdf/' . $name;
        //     } else {
        //         move_uploaded_file($tmpName, './Data/formation/video/' . $name);
        //         $url = './Data/formation/video/' . $name;
        //     }

        //     $data = array(
        //         $_POST['titre'],
        //         $_POST['categorie'],
        //         $url,
        //     );

        // $bdd->new_formation($data);
        // } else {
        //     exit("Erreur le format de fichier que vous avez fournie n'est pas authoriser par votre plateforme");
        // }



        // echo $url;
    }
}
// Verification si le format déposer est celui désiré
function is_authorized($type)
{
    $state = False;

    $authorized = array(
        'application/pdf',
        'video/quicktime',
    );

    for ($i = 0; $i < count($authorized); $i++) {
        if ($authorized[$i] == $type) {
            $state = True;
            break;
        }
    };

    return $state;
}

function find_slash($category)
{
    for ($i = 0; $i < strlen($category); $i++) {
        if ($_POST['category'][$i] == '/') {
            return $i;
            break;
        }
    }
}

function find_id_sub($arg)
{
    $position = find_slash($arg);
    $sub = '';
    for ($i = $position + 1; $i < strlen($arg); $i++) {
        $sub = $sub . $arg[$i];
    }
    return $sub;
}

function find_id_category($arg)
{
    $position = find_slash($arg);
    $cat = '';
    for ($i = 0; $i < $position; $i++) {
        $cat = $cat . $arg[$i];
    }
    return $cat;
}
