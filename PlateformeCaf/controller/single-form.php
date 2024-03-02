<?php

require_once './model/bdd.php';
require_once './view/single-form.php';

$title = "Formation || Ajout d'une formation";

$profile = $_SESSION['user']['profil'];

$bdd = new Bdd();

$id_form = $_GET['id-form'];

$data_form = $bdd->get_formation_data($id_form);

$title = $data_form['title'];
$data = $bdd->get_sub_info($_SESSION['user']['id'], $id_form);

if(isset($_POST['valider-form'])){
    $bdd->delete_sub($data['id_sub'], $data['id_student']);
}

if($data['state_sub'] === 'inscrit'){
    $bdd->update_sub_state($data['id_student'], $id_form, 'ouvert', $data['id_sub']);
}

if($data[1] == ''){
    echo '<span id=inscrit style=display:none; data-sub=non>non inscrit</span>';
}else {
    echo '<span id=inscrit style=display:none; data-sub=oui>inscrit</span>';
}

?>