<?php

require_once './model/bdd.php';
require_once './view/single-form-teach.php';


$profile = $_SESSION['user']['profil'];

$bdd = new Bdd();

$id_form = $_GET['id-form'];

$data_form = $bdd->get_formation_data($id_form);
$title = "Formation || ".$data_form['title'];


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





if (isset($_POST['suppression'])) {
    if (isset($_POST['id'])) {
        $bdd->supp_file($_POST['id'], $id_form);
    }
}

if (isset($_POST['new'])) {
    if (isset($_FILES['fichier'])) {
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
        $bdd->upload_new_files($id_form, $link);
    }
}

if (isset($_POST['update-informations'])) {
    $new_title = $_POST['new-title'];
    $new_text = $_POST['new-text'];
    
    $data = array(
        $new_title,
        $new_text,
    );

    $bdd->update_info_form($data, $id_form);
}

if(isset($_POST['inscription'])){
    if(isset($_POST['id'])){
        $bdd->sub_users($_POST['id'], $id_form);
    }else {
        exit();
    }
}

if(isset($_POST['unsub'])){
    if(isset($_POST['id'])){
        $bdd->unsub($id_form, $_POST['id']);
    }else {
        exit();
    }
}

if(isset($_POST['suppression-formation'])){
    $bdd->delete_formation($id_form);
}



