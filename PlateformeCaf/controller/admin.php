<?php

require_once './model/bdd.php';
require_once './view/admin.php';

date_default_timezone_set('Europe/Paris');
$date = date('d-m-y H:i:s');
$title = "Administration";

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

$h = date('H');
if ($h > 6 && $h < 18) {
    $l = 'Bonjour';
} else {
    $l = 'Bonsoir';
}

$url = $_SESSION['url'];
$bdd = new Bdd();

if (isset($_POST['unlog'])) {
    $bdd->unlog();
}

if (isset($_POST['new-mate'])) {
    if (isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['firstname']) && isset($_POST['profil'])) {
        $bdd->registration($_POST['lastname'], $_POST['email'], $_POST['firstname'], $_POST['profil']);
    }
}

if (isset($_POST['suppression'])) {
    if (isset($_POST['id'])) {
        $bdd->delete($_POST['id']);
        echo '<meta http-equiv="refresh" content="10">';
    }
}

// if (isset($_POST['modifier'])) {
//     if (isset($_POST['id'])) {
//         $id = $_POST['id'][0];
//         header('Location: ./View/modal2.php?id=' . $id);
//         // $bdd-> update($id);
//     }
// }

// if (isset($_POST['modifierInfo'])) {
//     if (isset($_POST['new_data'])) {
//         $new_data = $_POST['new_data'];
//         if (isset($_SESSION['data'])) {
//             $data = $_SESSION['data'];
//             if ($data['email'] != $new_data[0] && isset($new_data[0])) {
//                 $data['email'] = $new_data[0];
//             } else if ($data['username'] != $new_data[1]) {
//                 $data['username'] = $new_data[1];
//             } else if ($data['pswd'] != $new_data[2] && isset($new_data[2])) {
//                 $data['pswd'] = $new_data[2];
//             }
//             // $bdd->update_trt($data);
//         }
//     }
// }

if (isset($_GET['action'])) {
    $_SESSION['user']['state'] = False;
    session_destroy();
    echo '<meta http-equiv="refresh" content="1; url=' . $url . '">';
}

// if(isset($_POST['modifier'])){
//     if(isset($_POST['id'])){
//         echo '<meta http-equiv="refresh" content="1; id=' . $url . '/function/modifier-admin.php?id='.$_POST['id'].'">';
//     }
// }
